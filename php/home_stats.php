<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
$pdo = getPdo();

/**
 * Hypothèses métiers (ces nombres sont fictifs et ne reflètent pas la réalité) :
 * - Date de lancement du site : 2025-01-01
 * - Coût moyen d'un trajet en solo : 50 € / personne
 * - Occupation moyenne d'un covoiturage : 4 personnes (conducteur inclus)
 * - Économie de CO₂ : on suppose qu'un covoiturage évite 3 trajets "solo"
 * - CO₂ moyen par trajet : 15 kg
 */
const SITE_SINCE_DATE = '2025-01-01';
const AVG_TRIP_COST_EUR = 50.0;
const AVG_OCCUPANCY     = 4.0;
const CO2_PER_TRIP_KG   = 15.0;

try {
  // Nombre de covoiturages depuis le 01/01/2025
  $stTrips = $pdo->prepare("SELECT COUNT(*) FROM trajets WHERE date_trajet >= ?");
  $stTrips->execute([SITE_SINCE_DATE]);
  $totalTrips = (int)$stTrips->fetchColumn();

  // CO₂ économisé ≈ nb_trajets * (3 trajets évités) * CO₂ moyen par trajet
  $co2SavedKg = $totalTrips * 3 * CO2_PER_TRIP_KG;

  // Argent économisé par utilisateur (moyenne)
  // On estime les "participations" = passagers (reservations) + conducteurs (1 par trajet)
  $stRes = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reservations r 
    INNER JOIN trajets t ON t.id = r.trajet_id 
    WHERE t.date_trajet >= ?
  ");
  $stRes->execute([SITE_SINCE_DATE]);
  $passengerCount = (int)$stRes->fetchColumn();
  $driverCount = $totalTrips;
  $participations = $passengerCount + $driverCount;

  // Économie par personne et par covoiturage : 50€ (solo) - 50€/4 (partagé) = 37.5 €
  $savingPerParticipation = AVG_TRIP_COST_EUR * (1.0 - 1.0/AVG_OCCUPANCY); // 37.5 €
  $totalSavedAllUsers = $participations * $savingPerParticipation;

  // On moyenne par utilisateur inscrit (hors admin)
  $stUsers = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role <> 'admin'");
  $userCount = max(1, (int)$stUsers->fetchColumn()); // évite division par 0
  $savedPerUser = $totalSavedAllUsers / $userCount;

  echo json_encode([
    'success' => true,
    'since'   => SITE_SINCE_DATE,
    'trips'   => $totalTrips,
    'co2SavedKg' => $co2SavedKg,
    'money' => [
      'perUserEur' => $savedPerUser,
      'perParticipationEur' => $savingPerParticipation,
    ],
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false, 'message'=>'Erreur BDD: '.$e->getMessage()]);
}
