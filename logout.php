<?php
session_start(); // Assure que la session est bien démarrée

// Supprime toutes les variables de session
$_SESSION = [];

// Détruit la session
session_destroy();

// Redirige vers la page de connexion
header('Location: login.php');
exit;
