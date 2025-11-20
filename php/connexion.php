<?php
declare(strict_types=1);

// ⚠️ pendant le debug, laisse display_errors = 1
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

try {
    $pdo = getPdo();

    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Corps de requête vide']);
        exit;
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'JSON invalide', 'debug' => $raw]);
        exit;
    }

    $username = trim($payload['username'] ?? '');
    $password = $payload['password'] ?? '';

    if ($username === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs vides']);
        exit;
    }

    $sql = 'SELECT id, mot_de_passe, role FROM utilisateurs WHERE nom = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        !$user
        || !isset($user['mot_de_passe'])
        || !password_verify($password, $user['mot_de_passe'])
    ) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['utilisateur_id'] = (int) $user['id'];
    $_SESSION['role']           = (string) $user['role'];

    $redirect = ($user['role'] === 'admin')
        ? '/index.php?page=admin'
        : '/index.php?page=profil';

    echo json_encode([
        'success'  => true,
        'role'     => $user['role'],
        'redirect' => $redirect,
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur interne',
        'debug'   => $e->getMessage(), // tu pourras enlever debug ensuite
    ]);
    exit;
}
