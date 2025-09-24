<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/mongo.php';

// Filtres simples
$depart  = trim($_GET['depart']  ?? '');
$arrivee = trim($_GET['arrivee'] ?? '');
$dateMin = trim($_GET['date']    ?? ''); // yyyy-mm-dd

$filter = [];
if ($depart  !== '')  $filter['depart']  = $depart;
if ($arrivee !== '')  $filter['arrivee'] = $arrivee;
if ($dateMin !== '')  $filter['date_trajet'] = ['$gte' => $dateMin];

$cursor = $trajetsRMCol->find($filter, [
  'projection' => ['_id'=>1,'depart'=>1,'arrivee'=>1,'prix'=>1,'places_restantes'=>1,'date_trajet'=>1,'conducteur_id'=>1,'conducteur_nom'=>1],
  'sort' => ['date_trajet' => 1],
  'limit'=> 100
]);
$docs = iterator_to_array($cursor, false);

echo json_encode(['success'=>true,'source'=>'mongo','trajets'=>$docs], JSON_UNESCAPED_UNICODE);
