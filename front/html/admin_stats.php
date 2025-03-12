<?php
session_start();
require_once __DIR__ . '/../../lib/session.php';
require_once __DIR__ . '/../../lib/pdo.php';
require_once __DIR__ . '/../../lib/redis.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$animalViews = [];

// Récupérer toutes les clés Redis qui contiennent les stats des animaux
$keys = $redis->keys("visits:animal:*");

foreach ($keys as $key) {
    $animal_id = str_replace("visits:animal:", "", $key);
    $views = $redis->get($key);

    // Récupérer le nom de l'animal depuis la base de données
    $query = $pdo->prepare("SELECT name FROM animals WHERE id = :id");
    $query->execute([':id' => $animal_id]);
    $animal = $query->fetch(PDO::FETCH_ASSOC);

    if ($animal) {
        $animalViews[] = [
            'name' => $animal['name'],
            'views' => (int) $views
        ];
    }
}

// Trier les animaux par nombre de vues (du plus vu au moins vu)
usort($animalViews, fn($a, $b) => $b['views'] - $a['views']);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Animaux</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="container mt-5">
    <h1 class="text-center">📊 Statistiques des Animaux</h1>

    <?php if (!empty($animalViews)): ?>
        <!-- TABLEAU -->
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>Animal</th>
                    <th>Nombre de consultations</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animalViews as $animal): ?>
                    <tr>
                        <td><?= htmlspecialchars($animal['name']) ?></td>
                        <td><?= htmlspecialchars($animal['views']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- GRAPHIQUE -->
        <div class="mt-5">
            <canvas id="animalChart"></canvas>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let ctx = document.getElementById('animalChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_column($animalViews, 'name')) ?>,
                        datasets: [{
                            label: 'Nombre de consultations',
                            data: <?= json_encode(array_column($animalViews, 'views')) ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            });
        </script>

    <?php else: ?>
        <p class="text-muted text-center">Aucune donnée disponible.</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Retour</a>
</body>

</html>
