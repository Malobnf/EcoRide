<?php
declare(strict_types=1);

require __DIR__ . '/session_boot.php';

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
$pdo = getPdo();

// Vérifier que l'utilisateur est connecté
if (empty($_SESSION['utilisateur_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour proposer un trajet.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = (int) $_SESSION['utilisateur_id'];

// Vérifier les champs obligatoires
$requiredFields = ['depart', 'arrivee', 'date', 'heure', 'prix', 'passagers', 'voiture'];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Champ manquant : $field"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Récupération + nettoyage des données
$depart = trim((string) $_POST['depart']);
$arrivee = trim((string) $_POST['arrivee']);
$date = (string) $_POST['date'];  
$heure = (string) $_POST['heure']; 
$prix = (int) $_POST['prix'];
$places = (int) $_POST['passagers'];
$type = trim((string) $_POST['voiture']);

if ($prix < 2) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Le prix doit être au minimum de 2 crédits.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($places < 1) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Le nombre de passagers doit être au moins de 1.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


try {
    $pdo->beginTransaction();

    // Insertion du trajet en MySQL
    $sql = "
        INSERT INTO trajets (
            conducteur_id,
            ville_depart,
            ville_arrivee,
            date_trajet,
            heure_depart,
            prix,
            places_disponibles,
            type_voiture,
            etat
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'à venir')
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $userId,
        $depart,
        $arrivee,
        $date,
        $heure,
        $prix,
        $places,
        $type
    ]);

    $trajetId = (int) $pdo->lastInsertId();

    // Récupérer le nom du conducteur pour Mongo
    $conducteurNom = null;
    $stmtUser = $pdo->prepare('SELECT nom FROM utilisateurs WHERE id = ?');
    $stmtUser->execute([$userId]);
    $conducteurNom = $stmtUser->fetchColumn() ?: null;

    $pdo->commit();

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "Erreur d'enregistrement du trajet.",
        'debug'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (class_exists('MongoDB\\Client') && getenv('MONGODB_URI')) {
    try {
        require_once __DIR__ . '/mongo.php';
        if (isset($trajetsRMCol)) {
            $trajetsRMCol->replaceOne(
                ['_id' => $trajetId],
                [
                    '_id'                => $trajetId,
                    'conducteur_id'      => $userId,
                    'conducteur_nom'     => $conducteurNom,
                    'depart'             => $depart,
                    'arrivee'            => $arrivee,
                    'prix'               => (float) $prix,
                    'places_disponibles' => (int) $places,
                    'reservations_count' => 0,
                    'places_restantes'   => (int) $places,
                    'date_trajet'        => $date,
                    'heure_depart'       => $heure,
                    'updatedAt'          => new MongoDB\BSON\UTCDateTime(),
                ],
                ['upsert' => true]
            );
        }
    } catch (Throwable $e) {
        error_log('[creer-trajet] Erreur MongoDB : ' . $e->getMessage());
    }
}

// Réponse JSON finale
echo json_encode([
    'success'   => true,
    'message'   => 'Trajet créé avec succès.',
    'trajet_id' => $trajetId
], JSON_UNESCAPED_UNICODE);
exit;
