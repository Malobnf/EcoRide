<?php
declare(strict_types=1);

// Charger l'autoloader
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
  require_once $autoload;
}

$mongoAvailable = false;

if (class_exists('MongoDB\\Client')) {
  $uri = getenv('MONGODB_URI') ?: '';
  if ($uri) {
    try {
      $client = new MongoDB\Client($uri, [], ['typeMap' => ['root'=>'array','document'=>'array']]);
      $db = $client->selectDatabase('ecoride');
      $userProfilesCol = $db->selectCollection('user_profiles');
      $trajetsRMCol    = $db->selectCollection('trajets_readmodel');
      $alertsCol       = $db->selectCollection('alertes_places');
      $outboxCol       = $db->selectCollection('outbox_events');
      $mongoAvailable = true;
    } catch (Throwable $e) {
      error_log('[mongo.php] '.$e->getMessage());
    }
  }
}
