<?php

$url = getenv('JAWSDB_URL') ?: die("❌ Erreur : Impossible de récupérer l'URL de la base de données.");

$dbparts = parse_url($url);

$host = $dbparts['host'];
$dbname = ltrim($dbparts['path'], '/');
$username = $dbparts['user'];
$password = $dbparts['pass'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur PDO : " . $e->getMessage());
}
