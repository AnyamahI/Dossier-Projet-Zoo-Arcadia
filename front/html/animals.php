<?php
require '../../lib/pdo.php'; // Connexion à la base de données
require __DIR__ . '/../../templates/header.html';

// Récupération des animaux avec leur habitat
$query = $pdo->query("SELECT animals.*, habitats.name AS habitat_name FROM animals INNER JOIN habitats ON animals.habitat_id = habitats.id ORDER BY animals.name ASC");
$animals = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

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
            <?php foreach ($animals as $animal): ?>
                <div class="col-md-4 col-sm-6 mb-4 animal-card d-flex justify-content-center">
                    <div class="card border-0 text-center" style="background: transparent;">
                        <img src="<?= htmlspecialchars($animal['image']) ?>" class="card-img-top img-fluid rounded" alt="<?= htmlspecialchars($animal['name']) ?>" style="object-fit: contain; max-height: 250px;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($animal['name']) ?></h5>
                            <p class="card-text text-muted">Habitat : <?= htmlspecialchars($animal['habitat_name']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <script src="/front/javascript/animals.js"></script>
    <?php require __DIR__ . '/../../templates/footer.php'; ?>

</body>

</html>