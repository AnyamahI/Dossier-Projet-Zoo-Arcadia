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

// Vérifier si l'ID de l'espèce est bien transmis via GET (pré-remplissage)
$species_id = isset($_GET['species_id']) && is_numeric($_GET['species_id']) ? intval($_GET['species_id']) : '';

// Récupérer les habitats
$habitats = [];
try {
    $query = $pdo->query("SELECT id, name FROM habitats");
    $habitats = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

// Récupérer les espèces
$speciesList = [];
try {
    $query = $pdo->query("SELECT id, name FROM species");
    $speciesList = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des espèces : " . $e->getMessage();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $species_id = $_POST['species_id'] ?? '';
    $habitat_id = $_POST['habitat_id'] ?? '';
    $imagePath = NULL; // Par défaut, pas d'image

    if (empty($name) || empty($species_id) || empty($habitat_id)) {
        $error = "❌ Tous les champs doivent être remplis.";
    } else {
        // 🔹 Gestion de l'upload d'image
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = '../../uploads/animals/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $imagePath = '/uploads/animals/' . $fileName;
            } else {
                $error = "Erreur lors du téléchargement de l'image.";
            }
        }

        if (empty($error)) {
            try {
                $query = $pdo->prepare("INSERT INTO animals (name, species_id, habitat_id, image) 
                                        VALUES (:name, :species_id, :habitat_id, :image)");
                $query->execute([
                    ':name' => $name,
                    ':species_id' => $species_id,
                    ':habitat_id' => $habitat_id,
                    ':image' => $imagePath // Ajout du chemin de l'image
                ]);

                $success = "L’animal a bien été ajouté avec son image !";
            } catch (PDOException $e) {
                $error = "Erreur lors de l'ajout : " . $e->getMessage();
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
        <!-- Affichage des erreurs ou du succès -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Nom de l'animal</label>
                <input type="text" name="name" id="name" class="form-control" required>
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
                <label for="species_id" class="form-label">Espèce</label>
                <select name="species_id" id="species_id" class="form-control" required>
                    <option value="">Sélectionner une espèce</option>
                    <?php foreach ($speciesList as $species): ?>
                        <option value="<?= $species['id'] ?>" <?= ($species['id'] == $species_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($species['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Champ pour l'image -->
            <div class="mb-3">
                <label for="image" class="form-label">Image de l'animal</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Créer l'Animal</button>
            <a href="manage_animals.php" class="btn btn-secondary">Retour</a>
        </form>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
