<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_read')) {
    header('Location: ../login.php');
    exit;
}

$canEdit = hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_update');
$canDelete = hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_delete');
$canAdd = hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_create');

// Récupération des animaux
$animals = [];
$error = '';

try {
    $query = $pdo->query("
        SELECT a.id, a.name, a.species, h.name AS habitat 
        FROM animals a 
        JOIN habitats h ON a.habitat_id = h.id
    ");
    $animals = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des animaux : " . $e->getMessage();
}

// Déterminer le bon dashboard
$dashboardLink = "../login.php"; // Redirection par défaut

if (isset($_SESSION['user']['role'])) {
    switch ($_SESSION['user']['role']) {
        case 'admin':
            $dashboardLink = "admin_dashboard.php";
            break;
        case 'veterinaire':
            $dashboardLink = "veterinaire_dashboard.php";
            break;
        case 'employee':
            $dashboardLink = "employe_dashboard.php";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Animaux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Gestion des Animaux</h1>
    </header>

    <main class="container mt-4">
        <!-- Boutons Retour et Ajouter alignés sur la même ligne -->
        <div class="d-flex justify-content-between mb-3">
            <a href="<?= $dashboardLink ?>" class="btn btn-secondary">Retour au Dashboard</a>
            <?php if ($canAdd): ?>
                <a href="/front/html/add_animal.php" class="btn btn-success">Ajouter un Animal</a>
            <?php endif; ?>
        </div>

        <!-- Affichage des erreurs -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Tableau des animaux -->
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Espèce</th>
                    <th>Habitat</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animals as $animal): ?>
                    <tr>
                        <td><?= htmlspecialchars($animal['id']) ?></td>
                        <td><?= htmlspecialchars($animal['name']) ?></td>
                        <td><?= htmlspecialchars($animal['species']) ?></td>
                        <td><?= htmlspecialchars($animal['habitat']) ?></td>
                        <td>
                            <a href="view_animal.php?id=<?= $animal['id'] ?>" class="btn btn-info btn-sm">Voir</a>
                            <?php if ($canEdit): ?>
                                <a href="edit_animal.php?id=<?= $animal['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <?php endif; ?>
                            <?php if ($canDelete): ?>
                                <a href="delete_animal.php?id=<?= $animal['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet animal ?');">Supprimer</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>