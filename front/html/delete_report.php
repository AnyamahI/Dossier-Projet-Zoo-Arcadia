<?php
session_start();
require_once __DIR__ . '/../../lib/session.php';
require_once __DIR__ . '/../../lib/pdo.php';

if (!isVeterinaire()) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_reports.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    $query = $pdo->prepare("DELETE FROM vet_reports WHERE id = :id");
    $query->execute([':id' => $id]);

    $_SESSION['success'] = "Rapport supprimé avec succès.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la suppression du rapport : " . $e->getMessage();
}

header('Location: manage_reports.php');
exit;
