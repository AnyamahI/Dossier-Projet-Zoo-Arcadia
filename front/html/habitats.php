<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérification de l'ID
$habitatId = $_GET['id'] ?? null;
$redirected = $_GET['redirected'] ?? false;

// Si l'ID est manquant et que la redirection n'a pas encore eu lieu
if (!$habitatId && !$redirected) {
    // Récupère le premier habitat pour redirection par défaut
    $firstHabitatQuery = $pdo->query("SELECT id FROM habitats LIMIT 1");
    $firstHabitat = $firstHabitatQuery->fetch(PDO::FETCH_ASSOC);
    $firstHabitatId = $firstHabitat['id'] ?? null;

    if ($firstHabitatId) {
        header('Location: habitats.php?id=' . $firstHabitatId . '&redirected=true');
        exit;
    } else {
        exit("❌ Aucun habitat disponible.");
    }
} elseif (!$habitatId && $redirected) {
    exit("❌ ID manquant après redirection.");
}

// 🔹 Requête pour récupérer les **espèces** de l'habitat sélectionné
$speciesQuery = $pdo->prepare("
    SELECT s.id, s.name, s.image
    FROM species s
    WHERE s.habitat_id = :id
");
$speciesQuery->bindParam(':id', $habitatId, PDO::PARAM_INT);
$speciesQuery->execute();
$species = $speciesQuery->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Requête pour récupérer tous les habitats
$habitatsQuery = $pdo->query("SELECT * FROM habitats ORDER BY name ASC");
$habitats = $habitatsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitats - Zoo Arcadia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/front/css/habitats.css">
    <script defer src="/front/javascript/habitats.js"></script>
</head>

<body>

    <!-- Navigation -->
    <?php require __DIR__ . '/../../templates/header.html'; ?>


    <!-- Liste des espèces de l'habitat sélectionné -->
    <section class="container py-5">
        <h2 class="text-center mb-4">Espèces de l'habitat</h2>
        
        <div class="row justify-content-center">
            <?php if (!empty($species)): ?>
                <?php foreach ($species as $specie): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center p-3">
                            <img src="<?= htmlspecialchars($specie['image']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($specie['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($specie['name']) ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Aucune espèce trouvée pour cet habitat.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php require __DIR__ . '/../../templates/footer.php'; ?>

</body>

</html>
