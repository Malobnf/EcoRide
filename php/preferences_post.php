<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/mongo.php';

if (empty($_SESSION['utilisateur_id'])) {
  echo json_encode(['success'=>false,'message'=>'Non connecté']); exit;
}

$prefs = $_POST['preferences'] ?? [];
$prefs = array_values(array_unique(array_map('strval', (array)$prefs)));

$userProfilesCol->updateOne(
  ['_id' => (int)$_SESSION['utilisateur_id']],
  ['$set' => [
    'preferences' => $prefs,
    'updatedAt'   => new MongoDB\BSON\UTCDateTime()
  ]],
  ['upsert' => true]
);

echo json_encode(['success'=>true]);
