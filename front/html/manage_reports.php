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

$reports = [];
$error = '';

try {
    // Requête pour récupérer les rapports
    $query = $pdo->query("
        SELECT 
            vet_reports.id, 
            animals.name AS animal_name, 
            users.email AS vet_email, 
            vet_reports.state, 
            vet_reports.food, 
            vet_reports.weight, 
            vet_reports.visit_date 
        FROM 
            vet_reports
        JOIN animals ON vet_reports.animal_id = animals.id
        JOIN users ON vet_reports.vet_id = users.id
    ");
    $reports = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des rapports : " . $e->getMessage();
}

if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rapports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Déterminer le bon dashboard -->
    <?php
    $dashboardLink = "../login.php"; 

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
        <h1>Gestion des Rapports Vétérinaires</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="text-end mb-3">
            <div class="mb-3">
                <a href="<?= $dashboardLink ?>" class="btn btn-secondary">⬅ Retour au Dashboard</a>
            </div>
            <a href="/front/html/add_report.php" class="btn btn-success">Ajouter un Rapport</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Animal</th>
                        <th>Vétérinaire</th>
                        <th>État</th>
                        <th>Nourriture</th>
                        <th>Grammage</th>
                        <th>Date de Passage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['id']) ?></td>
                            <td><?= htmlspecialchars($report['animal_name']) ?></td>
                            <td><?= htmlspecialchars($report['vet_email']) ?></td>
                            <td><?= htmlspecialchars($report['state']) ?></td>
                            <td><?= htmlspecialchars($report['food']) ?></td>
                            <td><?= htmlspecialchars($report['weight']) ?> g</td>
                            <td><?= htmlspecialchars($report['visit_date']) ?></td>
                            <td>
                                <a href="/front/html/edit_report.php?id=<?= $report['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                                <a href="/front/html/delete_report.php?id=<?= $report['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">Supprimer</a>
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