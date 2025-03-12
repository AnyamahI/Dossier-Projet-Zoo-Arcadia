<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';

// Vérifiez si un ID est fourni dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_animals.php');
    exit;
}

$id = intval($_GET['id']);

// Supprimer l'espèce de la base de données
try {
    $query = $pdo->prepare("DELETE FROM species WHERE id = :id");
    $query->execute([':id' => $id]);

    // Rediriger après suppression
    $_SESSION['success'] = "L'espèce a été supprimée avec succès.";
    header('Location: manage_animals.php');
    exit;
} catch (PDOException $e) {
    $error = "Erreur lors de la suppression de l'espèce : " . $e->getMessage();
    $_SESSION['error'] = $error;
    header('Location: manage_animals.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Supprimer une Espèce</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>