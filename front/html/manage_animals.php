<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!hasPermission($pdo, $_SESSION['user']['role'], 'species', 'can_read')) {
    header('Location: ../login.php');
    exit;
}

$canEdit = hasPermission($pdo, $_SESSION['user']['role'], 'species', 'can_update');
$canDelete = hasPermission($pdo, $_SESSION['user']['role'], 'species', 'can_delete');
$canAdd = hasPermission($pdo, $_SESSION['user']['role'], 'species', 'can_create');

$speciesList = [];
$error = '';

try {
    $query = $pdo->query("SELECT id, name, scientific_name FROM species");
    $speciesList = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des espèces : " . $e->getMessage();
}

// Déterminer le bon dashboard
$dashboardLink = "login.php"; // Redirection par défaut

if (isset($_SESSION['user']['role'])) {
    switch ($_SESSION['user']['role']) {
        case 'admin':
            $dashboardLink = "admin_dashboard.php";
            break;
        case 'veterinaire':
            $dashboardLink = "veterinaire_dashboard.php";
            break;
        case 'employee':
            $dashboardLink = "employee_dashboard.php";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Espèces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Gestion des Espèces</h1>
    </header>

    <main class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <a href="<?= $dashboardLink ?>" class="btn btn-secondary">Retour au Dashboard</a>
        </div>

        <?php if ($canAdd): ?>
            <div class="d-flex justify-content-center gap-3 mb-4">
                <a href="/front/html/add_species.php" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-lg"></i> Ajouter une Espèce
                </a>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom Commun</th>
                    <th>Nom Scientifique</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($speciesList as $species): ?>
                    <tr>
                        <td><?= htmlspecialchars($species['id']) ?></td>
                        <td><?= htmlspecialchars($species['name']) ?></td>
                        <td><?= htmlspecialchars($species['scientific_name'] ?? 'Non renseigné') ?></td>
                        <td>
                            <?php if ($canEdit): ?>
                                <a href="/front/html/edit_species.php?id=<?= $species['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <?php endif; ?>
                            <?php if ($canDelete): ?>
                                <a href="/front/html/delete_species.php?id=<?= $species['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette espèce ?');">Supprimer</a>
                            <?php endif; ?>
                            <a href="create_animals.php?species_id=<?= $species['id'] ?>" class="btn btn-success btn-sm">
                                Ajouter un <?= htmlspecialchars($species['name']) ?>
                            </a>
                            <a href="list_animals.php?species_id=<?= $species['id'] ?>" class="btn btn-info btn-sm">
                                Voir les <?= htmlspecialchars($species['name']) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>