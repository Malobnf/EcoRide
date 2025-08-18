<?php
declare(strict_types=1);

require __DIR__ . '/db.php';
require __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;

$pdo = getPdo();
$client = new Client(getenv('MONGODB_URI'));
$db = $client->selectDatabase('ecoride');

$colTrajets = $db->selectCollection('trajets_public');
$colParts = $db->selectCollection('participants_by_trajet');
$colRes = $db->selectCollection('reservations_read');
$colAvis = $db-selectCollection('avis_index');

function dt($s): ?UTCDateTime {
    if (empty($s)) return null;
    $ts = is_numeric($s) ? (int)$s : strtotime((string)$s);
    if (!$ts) return null;
    return new UTCDateTime($ts * 1000);
}

function buildTrajetDoc(array $r): array {
  $doc = ['_id' => (int)($r['id'] ?? 0)];
  $doc['ville_depart'] = $r['ville_depart'] ?? null;
  $doc['ville_arrivee'] = $r['ville_arrivee'] ?? null;
  if (!empty($r['depart_at']))          $doc['depart_at'] = dt($r['depart_at']);
  elseif (!empty($r['date']) && !empty($r['heure'])) $doc['depart_at'] = dt($r['date'].' '.$r['heure']);
  if (isset($r['prix']))                $doc['prix'] = (int)$r['prix'];
  if (isset($r['places_disponibles']))  $doc['places_disponibles'] = (int)$r['places_disponibles'];

  if (isset($r['depart_lng'], $r['depart_lat'])) {
    $doc['geo_depart'] = ['type'=>'Point','coordinates'=>[(float)$r['depart_lng'], (float)$r['depart_lat']]];
  }
  if (isset($r['arrivee_lng'], $r['arrivee_lat'])) {
    $doc['geo_arrivee'] = ['type'=>'Point','coordinates'=>[(float)$r['arrivee_lng'], (float)$r['arrivee_lat']]];
  }
  return $doc;
}

// Charge un event batch
$events = $pdo->query("SELECT * FROM outbox_events WHERE processed_at IS NULL ORDER BY id LIMIT 200")
              ->fetchAll(PDO::FETCH_ASSOC);

foreach ($events as $e) {
  $type = $e['aggregate_type'];
  $id   = (int)$e['aggregate_id'];
  $evt  = $e['event_type'];
  $pl   = $e['payload'] ? json_decode($e['payload'], true) : [];

  try {
    switch ($type) {
      case 'trajet':
        if ($evt === 'deleted') {
          $colTrajets->deleteOne(['_id'=>$id]);
        } else {
          // si payload vide, recharge depuis SQL
          if (!$pl) {
            $stmt = $pdo->prepare("SELECT * FROM trajets WHERE id=?");
            $stmt->execute([$id]);
            $pl = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
          }
          if (!$pl) break;
          $doc = buildTrajetDoc($pl);
          if (!$doc['_id']) $doc['_id'] = $id;
          $colTrajets->updateOne(['_id'=>$doc['_id']], ['$set'=>$doc], ['upsert'=>true]);
        }
        break;

      case 'participant_list':
        // recalculer la vue participants depuis SQL
        $sql = "SELECT cp.user_id, u.nom, u.prenom, u.note
                FROM covoiturages_participants cp
                JOIN utilisateurs u ON u.id = cp.user_id
                WHERE cp.trajet_id = ?
                ORDER BY u.nom, u.prenom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $colParts->updateOne(['_id'=>$id],
          ['$set'=>['_id'=>$id,'participants'=>$participants]],
          ['upsert'=>true]
        );
        break;

      case 'reservation':
        // upsert simple par réservation (payload déjà prêt)
        if ($evt === 'deleted') {
          $colRes->deleteOne(['_id'=>$id]);
        } else {
          if (!isset($pl['_id'])) $pl['_id'] = $id;
          if (isset($pl['created_at'])) $pl['created_at'] = dt($pl['created_at']);
          $colRes->updateOne(['_id'=>$id], ['$set'=>$pl], ['upsert'=>true]);
        }
        break;

      case 'avis':
        if ($evt === 'deleted') {
          $colAvis->deleteOne(['_id'=>$id]);
        } else {
          if (!isset($pl['_id'])) $pl['_id'] = $id;
          if (isset($pl['created_at'])) $pl['created_at'] = dt($pl['created_at']);
          $colAvis->updateOne(['_id'=>$id], ['$set'=>$pl], ['upsert'=>true]);
        }
        break;

    }

    $pdo->prepare("UPDATE outbox_events SET processed_at=NOW(), last_error=NULL WHERE id=?")
        ->execute([$e['id']]);
    echo "OK #{$e['id']} {$type}/{$evt}\n";

  } catch (Throwable $ex) {
    $pdo->prepare("UPDATE outbox_events SET attempts=attempts+1, last_error=? WHERE id=?")
        ->execute([substr($ex->getMessage(),0,1000), $e['id']]);
    fwrite(STDERR, "ERR #{$e['id']} {$type}/{$evt} : ".$ex->getMessage()."\n");
  }
}