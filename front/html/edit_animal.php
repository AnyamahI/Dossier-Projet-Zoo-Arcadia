<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isVeterinair() && !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';
$animal = null;

// Vérifie si un ID valide est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_animals.php');
    exit;
}

$id = $_GET['id'];

// Récupérer les informations actuelles de l'animal
try {
    $query = $pdo->prepare("SELECT * FROM animals WHERE id = :id");
    $query->execute([':id' => $id]);
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

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $species = $_POST['species'] ?? '';
    $habitat = $_POST['habitat'] ?? '';
    $description = $_POST['description'] ?? '';
    $page_link = !empty($_POST['page_link']) ? htmlspecialchars(trim($_POST['page_link'])) : null; // Nouveau champ

    // Conserver l'ancienne image si aucune nouvelle n'est envoyée
    $imagePath = $animal['image'];

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../../uploads/animals/';
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $filename;

        // Vérifie si le dossier existe, sinon le créer
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Vérifie si le fichier est bien une image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $error = "❌ Seuls les fichiers JPG, PNG et GIF sont autorisés.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $imagePath = $filename; // Met à jour le chemin de l'image
            } else {
                $error = "❌ Erreur lors du téléchargement de l'image.";
            }
        }
    }

    if (empty($name) || empty($species) || empty($habitat)) {
        $error = '❌ Les champs Nom, Espèce et Habitat sont obligatoires.';
    } else {
        try {
            $query = $pdo->prepare("
                UPDATE animals 
                SET name = :name, species = :species, habitat_id = :habitat, description = :description, image = :image, page_link = :page_link
                WHERE id = :id
            ");
            $query->execute([
                ':name' => $name,
                ':species' => $species,
                ':habitat' => $habitat,
                ':description' => $description,
                ':image' => $imagePath,
                ':page_link' => $page_link, // Mise à jour du champ page_link
                ':id' => $id
            ]);

            $success = "✅ Animal mis à jour avec succès !";
        } catch (PDOException $e) {
            $error = "❌ Erreur lors de la mise à jour de l'animal : " . $e->getMessage();
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
        <!-- Afficher les messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($animal): ?>
            <!-- Formulaire de modification -->
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($animal['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="species" class="form-label">Espèce</label>
                    <input type="text" name="species" id="species" class="form-control" value="<?= htmlspecialchars($animal['species']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="habitat" class="form-label">Habitat</label>
                    <select name="habitat" id="habitat" class="form-select" required>
                        <?php foreach ($habitats as $habitat): ?>
                            <option value="<?= $habitat['id'] ?>" <?= ($habitat['id'] == $animal['habitat_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($habitat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($animal['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="page_link">Lien vers la page HTML (optionnel)</label>
                    <input type="text" name="page_link" id="page_link" class="form-control" value="<?= htmlspecialchars($animal['page_link']) ?>">
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Changer l’image</label>
                    <input type="file" name="image" id="image" class="form-control">
                    <?php if (!empty($animal['image'])): ?>
                        <p class="mt-2">Image actuelle :</p>
                        <img src="/uploads/animals/<?= htmlspecialchars($animal['image']) ?>" alt="Image de <?= htmlspecialchars($animal['name']) ?>" class="img-fluid" style="max-width: 200px;">
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