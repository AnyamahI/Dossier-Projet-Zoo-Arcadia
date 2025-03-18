<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

/*var_dump($_SESSION['user']); // Vérifie les infos de l'utilisateur connecté
var_dump(hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_read'));
var_dump(hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_create'));
var_dump(hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_update'));
var_dump(hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_delete'));

exit; // Stoppe l'exécution ici pour voir les résultats*/

if (!hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_read')) {
    header('Location: ../login.php');
    exit;
}

$canEdit = hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_update');
$canDelete = hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_delete');
$canAdd = hasPermission($pdo, $_SESSION['user']['role'], 'services', 'can_create');

$services = [];
$error = '';

try {
    // Récupérer tous les services
    $query = $pdo->query("SELECT id, name, description FROM services");
    $services = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des services : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <!-- Déterminer le bon dashboard -->
    <?php
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
    <header class="bg-dark text-white text-center py-3">
        <h1>Gestion des Services</h1>
    </header>
    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="<?= $dashboardLink ?>" class="btn btn-secondary">⬅ Retour au Dashboard</a>
            <?php if ($canAdd): ?>
                <a href="add_services.php" class="btn btn-success">Ajouter un service</a>
            <?php endif; ?>
        </div>
        <!-- Message d'erreur -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Tableau des services -->
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= htmlspecialchars($service['id']) ?></td>
                        <td><?= htmlspecialchars($service['name']) ?></td>
                        <td><?= htmlspecialchars($service['description']) ?></td>
                        <td>
                            <?php if ($canEdit): ?>
                                <a href="/front/html/edit_services.php?id=<?= $service['id'] ?>" class="btn btn-warning">Modifier</a>
                            <?php endif; ?>
                            <?php if ($canDelete): ?>
                                <a href="/front/html/delete_services.php?id=<?= $service['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce service ?');">Supprimer</a>
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