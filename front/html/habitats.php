<?php
require '../../lib/pdo.php';

header('Content-Type: text/html');

// Vérifier si un ID d'habitat est passé
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Erreur : ID d'habitat invalide.</p>";
    exit;
}

$habitatId = intval($_GET['id']);

// 🔹 Récupérer les espèces associées à cet habitat
$query = $pdo->prepare("
    SELECT s.id, s.name, s.image
    FROM species s
    WHERE s.habitat_id = :id
");
$query->bindParam(':id', $habitatId, PDO::PARAM_INT);
$query->execute();
$species = $query->fetchAll(PDO::FETCH_ASSOC);

// Générer l'affichage des espèces
if (empty($species)) {
    echo "<p>Aucune espèce trouvée pour cet habitat.</p>";
} else {
    echo '<div class="row">';
    foreach ($species as $specie) {
        echo '
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="' . htmlspecialchars($specie['image']) . '" class="card-img-top img-fluid" alt="' . htmlspecialchars($specie['name']) . '">
                    <div class="card-body text-center">
                        <h5 class="card-title">' . htmlspecialchars($specie['name']) . '</h5>
                    </div>
                </div>
            </div>';
    }
    echo '</div>';
}
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
