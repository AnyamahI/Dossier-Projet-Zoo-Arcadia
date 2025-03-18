<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérification des permissions
if (!hasPermission($pdo, $_SESSION['user']['role'], 'users', 'can_read')) {
    header('Location: ../login.php');
    exit;
}

$canEdit = hasPermission($pdo, $_SESSION['user']['role'], 'users', 'can_update');
$canDelete = hasPermission($pdo, $_SESSION['user']['role'], 'users', 'can_delete');
$canAdd = hasPermission($pdo, $_SESSION['user']['role'], 'users', 'can_create');

$users = [];
$error = '';

try {
    // Requête sécurisée
    $query = $pdo->prepare("SELECT id, email, role FROM users ORDER BY id ASC");
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des utilisateurs.";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/manage_user.css">
</head>

<body>
    <?php
    // Déterminer le dashboard en fonction du rôle
    $dashboardLink = "login.php";
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
        <h1>Gestion des Utilisateurs</h1>
    </header>

    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="<?= $dashboardLink ?>" class="btn btn-secondary">Retour au Dashboard</a>
            <?php if ($canAdd): ?>
                <a href="add_user.php" class="btn btn-success">Ajouter un utilisateur</a>
            <?php endif; ?>
        </div>

        <!-- Affichage des erreurs -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Tableau des utilisateurs -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <?php if ($canEdit): ?>
                                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    <?php endif; ?>
                                    <?php if ($canDelete): ?>
                                        <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>