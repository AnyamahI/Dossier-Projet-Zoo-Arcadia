<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérifier si l'utilisateur a le droit de supprimer un animal
if (!hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_delete')) {
    header('Location: manage_animals.php');
    exit;
}

// Vérifier si un ID d'animal est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID d'animal invalide.";
    header('Location: manage_animals.php');
    exit;
}

$animal_id = intval($_GET['id']);

try {
    // Récupérer l'animal avant suppression (pour récupérer son espèce et rediriger ensuite)
    $query = $pdo->prepare("SELECT species_id FROM animals WHERE id = :id");
    $query->execute([':id' => $animal_id]);
    $animal = $query->fetch(PDO::FETCH_ASSOC);

    if (!$animal) {
        $_SESSION['error'] = "Animal introuvable.";
        header('Location: manage_animals.php');
        exit;
    }

    $species_id = $animal['species_id']; // Pour la redirection après suppression

    // Supprimer l'animal
    $query = $pdo->prepare("DELETE FROM animals WHERE id = :id");
    $query->execute([':id' => $animal_id]);

    $_SESSION['success'] = "✅ Animal supprimé avec succès.";
    header("Location: list_animals.php?species_id=" . $species_id);
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "❌ Erreur lors de la suppression : " . $e->getMessage();
    header("Location: list_animals.php?species_id=" . $species_id);
    exit;
}
?>
