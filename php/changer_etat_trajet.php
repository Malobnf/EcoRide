<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non-connecté']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$trajetId = $data['id'] ?? null;
$etat = $data['etat'] ?? null;

if (!$trajetId || !$etat) {
  echo json_encode(['success' => false, 'message' => "Paramètres manquants"]);
  exit;
}

try {

  // Maj de l'état du trajet
  $update = $pdo->prepare("UPDATE trajets SET etat = :etat WHERE id = :id");
  $update->execute([':etat' => $etat, ':id' => $trajetId]);

  // Envoi d'un mail si etat = terminé
  if ($etat === 'terminé') {
    
    // Récupération info conducteur
    $stmt = $pdo->prepare("
      SELECT u.id, u.nom, u.email
      FROM trajets t
      JOIN utilisateurs u ON t.conducteur_id = u.id
      WHERE t.id = :id
      ");

    $stmt->execute([':id' => $trajetId]);
    $conducteur = $stmt->fetch(PDO::FETCH_ASSOC);

  // Récupération info passager(s)
  $stmt2 = $pdo->prepare("
    SELECT u.id, u.nom, u.email
    FROM reservations r
    JOIN utilisateurs u ON r.utilisateur_id = u.id
    WHERE r.trajet_id = :id
  ");

    $stmt2->execute([':id' => $trajetId]);
    $passagers = $stmt2->fetchAll(PDO::FETCH_ASSOC);

  // Envoi des mails quand trajet = terminé

  // Mails passagers
  foreach ($passagers as $p) {
    $to = $p['email'];
    $subject = "Évaluez votre conducteur EcoRide !";
    $message = "Bonjour {$p['nom']},\n\nMerci d’avoir voyagé avec Ecoride !\n\nVeuillez noter votre conducteur {$conducteur['nom']} :\nLien : http://localhost/ecoride/index.php?page=avis.php?trajet=$trajetId&vers=conducteur\n\nÀ bientôt sur Ecoride !";
    mail($to, $subject, $message, "From: noreply@ecoride.com");
  }
  
  // Mails conducteur
  foreach ($passagers as $p) {
    $to = $conducteur['email'];
    $subject = "Évaluez vos passagers EcoRide !";
    $message = "Bonjour {$conducteur['nom']},\n\nMerci pour ce trajet !\n\nVeuillez noter vos passagers :\nLien : http://localhost/ecoride/index.php?page=avis.php?trajet=$trajetId&vers=conducteur\n\nÀ bientôt sur Ecoride !";
    mail($to, $subject, $message, "From: noreply@ecoride.com"); 
  }
}

echo json_encode(['success' => true]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}