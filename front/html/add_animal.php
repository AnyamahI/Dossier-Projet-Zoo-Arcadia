<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérification des permissions
if (!hasPermission($pdo, $_SESSION['user']['role'], 'animals', 'can_create')) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Récupérer les habitats pour le formulaire
try {
    $query = $pdo->query("SELECT id, name FROM habitats");
    $habitats = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

// Récupérer la liste des espèces existantes
try {
    $query = $pdo->query("SELECT id, name FROM species");
    $speciesList = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des espèces : " . $e->getMessage();
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $habitat_id = !empty($_POST['habitat_id']) ? intval($_POST['habitat_id']) : null;
    $food = trim($_POST['food'] ?? '');
    $imagePath = NULL;

    // Vérifier si tous les champs sont remplis
    if (empty($name) || empty($species) || empty($habitat_id)) {
        $error = "❌ Tous les champs obligatoires doivent être remplis.";
    } else {
        try {
            // Vérifier si l'espèce existe déjà
            $query = $pdo->prepare("SELECT id FROM species WHERE name = :name");
            $query->execute([':name' => $species]);
            $existing_species = $query->fetch(PDO::FETCH_ASSOC);

            // Si l'espèce n'existe pas, on l'ajoute
            if (!$existing_species) {
                $query = $pdo->prepare("INSERT INTO species (name) VALUES (:name)");
                $query->execute([':name' => $species]);
                $species_id = $pdo->lastInsertId(); // Récupérer l'ID généré
            } else {
                $species_id = $existing_species['id'];
            }

            // Gestion de l'upload de l'image
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

            // Si aucune erreur, on insère l'animal dans la base
            if (empty($error)) {
                $query = $pdo->prepare("INSERT INTO animals (name, species_id, habitat_id, food, image) 
                                        VALUES (:name, :species_id, :habitat_id, :food, :image)");
                $query->execute([
                    ':name' => $name,
                    ':species_id' => $species_id,
                    ':habitat_id' => $habitat_id,
                    ':food' => $food,
                    ':image' => $imagePath
                ]);

                $lastId = $pdo->lastInsertId();

                // Vérifier que le dossier animals/ existe
                $dir = __DIR__ . '/../../animals/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                // Créer une page spécifique pour l'animal
                $file_name = $dir . $lastId . ".php";
                $content = "<?php\n";
                $content .= "\$_GET['id'] = " . intval($lastId) . ";\n";
                $content .= "require __DIR__ . '/../templates/animal_template.php';\n";
                file_put_contents($file_name, $content);

                $success = "✅ L'animal <strong>$name</strong> a bien été ajouté avec succès !";
                header("Location: add_animal.php?animal_id=" . $lastId);
                exit;
            }
        } catch (PDOException $e) {
            $error = "❌ Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}

// ✅ Vérification si le formulaire de création de la page de l'animal a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_page'])) {
    // ✅ Nettoyage et récupération des données du formulaire
    $animal_id = $_POST['animal_id'] ?? '';
    $IUCN_status = trim($_POST['IUCN_status'] ?? '');
    $class_bio = trim($_POST['class_bio'] ?? '');
    $order_bio = trim($_POST['order_bio'] ?? '');
    $family = trim($_POST['family'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $weight = trim($_POST['weight'] ?? '');
    $lifespan = trim($_POST['lifespan'] ?? '');
    $gestation = trim($_POST['gestation'] ?? '');
    $population_status = trim($_POST['population_status'] ?? '');
    $natural_habitat = trim($_POST['natural_habitat'] ?? '');
    $distribution = trim($_POST['distribution'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // ✅ Vérification si tous les champs obligatoires sont remplis
    if (empty($animal_id) || empty($IUCN_status) || empty($class_bio) || empty($order_bio) || empty($family)) {
        $error = "❌ Tous les champs obligatoires doivent être remplis.";
    } else {
        // ✅ Gestion sécurisée de l'upload des fichiers
        $uploads = [];
        $uploadDir = '../../uploads/animals/';

        // Liste des champs fichiers attendus
        $fileFields = ['IUCN_image', 'distribution_map', 'description_image', 'cover_image', 'secondary_image'];

        // Vérification et traitement de l'upload des fichiers
        foreach ($fileFields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $fileName = time() . '_' . basename($_FILES[$field]['name']);
                $targetFilePath = $uploadDir . $fileName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Vérification et déplacement du fichier
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFilePath)) {
                    $uploads[$field] = '/uploads/animals/' . $fileName;
                } else {
                    $uploads[$field] = NULL; // Si l'upload échoue, on met NULL
                    $error = "❌ Erreur lors de l'upload du fichier : " . $_FILES[$field]['name'];
                }
            } else {
                $uploads[$field] = NULL; // Si aucun fichier n'est envoyé
            }
        }

        // ✅ Mise à jour des informations de l'animal
        if (empty($error)) {
            try {
                $query = $pdo->prepare("
                    UPDATE animals SET 
                        IUCN_status = :IUCN_status, 
                        class_bio = :class_bio, 
                        order_bio = :order_bio, 
                        family = :family, 
                        size = :size, 
                        weight = :weight, 
                        lifespan = :lifespan,
                        gestation = :gestation,
                        population_status = :population_status, 
                        natural_habitat = :natural_habitat, 
                        distribution = :distribution, 
                        description = :description, 
                        IUCN_image = :IUCN_image, 
                        distribution_map = :distribution_map, 
                        description_image = :description_image, 
                        cover_image = :cover_image, 
                        secondary_image = :secondary_image, 
                        page_link = :page_link 
                    WHERE id = :animal_id
                ");

                // ✅ Exécution de la requête avec les valeurs associées
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
                    ':IUCN_image' => $uploads['IUCN_image'] ?? NULL,
                    ':distribution_map' => $uploads['distribution_map'] ?? NULL,
                    ':description_image' => $uploads['description_image'] ?? NULL,
                    ':cover_image' => $uploads['cover_image'] ?? NULL,
                    ':secondary_image' => $uploads['secondary_image'] ?? NULL,
                    ':page_link' => "/animals/" . $animal_id . ".php",
                    ':animal_id' => $animal_id
                ]);

                // ✅ Succès : Redirection après la mise à jour
                header("Location: /animals/" . $animal_id . ".php");
                exit;
            } catch (PDOException $e) {
                $error = "❌ Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }
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

        < method="POST" enctype="multipart/form-data">
            <h3>Ajouter une espèce</h3>

            <div class="mb-3">
                <label for="name" class="form-label">Nom de l'espèce</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="species" class="form-label">Nom scientifique de l'espèce</label>
                <input type="text" name="species" id="species" class="form-control" value="<?= htmlspecialchars($species ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="habitat_id" class="form-label">Habitat</label>
                <select name="habitat_id" id="habitat_id" class="form-control" required>
                    <?php foreach ($habitats as $habitat): ?>
                        <option value="<?= $habitat['id'] ?>" <?= isset($habitat_id) && $habitat_id == $habitat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($habitat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="food" class="form-label">Nourriture</label>
                <input type="text" name="food" id="food" class="form-control" value="<?= htmlspecialchars($food ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image principale</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>        

        <?php if (isset($_GET['animal_id'])): ?>
            <hr>
            <form method="POST" enctype="multipart/form-data">
                <h3>Créer une page pour l'animal</h3>
                <input type="hidden" name="animal_id" value="<?= $_GET['animal_id'] ?>">

                <div class="mb-3">
                    <label for="IUCN_status" class="form-label">Statut UICN</label>
                    <input type="text" name="IUCN_status" id="IUCN_status" class="form-control" value="<?= htmlspecialchars($IUCN_status ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="class_bio" class="form-label">Classe</label>
                    <input type="text" name="class_bio" id="class_bio" class="form-control" value="<?= htmlspecialchars($class_bio ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="order_bio" class="form-label">Ordre</label>
                    <input type="text" name="order_bio" id="order_bio" class="form-control" value="<?= htmlspecialchars($order_bio ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="family" class="form-label">Famille</label>
                    <input type="text" name="family" id="family" class="form-control" value="<?= htmlspecialchars($family ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="size" class="form-label">Taille</label>
                    <input type="text" name="size" id="size" class="form-control" value="<?= htmlspecialchars($size ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="weight" class="form-label">Poids</label>
                    <input type="text" name="weight" id="weight" class="form-control" value="<?= htmlspecialchars($weight ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="lifespan" class="form-label">Espérance de vie</label>
                    <input type="text" name="lifespan" id="lifespan" class="form-control" value="<?= htmlspecialchars($lifespan ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="gestation" class="form-label">Durée de gestation</label>
                    <input type="text" name="gestation" id="gestation" class="form-control" value="<?= htmlspecialchars($gestation ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="population_status" class="form-label">Statut de la population</label>
                    <input type="text" name="population_status" id="population_status" class="form-control" value="<?= htmlspecialchars($population_status ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="natural_habitat" class="form-label">Habitat naturel</label>
                    <input type="text" name="natural_habitat" id="natural_habitat" class="form-control" value="<?= htmlspecialchars($natural_habitat ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="distribution" class="form-label">Répartition géographique</label>
                    <textarea name="distribution" id="distribution" class="form-control"><?= htmlspecialchars($distribution ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="distribution_map" class="form-label">Image de la carte</label>
                    <input type="file" name="distribution_map" id="distribution_map" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="IUCN_image" class="form-label">Image IUCN</label>
                    <input type="file" name="IUCN_image" id="IUCN_image" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="description_image" class="form-label">Image de la description</label>
                    <input type="file" name="description_image" id="description_image" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="cover_image" class="form-label">Photo de couverture</label>
                    <input type="file" name="cover_image" id="cover_image" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="secondary_image" class="form-label">Photo secondaire</label>
                    <input type="file" name="secondary_image" id="secondary_image" class="form-control">
                </div>

                <button type="submit" name="add_animal" class="btn btn-success">Ajouter une espèce</button>
                </form>
        <?php endif; ?>

    </main>
</body>

</html>