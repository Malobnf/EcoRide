<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

try {
    if (empty($_SESSION['utilisateur_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Non connecté'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPdo();

    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $plaque = trim($_POST['plaque'] ?? '');
    $anneeImmat = trim($_POST['annee_immat'] ?? '');
    $couleur = trim($_POST['couleur'] ?? '');
    $places = trim($_POST['places'] ?? '');
    $propulsion = trim($_POST['propulsion'] ?? '');

    if ($marque === '' || $modele === '' || $plaque === '' || $anneeImmat === '' || $couleur === '' || $places === '' || $propulsion === '') {
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'message' => 'Tous les champs sont obligatoires.'
      ], JSON_UNESCAPED_UNICODE);
      exit;
    }

    $annee = (int)$anneeImmat;
    if ($annee < 1960 || $annee > 2025) {
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'message' => "Année invalide."
      ]);
    exit;
  }

    $placesInt = (int) $places;
    if ($placesInt <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Le nombre de places doit être un entier positif.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sql = "
        INSERT INTO vehicules (utilisateur_id, marque, modele, plaque, annee_immat, couleur, places, propulsion)
        VALUES (:uid, :marque, :modele, :plaque, :annee_immat, :couleur, :places, :propulsion)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':uid' => (int) $_SESSION['utilisateur_id'],
      ':marque' => $marque,
      ':modele' => $modele,
      ':plaque' => $plaque,
      ':annee_immat' => $annee,
      ':couleur' => $couleur,
      ':places' => $placesInt,
      ':propulsion' => $propulsion
    ]);

    echo json_encode([
      'success' => true,
      'message' => 'Véhicule ajouté avec succès.'
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur lors de l’ajout du véhicule.',
        'debug'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
