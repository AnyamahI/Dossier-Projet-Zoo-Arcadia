<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isVeterinair() && !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_animals.php');
    exit;
}

$id = $_GET['id'];

try {
    $query = $pdo->prepare("DELETE FROM animals WHERE id = :id");
    $query->execute([':id' => $id]);

    header('Location: manage_animals.php');
    exit;
} catch (PDOException $e) {
    die("Erreur lors de la suppression de l'animal : " . $e->getMessage());
}
