<?php
require '../../lib/pdo.php';

$query = $pdo->query("SELECT pseudo, review FROM visitor_reviews WHERE is_validated = 1 ORDER BY created_at DESC");
$reviews = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis des Visiteurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Avis des Visiteurs</h1>
        <div class="row">
            <?php foreach ($reviews as $review): ?>
                <div class="col-md-6 mb-3">
                    <div class="card p-3">
                        <h5><?= htmlspecialchars($review['pseudo']) ?></h5>
                        <p><?= htmlspecialchars($review['review']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
