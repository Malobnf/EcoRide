<?php
require_once __DIR__ . '/../vendor/autoload.php';
$mongoUri = getenv('MONGODB_URI') ?: '';
if (!$mongoUri) { throw new RuntimeException('MONGODB_URI manquant'); }
$mongo = new MongoDB\Client($mongoUri);
$db    = $mongo->selectDatabase('ecoride-studi-mb');
$userProfilesCol = $db->selectCollection('user_profiles');
$trajetsRMCol    = $db->selectCollection('trajets_readmodel');
$alertsCol       = $db->selectCollection('alertes_places');
$outboxCol       = $db->selectCollection('outbox_events');
