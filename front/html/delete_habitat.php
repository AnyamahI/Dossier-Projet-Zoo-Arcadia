<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $query = $pdo->prepare("DELETE FROM habitats WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de l'habitat : " . $e->getMessage();
    }
}

header('Location: manage_habitats.php');
exit;
