<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérification des permissions
if (!hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_read')) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$animals = [];
$species_name = '';

// Vérifier si un ID d'espèce est fourni
if (!isset($_GET['species_id']) || !is_numeric($_GET['species_id'])) {
    $error = "ID d'espèce invalide.";
} else {
    $species_id = intval($_GET['species_id']);

    // Récupérer le nom de l'espèce
    try {
        $query = $pdo->prepare("SELECT name FROM species WHERE id = :id");
        $query->execute([':id' => $species_id]);
        $species = $query->fetch(PDO::FETCH_ASSOC);

        if (!$species) {
            $error = "Espèce introuvable.";
        } else {
            $species_name = $species['name'];
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération de l'espèce : " . $e->getMessage();
    }

    // Récupérer les animaux associés à cette espèce
    if (empty($error)) {
        try {
            $query = $pdo->prepare("SELECT * FROM animals WHERE species_id = :species_id ORDER BY name ASC");
            $query->execute([':species_id' => $species_id]);
            $animals = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Erreur lors de la récupération des animaux : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des <?= htmlspecialchars($species_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Liste des <?= htmlspecialchars($species_name) ?></h1>
    </header>

    <main class="container mt-4">
        <div class="mb-3">
            <a href="manage_animals.php" class="btn btn-secondary">Retour</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Habitat</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($animals)): ?>
                        <?php foreach ($animals as $animal): ?>
                            <tr>
                                <td><?= htmlspecialchars($animal['id']) ?></td>
                                <td><?= htmlspecialchars($animal['name']) ?></td>
                                <td><?= htmlspecialchars($animal['habitat_id'] ?? 'Non défini') ?></td>
                                <td>
                                    <?php if (!empty($animal['image'])): ?>
                                        <img src="<?= htmlspecialchars($animal['image']) ?>" alt="<?= htmlspecialchars($animal['name']) ?>" style="width: 50px; height: auto;">
                                    <?php else: ?>
                                        <span class="text-muted">Aucune image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_animal.php?id=<?= $animal['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    <a href="delete_animal.php?id=<?= $animal['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet animal ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucun <?= htmlspecialchars($species_name) ?> trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
