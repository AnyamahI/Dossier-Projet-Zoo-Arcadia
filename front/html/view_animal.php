<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérifier si l'utilisateur peut voir les animaux
if (!hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_read')) {
    header('Location: ../login.php');
    exit;
}

// Vérifier si un ID est passé
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_animals.php');
    exit;
}

$animal = null;
$reports = [];
$error = '';

// Récupérer les infos de l'animal
try {
    $query = $pdo->prepare("
        SELECT a.*, h.name AS habitat 
        FROM animals a 
        JOIN habitats h ON a.habitat_id = h.id
        WHERE a.id = :id
    ");
    $query->execute([':id' => $_GET['id']]);
    $animal = $query->fetch(PDO::FETCH_ASSOC);

    if (!$animal) {
        $error = "Animal introuvable.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération de l'animal : " . $e->getMessage();
}

// Récupérer les derniers rapports vétérinaires
try {
    $query = $pdo->prepare("
        SELECT v.state, v.food, v.weight, v.visit_date, v.details, u.email AS vet_email
        FROM vet_reports v
        JOIN users u ON v.vet_id = u.id
        WHERE v.animal_id = :id
        ORDER BY v.visit_date DESC
    ");
    $query->execute([':id' => $_GET['id']]);
    $reports = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des rapports vétérinaires : " . $e->getMessage();
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
    <title>🐾 Détails de l'animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>🐾 Détails de l'Animal</h1>
    </header>

    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="<?= $dashboardLink ?>" class="btn btn-secondary">⬅ Retour au Dashboard</a>
            <a href="manage_animals.php" class="btn btn-primary">🔙 Retour à la liste</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <div class="card shadow-lg p-4">
                <h2 class="text-center"><?= htmlspecialchars($animal['name']) ?> (<?= htmlspecialchars($animal['species']) ?>)</h2>
                <p><strong>🌍 Habitat :</strong> <?= htmlspecialchars($animal['habitat']) ?></p>
                <p><strong>🍽️ Nourriture :</strong> <?= htmlspecialchars($animal['food'] ?? 'Non spécifié') ?></p>
                <p><strong>🗓️ Dernier contrôle :</strong> <?= $animal['last_checkup_date'] ?? 'Non renseigné' ?></p>
            </div>

            <h3 class="mt-5">📋 Rapports vétérinaires</h3>
            <?php if (empty($reports)): ?>
                <p>Aucun rapport disponible pour cet animal.</p>
            <?php else: ?>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>État</th>
                            <th>Nourriture</th>
                            <th>Poids</th>
                            <th>Vétérinaire</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= htmlspecialchars($report['visit_date']) ?></td>
                                <td><?= htmlspecialchars($report['state']) ?></td>
                                <td><?= htmlspecialchars($report['food']) ?></td>
                                <td><?= htmlspecialchars($report['weight']) ?> g</td>
                                <td><?= htmlspecialchars($report['vet_email']) ?></td>
                                <td><?= htmlspecialchars($report['details'] ?? 'Aucun détail') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($_SESSION['user']['role'] === 'veterinarian'): ?>
                <a href="add_report.php?animal_id=<?= $animal['id'] ?>" class="btn btn-success mt-3">➕ Ajouter un Rapport</a>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>