<?php
session_start();
header('Content-Type: application/json');
require 'db.php'; // Connexion PDO

// Fonction simple d'envoi de mail
function envoyer_mail($to, $subject, $message) {
    $headers = "From: no-reply@ecoride.fr\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return mail($to, $subject, $message, $headers);
}

$input = json_decode(file_get_contents('php://input'), true);
$reservationId = $input['reservation_id'] ?? null;
$idUtilisateur = $_SESSION['utilisateur_id'] ?? null;

if (!$reservationId || !$idUtilisateur) {
  echo json_encode(['success' => false, 'message' => "Données manquantes."]);
  exit;
}

try {
  $pdo->beginTransaction();

  // Récupérer la réservation avec verrou (FOR UPDATE)
  $stmt = $pdo->prepare("
    SELECT r.*, t.prix, t.conducteur_id, t.id AS trajet_id,
           u.email AS conducteur_email, u.nom AS conducteur_nom,
           t.ville_depart, t.ville_arrivee, t.date_trajet
    FROM reservations r
    JOIN trajets t ON r.trajet_id = t.id
    JOIN utilisateurs u ON t.conducteur_id = u.id
    WHERE r.id = :rid AND r.utilisateur_id = :uid
    FOR UPDATE
  ");

  $stmt->execute([':rid' => $reservationId, ':uid' => $idUtilisateur]);
  $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$reservation) {
    throw new Exception("Réservation introuvable.");
  }

  $prix = $reservation['prix'];
  $commission = 2;
  $gainConducteur = $prix - $commission;
  $trajetId = $reservation['trajet_id'];
  $conducteurId = $reservation['conducteur_id'];

  // Suppression de la réservation
  $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
  $stmt->execute([':id' => $reservationId]);

  // Ajouter une place au trajet
  $stmt = $pdo->prepare("UPDATE trajets SET places_disponibles = places_disponibles + 1 WHERE id = :id");
  $stmt->execute([':id' => $trajetId]);

  // Rendre les crédits à l'utilisateur
  $stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits + :montant WHERE id = :id");
  $stmt->execute([':montant' => $prix, ':id' => $idUtilisateur]);

  // Retirer les crédits au conducteur
  $stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits - :gain WHERE id = :id");
  $stmt->execute([':gain' => $gainConducteur, ':id' => $conducteurId]);

  // Retirer les crédits à l'ENT
  $stmt = $pdo->prepare("UPDATE entreprise_credits SET total_credits = total_credits - :commission WHERE id = 1");
  $stmt->execute([':commission' => $commission]);

  // Envoi du mail au conducteur
  $subject = "Annulation d'une réservation sur votre trajet EcoRide";
  $message = "Bonjour {$reservation['conducteur_nom']},\n\n"
           . "Le passager a annulé sa réservation pour votre trajet de {$reservation['ville_depart']} vers {$reservation['ville_arrivee']} prévu le {$reservation['date_trajet']}.\n\n"
           . "Cordialement,\nL'équipe EcoRide";

  envoyer_mail($reservation['conducteur_email'], $subject, $message);

  $pdo->commit();

  echo json_encode(['success' => true]);

} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
