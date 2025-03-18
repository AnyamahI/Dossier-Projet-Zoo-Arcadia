<?php
require '../../lib/pdo.php'; // Connexion à la base de données
require __DIR__ . '/../../templates/header.html';

$speciess = [];

try {
    $query = $pdo->query("
    SELECT s.*, h.name AS habitat_name, s.main_image
    FROM species s
    LEFT JOIN habitats h ON s.habitat_id = h.id
    ORDER BY s.name ASC
");

    $speciess = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>❌ Erreur lors de la récupération des animaux : " . htmlspecialchars($e->getMessage()) . "</div>";
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Animaux - Zoo Arcadia</title>
    <link rel="stylesheet" href="/front/css/animals.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <section class="container py-5">
        <h1 class="text-center mb-4">Découvrez les animaux d'Arcadia</h1>

        <!-- Barre de recherche -->
        <div class="mb-4 pt-4 w-50 mx-auto">
            <input type="text" id="searchInput" class="form-control text-center" placeholder="Rechercher un animal..." onkeyup="filterAnimals()">
        </div>

        <div class="row justify-content-center" id="animalList">
            <?php if (!empty($speciess)): ?>
                <?php foreach ($speciess as $specie): ?>
                    <div class="col-md-4 col-sm-6 mb-4 animal-card d-flex justify-content-center">
                        <a href="/species/<?= $specie['id'] ?>.php" class="card border-0 text-center">
                            <img src="<?= htmlspecialchars($specie['main_image'] ?? '/images/default.jpg') ?>" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($specie['name']) ?></h5>
                                <p class="card-text text-muted">
                                    Habitat : <?= htmlspecialchars($specie['habitat_name'] ?? 'Non défini') ?>
                                </p>

                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">Aucun animal trouvé.</p>
            <?php endif; ?>
        </div>
    </section>

    <script src="/front/javascript/animals.js"></script>
    <?php require __DIR__ . '/../../templates/footer.php'; ?>

</body>

</html>