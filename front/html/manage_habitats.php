<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérifier si l'utilisateur a accès à la gestion des habitats
if (!hasPermission($pdo, $_SESSION['user']['role'], 'habitats', 'can_read')) {
    header('Location: ../login.php');
    exit;
}

$habitats = [];
$error = '';

try {
    $query = $pdo->query("SELECT * FROM habitats");
    $habitats = $query->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

// Déterminer le bon dashboard
$dashboardLink = "login.php";
if (isset($_SESSION['user']['role'])) {
    switch ($_SESSION['user']['role']) {
        case 'admin':
            $dashboardLink = "admin_dashboard.php";
            break;
        case 'veterinarian':
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
    <title>🏞️ Gestion des Habitats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/manage_habitats.css">
</head>

<body>
    <!-- Header -->
    <header class="bg-dark text-white text-center py-4">
        <h1>🏞️ Gestion des Habitats</h1>
    </header>

    <main class="container mt-4">
        <!-- Boutons Retour & Ajouter -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="<?= $dashboardLink ?>" class="btn btn-secondary">⬅ Retour au Dashboard</a>
            <a href="add_habitat.php" class="btn btn-success">Ajouter un Habitat</a>
        </div>

        <!-- Message d'erreur -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Tableau des habitats -->
        <div class="table-responsive">
            <table class="table table-striped table-hover border rounded-3 shadow">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($habitats as $habitat): ?>
                        <tr>
                            <td><?= htmlspecialchars($habitat['id']) ?></td>
                            <td><?= htmlspecialchars($habitat['name']) ?></td>
                            <td><?= htmlspecialchars($habitat['description']) ?></td>
                            <td>
                                <a href="edit_habitat.php?id=<?= $habitat['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="delete_habitat.php?id=<?= $habitat['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet habitat ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>