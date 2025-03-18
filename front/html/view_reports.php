<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_animals.php'); // Rediriger si l'ID de l'animal n'est pas valide
    exit;
}

if (!isVeterinaire() && !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$animal_id = $_GET['id'];
$error = '';
$animal = null;
$reports = [];

try {
    // Récupérer les détails de l'animal
    $query = $pdo->prepare("SELECT * FROM animals WHERE id = :id");
    $query->execute([':id' => $animal_id]);
    $animal = $query->fetch(PDO::FETCH_ASSOC);

    if (!$animal) {
        $error = "Animal introuvable.";
    } else {
        // Récupérer les rapports vétérinaires associés
        $reportQuery = $pdo->prepare("
            SELECT vr.*, u.name AS vet_name 
            FROM vet_reports vr
            JOIN users u ON vr.vet_id = u.id
            WHERE vr.animal_id = :animal_id
            ORDER BY vr.visit_date DESC
        ");
        $reportQuery->execute([':animal_id' => $animal_id]);
        $reports = $reportQuery->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Détails de l'Animal</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <!-- Détails de l'animal -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($animal['name']) ?></h2>
                    <p><strong>Espèce :</strong> <?= htmlspecialchars($animal['species']) ?></p>
                    <p><strong>Description :</strong> <?= htmlspecialchars($animal['description']) ?></p>
                    <p><strong>Habitat :</strong> <?= htmlspecialchars($animal['habitat']) ?></p>
                </div>
            </div>

            <!-- Rapports vétérinaires -->
            <h3>Rapports vétérinaires</h3>
            <?php if (empty($reports)): ?>
                <p class="text-muted">Aucun rapport vétérinaire disponible pour cet animal.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Date de passage</th>
                                <th>État</th>
                                <th>Nourriture</th>
                                <th>Grammage</th>
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
                                    <td><?= htmlspecialchars($report['vet_name']) ?></td>
                                    <td><?= htmlspecialchars($report['details'] ?? 'Aucun détail') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <a href="/front/html/admin_dashboard.php" class="btn btn-secondary mt-3">Retour</a>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>