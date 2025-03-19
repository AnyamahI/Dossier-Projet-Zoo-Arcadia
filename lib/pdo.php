<?php

$clefHeroku = getenv('JAWSDB_URL') ?: die("Erreur : Impossible de récupérer l'URL de la base de données.");
var_dump($clefHeroku);
die();

$dbparts = parse_url($clefHeroku);

$host = $dbparts['host'];
$username = $dbparts['user'];
$password = $dbparts['pass'];
$database = ltrim($dbparts['path'], '/');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
