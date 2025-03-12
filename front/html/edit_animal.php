<?php
session_start();
require_once __DIR__ . '/../../lib/session.php';
require_once __DIR__ . '/../../lib/pdo.php';

// Vérification des permissions (seuls les vétérinaires et admins peuvent modifier un animal)
if (!isset($_SESSION['user']['role']) || !in_array($_SESSION['user']['role'], ['veterinaire', 'admin'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Vérifier si un ID d'animal est passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_animals.php');
    exit;
}

$animal_id = intval($_GET['id']);

// Récupérer les informations de l'animal
try {
    $query = $pdo->prepare("SELECT * FROM animals WHERE id = :id");
    $query->execute([':id' => $animal_id]);
    $animal = $query->fetch(PDO::FETCH_ASSOC);

    if (!$animal) {
        $error = "❌ Animal introuvable.";
    }
} catch (PDOException $e) {
    $error = "❌ Erreur lors de la récupération de l'animal : " . $e->getMessage();
}

// Récupérer les habitats pour le dropdown
try {
    $query = $pdo->query("SELECT id, name FROM habitats");
    $habitats = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "❌ Erreur lors de la récupération des habitats : " . $e->getMessage();
}

// Récupérer les espèces pour le dropdown
try {
    $query = $pdo->query("SELECT id, name FROM species");
    $speciesList = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "❌ Erreur lors de la récupération des espèces : " . $e->getMessage();
}

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $habitat_id = $_POST['habitat_id'] ?? '';
    $species_id = $_POST['species_id'] ?? '';
    $imagePath = $animal['image']; // Garder l'image actuelle par défaut

    // Vérification des champs obligatoires
    if (empty($name) || empty($habitat_id) || empty($species_id)) {
        $error = "❌ Tous les champs doivent être remplis.";
    } else {
        // Gestion de l'upload d'image
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

        // Mise à jour des données
        try {
            $query = $pdo->prepare("
                UPDATE animals 
                SET name = :name, habitat_id = :habitat_id, species_id = :species_id, image = :image
                WHERE id = :id
            ");
            $query->execute([
                ':name' => $name,
                ':habitat_id' => $habitat_id,
                ':species_id' => $species_id,
                ':image' => $imagePath,
                ':id' => $animal_id
            ]);

            $success = "✅ Animal mis à jour avec succès !";
            header("Location: manage_animals.php");
            exit;
        } catch (PDOException $e) {
            $error = "❌ Erreur lors de la mise à jour : " . $e->getMessage();
        }
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
        <!-- Messages d'erreur ou de succès -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Nom de l'animal</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($animal['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="habitat_id" class="form-label">Habitat</label>
                <select name="habitat_id" id="habitat_id" class="form-select" required>
                    <?php foreach ($habitats as $habitat): ?>
                        <option value="<?= $habitat['id'] ?>" <?= ($habitat['id'] == $animal['habitat_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($habitat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="species_id" class="form-label">Espèce</label>
                <select name="species_id" id="species_id" class="form-select" required>
                    <?php foreach ($speciesList as $species): ?>
                        <option value="<?= $species['id'] ?>" <?= ($species['id'] == $animal['species_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($species['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Image actuelle :</label>
                <div>
                    <?php if (!empty($animal['image'])): ?>
                        <img src="<?= htmlspecialchars($animal['image']) ?>" alt="Image actuelle" class="img-fluid rounded" style="max-width: 200px;">
                    <?php else: ?>
                        <p class="text-muted">Aucune image enregistrée.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Nouvelle image (optionnel)</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Modifier</button>
            <a href="manage_animals.php" class="btn btn-secondary">Retour</a>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
