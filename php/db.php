<?php
function getPdo(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // ini_set('display_errors', '1');
    // ini_set('display_startup_errors', '1');
    // error_reporting(E_ALL);

    $jawsUrl = getenv('JAWSDB_URL');
    if ($jawsUrl) {                                     // lecture des infos de connexion depuis heroku (jawsdb)
        $parts  = parse_url($jawsUrl);
        $host   = $parts['host'] ?? 'localhost';
        $user   = $parts['user'] ?? '';
        $pass   = $parts['pass'] ?? '';
        $dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
        $port   = $parts['port'] ?? 3306;

    } else {                                            // lecture des variables locales si pas sur heroku (docker)
        $host   = getenv('DB_HOST') ?: 'db';
        $port   = (int)(getenv('DB_PORT') ?: 3306);
        $dbname = getenv('DB_NAME') ?: 'ecoride';
        $user   = getenv('DB_USER') ?: 'ecoride';
        $pass   = getenv('DB_PASS') ?: 'ecoride';
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";              // Consugnes de connexion à mysql pour PDO

    try {
        $pdo = new PDO($dsn, $user, $pass, [                                // Connexion BDD
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {                                             // Message si erreur
        throw new RuntimeException('Erreur de connexion BDD : ' . $e->getMessage());
    }

    return $pdo;                                                            // PDO statique : appel une seul fois à la BDD même si la fonction est appelée plusieurs fois
}
