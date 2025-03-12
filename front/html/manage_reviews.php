<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Validation d'un avis
if (isset($_GET['validate'])) {
    $id = (int) $_GET['validate'];
    $query = $pdo->prepare("UPDATE visitor_reviews SET is_validated = 1 WHERE id = :id");

    if ($query->execute([':id' => $id])) {
        $_SESSION['success'] = "Avis validé avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la validation de l'avis.";
    }

    header('Location: manage_reviews.php'); // 🔹 Évite le chargement infini
    exit;
}

// Suppression d'un avis
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $query = $pdo->prepare("DELETE FROM visitor_reviews WHERE id = :id");

    if ($query->execute([':id' => $id])) {
        $_SESSION['success'] = "Avis supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'avis.";
    }

    header('Location: manage_reviews.php'); // 🔹 Évite le chargement infini
    exit;
}

// Récupération des avis
$query = $pdo->query("SELECT * FROM visitor_reviews ORDER BY created_at DESC");
$reviews = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des avis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center">Gestion des Avis</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Pseudo</th>
                    <th>Avis</th>
                    <th>Validé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?= htmlspecialchars($review['id']) ?></td>
                        <td><?= htmlspecialchars($review['pseudo']) ?></td>
                        <td><?= htmlspecialchars($review['review']) ?></td>
                        <td><?= $review['is_validated'] ? '✅' : '❌' ?></td>
                        <td>
                            <?php if (!$review['is_validated']): ?>
                                <a href="?validate=<?= $review['id'] ?>" class="btn btn-success">Valider</a>
                            <?php endif; ?>
                            <a href="?delete=<?= $review['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet avis ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>