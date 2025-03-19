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
    $firstHabitat = $firstHabitatQuery->fetch();
    $firstHabitatId = $firstHabitat['id'] ?? null;

    if ($firstHabitatId) {
        header('Location: habitats.php?id=' . $firstHabitatId . '&redirected=true'); // ligne 18
        exit;
    } else {
        exit("❌ Aucun habitat disponible.");
    }
} elseif (!$habitatId && $redirected) {
    exit("❌ ID manquant après redirection.");
}

// Requête pour récupérer les animaux de l'habitat actuel
$speciesQuery = $pdo->prepare("
    SELECT a.id, a.name, a.image
    FROM species a
    WHERE a.habitat_id = :id
");
$speciesQuery->bindParam(':id', $habitatId);
$speciesQuery->execute();
$species = $speciesQuery->fetchAll();

// Requête séparée pour récupérer la liste des habitats
$habitatsQuery = $pdo->query("SELECT * FROM habitats ORDER BY name ASC");
$habitats = $habitatsQuery->fetchAll();



$query = $pdo->prepare("
    SELECT a.id, a.name, a.image
    FROM species a
    WHERE a.habitat_id = :id
");

// Récupération de la liste des habitats
$query = $pdo->query("SELECT * FROM habitats ORDER BY name ASC");
$habitats = $query->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Liste des habitats -->
    <section class="container py-5">
        <h1 class="text-center mb-4">Nos Habitats</h1>

        <div class="d-flex flex-column align-items-center gap-4">
            <?php foreach ($habitats as $habitat): ?>
                <div class="col-md-10 mb-4">
                    <div class="card habitat-card border-0 position-relative text-center p-3"
                        style="background: url('<?= htmlspecialchars($habitat['image']) ?>') center/cover no-repeat;">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center"
                           style="background-color: rgba(0, 0, 0, 0.2);">
                            <h1 class="text-white">
                                <?= htmlspecialchars($habitat['name']) ?>
                            </h1>
                            <button class="btn btn-primary voir-details mt-2" data-id="<?= $habitat['id'] ?>">
                                Voir plus
                            </button>
                            <div id="details-<?= $habitat['id'] ?>" class="mt-3"></div> <!-- Conteneur AJAX -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

    </section>

    <!-- Footer -->
    <?php require __DIR__ . '/../../templates/footer.php'; ?>

</body>

</html>