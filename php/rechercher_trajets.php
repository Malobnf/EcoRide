<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
$pdo = getPdo();

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$depart   = trim($data['depart']  ?? '');
$arrivee  = trim($data['arrivee'] ?? '');
$date     = trim($data['date']    ?? '');
$propulsion = trim($data['voiture'] ?? '');

if ($depart === '' || $arrivee === '' || $date === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Champs manquants'
    ]);
    exit;
}

try {
    $sql = "
        SELECT 
          t.id,
          t.ville_depart,
          t.ville_arrivee,
          t.date_trajet,
          t.heure_depart,
          t.prix,
          t.places_disponibles,
          u.nom AS conducteur,
          v.propulsion
        FROM trajets t
        JOIN utilisateurs u ON t.conducteur_id = u.id
        LEFT JOIN vehicules v ON v.utilisateur_id = u.id
        WHERE 
          t.ville_depart = :depart
          AND t.ville_arrivee = :arrivee
          AND t.date_trajet = :date_trajet
    ";

    $params = [
        ':depart'      => $depart,
        ':arrivee'     => $arrivee,
        ':date_trajet' => $date,
    ];

    if ($propulsion !== '') {
        $sql .= " AND v.propulsion = :propulsion";
        $params[':propulsion'] = $propulsion;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'trajets' => $trajets
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
}
