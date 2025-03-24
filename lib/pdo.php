<?php

// Récupérer l'URL de la base de données depuis Heroku
$clefHeroku = getenv('JAWSDB_URL');

if (!$clefHeroku) {
    die("Erreur : Impossible de récupérer l'URL de la base de données.");
}

// Debugging : Vérifier si l'URL est récupérée
error_log("URL MySQL récupérée : " . $clefHeroku);

// S'assurer que l'URL est bien parsée
$dbparts = parse_url($clefHeroku);
if (!$dbparts || !isset($dbparts['host'], $dbparts['user'], $dbparts['pass'], $dbparts['path'])) {
    die("Erreur : URL MySQL mal formatée.");
}

// Extraire les infos
$host = $dbparts['host'];
$username = $dbparts['user'];
$password = $dbparts['pass'];
$database = isset($dbparts['path']) ? ltrim($dbparts['path'], '/') : '';

if (!$host || !$username || !$password || !$database) {
    die("Erreur : Problème avec les informations de la base de données.");
}

try {
    // Connexion à MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    error_log("Connexion à la base de données réussie !");
} catch (PDOException $e) {
    error_log("Erreur de connexion MySQL : " . $e->getMessage());
    die("Erreur de connexion à la base de données.");
}

return $pdo;
