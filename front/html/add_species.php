<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérification des permissions
if (!hasPermission($pdo, $_SESSION['user']['role'], 'species', 'can_create')) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Récupérer les habitats existants
try {
    $query = $pdo->query("SELECT id, name FROM habitats");
    $habitats = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $scientific_name = trim($_POST['scientific_name'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $IUCN_status = trim($_POST['IUCN_status'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $weight = trim($_POST['weight'] ?? '');
    $lifespan = trim($_POST['lifespan'] ?? '');
    $population_status = trim($_POST['population_status'] ?? '');
    $distribution = trim($_POST['distribution'] ?? '');
    $family = trim($_POST['family'] ?? '');
    $order_bio = trim($_POST['order_bio'] ?? '');
    $class_bio = trim($_POST['class_bio'] ?? '');
    $natural_habitat = trim($_POST['natural_habitat'] ?? '');
    $gestation = trim($_POST['gestation'] ?? '');
    $habitat_id = !empty($_POST['habitat_id']) ? intval($_POST['habitat_id']) : null;


    // Vérification des champs obligatoires
    if (empty($name) || empty($description) || empty($family) || empty($order_bio) || empty($class_bio) || empty($habitat_id)) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } else {
        try {
            // Gestion des uploads d'images
            $uploads = [];
            $speciesUploadDir = '../../uploads/species/';
            $animalUploadDir = '../../uploads/animals/';

            // Liste des images spécifiques à l'espèce (stockées dans `/uploads/species/`)
            $speciesImageFields = ['IUCN_image', 'distribution_map', 'description_image', 'cover_image', 'secondary_image'];

            foreach ($speciesImageFields as $field) {
                if (!empty($_FILES[$field]['name'])) {
                    $fileName = time() . '_' . basename($_FILES[$field]['name']);
                    $targetFilePath = $speciesUploadDir . $fileName;

                    if (!is_dir($speciesUploadDir)) {
                        mkdir($speciesUploadDir, 0777, true);
                    }

                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFilePath)) {
                        $uploads[$field] = '/uploads/species/' . $fileName;
                    } else {
                        $uploads[$field] = NULL;
                        $error = "❌ Erreur lors de l'upload du fichier : " . $_FILES[$field]['name'];
                    }
                } else {
                    $uploads[$field] = NULL;
                }
            }

            $mainImagePath = NULL;
            if (!empty($_FILES['main_image']['name'])) {
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
            // Vérification de l'existence de l'espèce
            $query = $pdo->prepare("SELECT id FROM species WHERE name = :name");
            $query->execute([':name' => $name]);
            $existing_species = $query->fetch(PDO::FETCH_ASSOC);

            if (!$existing_species) {
                // Insérer l'espèce dans la base de données
                $query = $pdo->prepare("
                    INSERT INTO species (name, scientific_name, description, IUCN_status, IUCN_image, size, weight, lifespan, population_status, 
                                        distribution, distribution_map, family, order_bio, class_bio, description_image, 
                                        natural_habitat, cover_image, secondary_image, gestation, habitat_id, main_image) 
                    VALUES (:name, :scientific_name, :description, :IUCN_status, :IUCN_image, :size, :weight, :lifespan, :population_status, 
                            :distribution, :distribution_map, :family, :order_bio, :class_bio, :description_image, 
                            :natural_habitat, :cover_image, :secondary_image, :gestation, :habitat_id, :main_image)
                ");

                $query->execute([
                    ':name' => $name,
                    ':scientific_name' => $scientific_name,
                    ':description' => $description,
                    ':IUCN_status' => $IUCN_status,
                    ':IUCN_image' => $uploads['IUCN_image'] ?? NULL,
                    ':size' => $size,
                    ':weight' => $weight,
                    ':lifespan' => $lifespan,
                    ':population_status' => $population_status,
                    ':distribution' => $distribution,
                    ':distribution_map' => $uploads['distribution_map'] ?? NULL,
                    ':family' => $family,
                    ':order_bio' => $order_bio,
                    ':class_bio' => $class_bio,
                    ':description_image' => $uploads['description_image'] ?? NULL,
                    ':natural_habitat' => $natural_habitat,
                    ':cover_image' => $uploads['cover_image'] ?? NULL,
                    ':secondary_image' => $uploads['secondary_image'] ?? NULL,
                    ':gestation' => $gestation,
                    ':habitat_id' => $habitat_id,
                    ':main_image' => $mainImagePath
                ]);
                $species_id = $pdo->lastInsertId();
            } else {
                $species_id = $existing_species['id'];
            }
            // Ajouter un premier individu dans `animals`
            if (empty($error)) {

                // Générer la page de l'espèce
                $dir = __DIR__ . '/../../species/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $file_name = $dir . $species_id . ".php";
                $content = "<?php\n";
                $content .= "\$_GET['id'] = " . intval($species_id) . ";\n";
                $content .= "require __DIR__ . '/../templates/species_template.php';\n";
                file_put_contents($file_name, $content);

                // Succès et redirection
                $success = "L'espèce <strong>$name</strong> a bien été ajoutée avec succès !";
                header("Location: /species/$species_id.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Espèce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Ajouter une Espèce</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <h3>Ajouter une espèce</h3>

            <div class="mb-3">
                <label for="name" class="form-label">Nom commun</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="scientific_name" class="form-label">Nom scientifique</label>
                <input type="text" name="scientific_name" id="scientific_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="habitat_id" class="form-label">Habitat</label>
                <select name="habitat_id" id="habitat_id" class="form-control" required>
                    <?php foreach ($habitats as $habitat): ?>
                        <option value="<?= $habitat['id'] ?>" <?= isset($habitat_id) && $habitat_id == $habitat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($habitat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="IUCN_status" class="form-label">Statut UICN</label>
                <input type="text" name="IUCN_status" id="IUCN_status" class="form-control">
            </div>

            <div class="mb-3">
                <label for="class_bio" class="form-label">Classe</label>
                <input type="text" name="class_bio" id="class_bio" class="form-control">
            </div>

            <div class="mb-3">
                <label for="order_bio" class="form-label">Ordre</label>
                <input type="text" name="order_bio" id="order_bio" class="form-control">
            </div>

            <div class="mb-3">
                <label for="family" class="form-label">Famille</label>
                <input type="text" name="family" id="family" class="form-control">
            </div>

            <div class="mb-3">
                <label for="size" class="form-label">Taille</label>
                <input type="text" name="size" id="size" class="form-control">
            </div>

            <div class="mb-3">
                <label for="weight" class="form-label">Poids</label>
                <input type="text" name="weight" id="weight" class="form-control">
            </div>

            <div class="mb-3">
                <label for="lifespan" class="form-label">Espérance de vie</label>
                <input type="text" name="lifespan" id="lifespan" class="form-control">
            </div>

            <div class="mb-3">
                <label for="gestation" class="form-label">Durée de gestation</label>
                <input type="text" name="gestation" id="gestation" class="form-control">
            </div>

            <div class="mb-3">
                <label for="population_status" class="form-label">Statut de la population</label>
                <input type="text" name="population_status" id="population_status" class="form-control">
            </div>

            <div class="mb-3">
                <label for="natural_habitat" class="form-label">Habitat naturel</label>
                <input type="text" name="natural_habitat" id="natural_habitat" class="form-control">
            </div>

            <div class="mb-3">
                <label for="distribution" class="form-label">Répartition géographique</label>
                <textarea name="distribution" id="distribution" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>

            <!-- Upload d'images -->
            <div class="mb-3">
                <label for="description_image" class="form-label">Image de la description</label>
                <input type="file" name="description_image" id="description_image" class="form-control">
            </div>

            <div class="mb-3">
                <label for="main_image" class="form-label">Image principale de l'espèce</label>
                <input type="file" name="main_image" id="main_image" class="form-control">
            </div>

            <div class="mb-3">
                <label for="secondary_image" class="form-label">Photo secondaire</label>
                <input type="file" name="secondary_image" id="secondary_image" class="form-control">
            </div>

            <div class="mb-3">
                <label for="IUCN_image" class="form-label">Image IUCN</label>
                <input type="file" name="IUCN_image" id="IUCN_image" class="form-control">
            </div>

            <div class="mb-3">
                <label for="distribution_map" class="form-label">Carte de répartition</label>
                <input type="file" name="distribution_map" id="distribution_map" class="form-control">
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Image de couverture</label>
                <input type="file" name="cover_image" id="cover_image" class="form-control">
            </div>

            <button type="submit" name="add_species" class="btn btn-success">Ajouter l'espèce</button>
        </form>

    </main>
</body>

</html>
