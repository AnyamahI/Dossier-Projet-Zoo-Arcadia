<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_create')) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Récupérer les habitats pour le formulaire
$habitats = [];
try {
    $query = $pdo->query("SELECT id, name FROM habitats");
    $habitats = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

// Traitement du formulaire d'ajout d'un animal de base
if (isset($_POST['add_animal'])) {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $habitat_id = $_POST['habitat_id'];
    $food = $_POST['food'];
    $imagePath = NULL;

    // Gestion de l'upload de l'image principale
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../../uploads/animals/';
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $filename;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imagePath = '/uploads/animals/' . $filename;
        } else {
            $error = "❌ Erreur lors du téléchargement de l'image.";
        }
    }

    if (empty($error)) {
        try {
            $query = $pdo->prepare("INSERT INTO animals (name, species, habitat_id, food, image) VALUES (:name, :species, :habitat_id, :food, :image)");
            $query->execute([
                ':name' => $name,
                ':species' => $species,
                ':habitat_id' => $habitat_id,
                ':food' => $food,
                ':image' => $imagePath
            ]);

            $lastId = $pdo->lastInsertId();
            header("Location: add_animal.php?animal_id=" . $lastId);
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}

// Traitement du formulaire de création de la page de l'animal
if (isset($_POST['create_page'])) {
    $animal_id = $_POST['animal_id'];
    $IUCN_status = $_POST['IUCN_status'];
    $theme = $_POST['theme'];
    $size = $_POST['size'];
    $weight = $_POST['weight'];
    $lifespan = $_POST['lifespan'];
    $population_status = $_POST['population_status'];
    $distribution = $_POST['distribution'];
    $family = $_POST['family'];
    $order_bio = $_POST['order_bio'];
    $class_bio = $_POST['class_bio'];

    $uploads = [];
    foreach (['IUCN_image', 'distribution_map', 'description_image'] as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $uploadDir = '../../uploads/animals/';
            $fileName = time() . '_' . basename($_FILES[$field]['name']);
            $targetFilePath = $uploadDir . $fileName;
            move_uploaded_file($_FILES[$field]['tmp_name'], $targetFilePath);
            $uploads[$field] = '/uploads/animals/' . $fileName;
        } else {
            $uploads[$field] = NULL;
        }
    }

    try {
        $query = $pdo->prepare("UPDATE animals SET IUCN_status = :IUCN_status, size = :size, weight = :weight, lifespan = :lifespan, population_status = :population_status, distribution = :distribution, family = :family, order_bio = :order_bio, class_bio = :class_bio, IUCN_image = :IUCN_image, distribution_map = :distribution_map, description_image = :description_image, page_link = :page_link WHERE id = :animal_id");
        $query->execute([
            ':IUCN_status' => $IUCN_status,
            ':size' => $size,
            ':weight' => $weight,
            ':lifespan' => $lifespan,
            ':population_status' => $population_status,
            ':distribution' => $distribution,
            ':family' => $family,
            ':order_bio' => $order_bio,
            ':class_bio' => $class_bio,
            ':IUCN_image' => $uploads['IUCN_image'],
            ':distribution_map' => $uploads['distribution_map'],
            ':description_image' => $uploads['description_image'],
            ':page_link' => "/animal.php?id=" . $animal_id,
            ':animal_id' => $animal_id
        ]);

        header("Location: /animal.php?id=" . $animal_id);
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la création de la page : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Ajouter un Animal</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="manage_animals.php" class="btn btn-secondary">⬅ Retour à la gestion des animaux</a>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <h3>Ajouter un animal</h3>

            <div class="mb-3">
                <label for="name" class="form-label">Nom de l'animal</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="species" class="form-label">Espèce</label>
                <input type="text" name="species" id="species" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="habitat_id" class="form-label">Habitat</label>
                <select name="habitat_id" id="habitat_id" class="form-control" required>
                    <?php foreach ($habitats as $habitat): ?>
                        <option value="<?= $habitat['id'] ?>"><?= htmlspecialchars($habitat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="food" class="form-label">Nourriture</label>
                <input type="text" name="food" id="food" class="form-control">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image principale</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>

            <button type="submit" name="add_animal" class="btn btn-success">Ajouter l'animal</button>
        </form>

        <?php if (isset($_GET['animal_id'])): ?>
            <form method="POST" enctype="multipart/form-data">
                <h3>Créer une page pour l'animal</h3>
                <input type="hidden" name="animal_id" value="<?= $_GET['animal_id'] ?>">

                <div class="mb-3">
                    <label for="IUCN_status" class="form-label">Statut UICN</label>
                    <input type="text" name="IUCN_status" id="IUCN_status" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="theme" class="form-label">Thème</label>
                    <select name="theme" id="theme" class="form-control" required>
                        <option value="jungle">Jungle</option>
                        <option value="savane">Savane</option>
                        <option value="marais">Marais</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label for="distribution" class="form-label">Répartition géographique</label>
                    <textarea name="distribution" id="distribution" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label for="distribution_map" class="form-label">Image de la carte</label>
                    <input type="file" name="distribution_map" id="distribution_map" class="form-control">
                </div>

                <button type="submit" name="create_page" class="btn btn-primary">Créer la page</button>
            </form>
        <?php endif; ?>

    </main>
</body>

</html>