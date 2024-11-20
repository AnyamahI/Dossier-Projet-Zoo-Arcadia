<?php
$host = 'localhost';
$dbname = 'arcadia.'; // Nom de la base de données
$username = 'root';
$password = ''; // Par défaut, laissez vide pour XAMPP et WAMP

$dsn = "mysql:host=$host;dbname=zoo_arcadia"; 
$options = [ 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES => false, 
];

try { 
    $pdo = new PDO($dsn, $user, $pass, $options); 
} catch (PDOException $e) {
     echo 'Erreur de connexion : ' . $e->getMessage(); exit; 
    }
?>