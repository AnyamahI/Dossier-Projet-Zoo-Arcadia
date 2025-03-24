<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isVeterinaire() && !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Vérifie si un ID valide est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_animals.php');
    exit;
}

$id = intval($_GET['id']);

// Récupérer les informations actuelles de l'animal
try {
    $query = $pdo->prepare("
        SELECT * FROM species WHERE id = :id
    ");
    $query->execute([':id' => $id]);
    $species = $query->fetch(PDO::FETCH_ASSOC);

    if (!$species) {
        $error = "Animal introuvable.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération de l'animal : " . $e->getMessage();
}

// Récupérer les habitats pour le dropdown
try {
    $query = $pdo->query("SELECT id, name FROM habitats");
    $habitats = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

if (!$species) {
    $species = [];
}

// Initialisation des variables avec les valeurs existantes
$name = $species['name'] ?? '';
$species_name = $species['species_name'] ?? '';
$habitat_id = $species['habitat_id'] ?? '';
$IUCN_status = $species['IUCN_status'] ?? '';
$class_bio = $species['class_bio'] ?? '';
$order_bio = $species['order_bio'] ?? '';
$family = $species['family'] ?? '';
$size = $species['size'] ?? '';
$weight = $species['weight'] ?? '';
$lifespan = $species['lifespan'] ?? '';
$gestation = $species['gestation'] ?? '';
$population_status = $species['population_status'] ?? '';
$natural_habitat = $species['natural_habitat'] ?? '';
$distribution = $species['distribution'] ?? '';
$description = $species['description'] ?? '';
$imagePath = $species['image'] ?? null;

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les nouvelles valeurs du formulaire
    $name = $_POST['name'] ?? $name;
    $species_name = $_POST['species_name'] ?? $species_name;
    $habitat_id = $_POST['habitat_id'] ?? $habitat_id;
    $IUCN_status = $_POST['IUCN_status'] ?? $IUCN_status;
    $class_bio = $_POST['class_bio'] ?? $class_bio;
    $order_bio = $_POST['order_bio'] ?? $order_bio;
    $family = $_POST['family'] ?? $family;
    $size = $_POST['size'] ?? $size;
    $weight = $_POST['weight'] ?? $weight;
    $lifespan = $_POST['lifespan'] ?? $lifespan;
    $gestation = $_POST['gestation'] ?? $gestation;
    $population_status = $_POST['population_status'] ?? $population_status;
    $natural_habitat = $_POST['natural_habitat'] ?? $natural_habitat;
    $distribution = $_POST['distribution'] ?? $distribution;
    $description = $_POST['description'] ?? $description;

    // Vérifier les images (si nouvelles images uploadées)
    $mainImagePath = $species['main_image'] ?? null;

    if (!empty($_FILES['main_image']['name'])) {
        $speciesUploadDir = '../../uploads/species/';
        $filename = time() . '_' . basename($_FILES['main_image']['name']);
        $uploadFile = $speciesUploadDir . $filename;

        if (!is_dir($speciesUploadDir)) {
            mkdir($speciesUploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadFile)) {
            $mainImagePath = '/uploads/species/' . $filename;
        } else {
            $error = "Erreur lors du téléchargement de l'image principale.";
        }
    }

    try {
        $query = $pdo->prepare("
            UPDATE species SET 
                name = :name, 
                habitat_id = :habitat_id, 
                main_image = :main_image
            WHERE id = :id
        ");
        $query->execute([
            ':name' => $name,
            ':habitat_id' => $habitat_id,
            ':main_image' => $mainImagePath,
            ':id' => $id
        ]);


        // Mise à jour des informations de l'espèce
        $query = $pdo->prepare("
            UPDATE species 
            SET IUCN_status = :IUCN_status, class_bio = :class_bio, 
                order_bio = :order_bio, family = :family, size = :size, 
                weight = :weight, lifespan = :lifespan, gestation = :gestation, 
                population_status = :population_status, natural_habitat = :natural_habitat, 
                distribution = :distribution, description = :description
            WHERE id = :species_id
        ");
        $query->execute([
            ':IUCN_status' => $IUCN_status,
            ':class_bio' => $class_bio,
            ':order_bio' => $order_bio,
            ':family' => $family,
            ':size' => $size,
            ':weight' => $weight,
            ':lifespan' => $lifespan,
            ':gestation' => $gestation,
            ':population_status' => $population_status,
            ':natural_habitat' => $natural_habitat,
            ':distribution' => $distribution,
            ':description' => $description,
            ':species_id' => $id
        ]);
        $species_id = $_POST["species_id"] ?? null;
        if ($query->rowCount() > 0) {
            echo "Mise à jour réussie.";
        $success = "Animal mis à jour";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour de l'animal : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Modifier un Animal</h1>
    </header>
    <main class="container mt-4">
        <!-- Afficher les messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($species): ?>
            <!-- Formulaire de modification -->
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom commun</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($species['name'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="species_name" class="form-label">Nom scientifique</label>
                    <input type="text" name="species_name" id="species_name" class="form-control" value="<?= htmlspecialchars($species['species_name'] ?? 'Non renseigné') ?>">
                </div>

                <div class="mb-3">
                    <label for="habitat_id" class="form-label">Habitat</label>
                    <select name="habitat_id" id="habitat_id" class="form-select">
                        <?php foreach ($habitats as $habitat): ?>
                            <option value="<?= $habitat['id'] ?>" <?= (!empty($species['habitat_id']) && $habitat['id'] == $species['habitat_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($habitat['name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="IUCN_status" class="form-label">Statut UICN</label>
                    <input type="text" name="IUCN_status" id="IUCN_status" class="form-control" value="<?= htmlspecialchars($species['IUCN_status'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="class_bio" class="form-label">Classe</label>
                    <input type="text" name="class_bio" id="class_bio" class="form-control" value="<?= htmlspecialchars($species['class_bio'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="order_bio" class="form-label">Ordre</label>
                    <input type="text" name="order_bio" id="order_bio" class="form-control" value="<?= htmlspecialchars($species['order_bio'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="family" class="form-label">Famille</label>
                    <input type="text" name="family" id="family" class="form-control" value="<?= htmlspecialchars($species['family'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="size" class="form-label">Taille</label>
                    <input type="text" name="size" id="size" class="form-control" value="<?= htmlspecialchars($species['size'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="weight" class="form-label">Poids</label>
                    <input type="text" name="weight" id="weight" class="form-control" value="<?= htmlspecialchars($species['weight'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="lifespan" class="form-label">Espérance de vie</label>
                    <input type="text" name="lifespan" id="lifespan" class="form-control" value="<?= htmlspecialchars($species['lifespan'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="gestation" class="form-label">Gestation et porté</label>
                    <input type="text" name="gestation" id="gestation" class="form-control" value="<?= htmlspecialchars($species['gestation'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="population_status" class="form-label">Statut de la population</label>
                    <input type="text" name="population_status" id="population_status" class="form-control" value="<?= htmlspecialchars($species['population_status'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="natural_habitat" class="form-label">Habitat naturel</label>
                    <input type="text" name="natural_habitat" id="natural_habitat" class="form-control" value="<?= htmlspecialchars($species['natural_habitat'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="distribution" class="form-label">Répartition géographique</label>
                    <input type="text" name="distribution" id="distribution" class="form-control" value="<?= htmlspecialchars($species['distribution'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="main_image" class="form-label">Image principale</label>
                    <input type="file" name="main_image" id="main_image" class="form-control">
                    <?php if (!empty($species['main_image'])): ?>
                        <p class="mt-2">Image actuelle :</p>
                        <img src="<?= htmlspecialchars($species['main_image']) ?>" alt="Image principale" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="distribution_map" class="form-label">Image de la carte</label>
                    <input type="file" name="distribution_map" id="distribution_map" class="form-control">
                    <?php if (!empty($species['distribution_map'] ?? '')): ?>
                        <p class="mt-2">Image actuelle :</p>
                        <img src="<?= htmlspecialchars($species['distribution_map'] ?? '') ?>" alt="Carte de répartition" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($species['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="IUCN_image" class="form-label">Image IUCN</label>
                    <input type="file" name="IUCN_image" id="IUCN_image" class="form-control">
                    <?php if (!empty($species['IUCN_image'])): ?>
                        <img src="<?= htmlspecialchars($species['IUCN_image']) ?>" alt="Image IUCN" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="description_image" class="form-label">Image de la description</label>
                    <input type="file" name="description_image" id="description_image" class="form-control">
                    <?php if (!empty($species['description_image'])): ?>
                        <img src="<?= htmlspecialchars($species['description_image']) ?>" alt="Image de la description" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="cover_image" class="form-label">Photo de couverture</label>
                    <input type="file" name="cover_image" id="cover_image" class="form-control">
                    <?php if (!empty($species['cover_image'])): ?>
                        <img src="<?= htmlspecialchars($species['cover_image']) ?>" alt="Photo de couverture" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="secondary_image" class="form-label">Photo secondaire</label>
                    <input type="file" name="secondary_image" id="secondary_image" class="form-control">
                    <?php if (!empty($species['secondary_image'])): ?>
                        <img src="<?= htmlspecialchars($species['secondary_image']) ?>" alt="Photo secondaire" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="manage_animals.php" class="btn btn-secondary">Annuler</a>
            </form>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
