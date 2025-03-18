<?php
session_start();
require_once __DIR__ . '/../lib/pdo.php';
require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../templates/header.html';

// Vérifier si un ID d'espèce est passé en paramètre
if (!isset($_GET['id'])) {
    echo "Espèce ID manquant.";
    exit;
}

$species_id = intval($_GET['id']);

// Récupérer les informations de l'espèce
try {
    // Récupérer les informations de l'espèce et de son habitat
    $query = $pdo->prepare("
        SELECT s.*, h.name AS habitat_name, h.theme AS habitat_theme
        FROM species s
        LEFT JOIN habitats h ON s.habitat_id = h.id
        WHERE s.id = :id
        ");
    $query->execute([':id' => $species_id]);
    $species = $query->fetch(PDO::FETCH_ASSOC);

    if (!$species) {
        echo "Espèce non trouvée.";
        exit;
    }

    // Charger le bon thème CSS
    $themeCss = !empty($species['habitat_theme']) ? $species['habitat_theme'] : 'default.css';


    // Récupérer les animaux associés à cette espèce **(EXCLUANT le nom de l'espèce)**
    $query = $pdo->prepare("
        SELECT a.id, a.name, a.image, h.name AS habitat_name
        FROM animals a
        JOIN habitats h ON a.habitat_id = h.id
        WHERE a.species_id = :species_id
        AND a.name != :species_name
    ");
    $query->execute([
        ':species_id' => $species_id,
        ':species_name' => $species['name'] // Exclure le nom de l'espèce
    ]);
    $animals = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="/front/css/<?= htmlspecialchars($themeCss) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title><?= htmlspecialchars($species['name']) ?></title>
</head>

<body>
    <section class="sec1">
        <div class="photo-background">
            <img src="<?= htmlspecialchars($species['cover_image']) ?>" alt="<?= htmlspecialchars($species['name']) ?>">
        </div>

        <div class="container">
            <div class="item_bg">
                <div class="blop_page_species">
                    <img src="<?= htmlspecialchars($species['secondary_image']) ?>" alt="<?= htmlspecialchars($species['name']) ?>">
                </div>
                <div class="info_IUCN">
                    <div class="titre-blop">
                        <img src="<?= htmlspecialchars($species['IUCN_image']) ?>" alt="Statut IUCN">
                        <h2><?= htmlspecialchars($species['IUCN_status']) ?></h2>
                    </div>
                    <div class="titre-text">
                        <h1><?= htmlspecialchars($species['name']) ?></h1>
                        <p><strong>Classement sur la liste rouge de l'IUCN</strong><br>
                            <?= htmlspecialchars($species['IUCN_status']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="shap-divider">
            <img src="/média/média/shap_divider_blanc.png" alt="">
        </div>
    </section>


    <section class="sec2 py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                    <p class="fs-5"><?= nl2br(htmlspecialchars($species['description'])) ?></p>
                </div>

                <div class="col-lg-4 col-md-12 text-center">
                    <img src="<?= htmlspecialchars($species['description_image']) ?>" class="img-fluid rounded shadow" alt="">
                </div>
            </div>
        </div>
    </section>


    <section class="sec3">
        <section class="sec3 py-5">
            <div class="container">
                <div class="row text-center g-4 align-items-start">
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box p-3">
                            <h5>Classe</h5>
                            <p><?= htmlspecialchars($species['class_bio']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box p-3">
                            <h5>Ordre</h5>
                            <p><?= htmlspecialchars($species['order_bio']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box p-3">
                            <h5>Famille</h5>
                            <p><?= htmlspecialchars($species['family']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box p-3">
                            <h5>Habitat</h5>
                            <p><?= htmlspecialchars($species['habitat_name']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                </div>

                <div class="row text-center g-4 align-items-end mt-5">
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="icon-box">
                            <i class="bi bi-rulers fs-1"></i>
                            <p><?= htmlspecialchars($species['size']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="icon-box">
                            <i class="bi bi-box-seam fs-1"></i>
                            <p><?= htmlspecialchars($species['weight']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="icon-box">
                            <i class="bi bi-cake fs-1"></i>
                            <p><?= htmlspecialchars($species['gestation']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="icon-box">
                            <i class="bi bi-hourglass-split fs-1"></i>
                            <p><?= htmlspecialchars($species['lifespan']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="icon-box">
                            <i class="bi bi-egg-fried fs-1"></i>
                            <p><?= htmlspecialchars($species['food']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="icon-box">
                            <i class="bi bi-bar-chart-line fs-1"></i>
                            <p><?= htmlspecialchars($species['population_status']) ?: "Non renseigné" ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </section>

    <section class="sec4">
        <div class="container">
            <div class="info-map">
                <div class="habitat">
                    <h3>Habitat naturel</h3>
                    <p><?= htmlspecialchars($species['natural_habitat']) ?></p> <!-- ici je veux récuperer l'habitat naturel de l'species -->
                </div>
                <div class="repartition">
                    <h3>Répartition géographique</h3>
                    <p><?= htmlspecialchars($species['distribution']) ?></p> <!-- ici je veux récuperer la répartition géographique de l'species -->
                </div>
            </div>
            <div class="map">
                <img src="<?= htmlspecialchars($species['distribution_map']) ?>" alt="" />
            </div>
        </div>
    </section>

    <section class="sec5">
        <div class="container">
            <h3 class="text-center mb-4">Nos <?= htmlspecialchars($species['name']) ?> au zoo</h3>
            <div class="row g-4 justify-content-center">
                <?php if (!empty($animals)): ?>
                    <?php foreach ($animals as $animal): ?>
                        <div class="col-md-4 col-sm-6 d-flex flex-column align-items-center">
                            <div class="text-center"
                                onclick="showAnimalDetails(<?= $animal['id'] ?>)"
                                style="cursor: pointer;">
                                <img src="<?= htmlspecialchars($animal['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($animal['name']) ?>">
                                <h5 class="mt-2 fw-bold"><?= htmlspecialchars($animal['name']) ?></h5>
                                <p class="text-muted mb-0">Habitat : <?= htmlspecialchars($animal['habitat_name']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">Aucun <?= htmlspecialchars($species['name']) ?> n'est actuellement visible au zoo.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Pop-up Bootstrap -->
    <div class="modal fade" id="animalDetailsModal" tabindex="-1" aria-labelledby="animalDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Augmente la taille de la modale -->
            <div class="modal-content border-0 shadow-lg rounded">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="animalDetailsLabel">Détails de l'animal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div id="animalDetailsContent" class="text-center">
                        <p class="text-muted">Chargement...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require_once __DIR__ . '/../templates/footer.php'; ?>
    <script src="/front/javascript/animal_details.js"></script>

</body>

</html>