<?php
declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;

$client = new Client(getenv('MONGODB_URI'));
$db = $client->selectDatabase('ecoride');

// Trajets : texte, géolocalisation et date
$db->selectCollection('trajets_public')->createIndex(['ville_depart' => 'text', 'ville_arrivee' => 'text', 'description' => 'text']);
$db->selectCollection('trajets_public')->createIndex(['geo_depart' => '2sphere']);
$db->selectCollection('trajets_public')->createIndex(['depart_at' => 1]);

// Participants
$db->selectCollection('participants_par_trajet')->createIndex(['_id' => 1]);

// Réservations
$db->selectCollection('reserations_read')->createIndex(['user_id' => 1, 'created _at' => -1]);

// Avis

$db->selectCollection('avis_index')->createIndex(['texte' => 'text']);
$db->selectCollection('avis_index')->createIndex(['conducteur_id' => 1, 'note' => -1]);

echo "Indexes OK\n";