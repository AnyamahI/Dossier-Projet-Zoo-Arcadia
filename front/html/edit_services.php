<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$service = null;

// Vérifiez si un ID est fourni dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_services.php');
    exit;
}

$id = $_GET['id'];

// Récupérer les informations du service à modifier
try {
    $query = $pdo->prepare("SELECT * FROM services WHERE id = :id");
    $query->execute([':id' => $id]);
    $service = $query->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        $error = "Service introuvable.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération du service : " . $e->getMessage();
}

// 🔹 Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';

    // On garde l'ancienne image si aucune nouvelle image n'est envoyée
    $imagePath = $service['image'];

    // Vérifier et traiter l'upload de l'image
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../../uploads/services/';
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
                $imagePath = $uploadFile; // Met à jour le chemin de l'image
            } else {
                $error = " Erreur lors du téléchargement de l'image.";
            }
        }
    }

    //  Mise à jour des données dans la base
    if (empty($error)) {
        try {
            $query = $pdo->prepare("UPDATE services SET name = :name, description = :description, image = :image WHERE id = :id");
            $query->execute([
                ':name' => $name,
                ':description' => $description,
                ':image' => $imagePath,
                ':id' => $id
            ]);

            echo '<div class="alert alert-success">Service mis à jour avec succès !</div>';
            echo '<script>setTimeout(function(){ window.location.href = "manage_services.php"; }, 2000);</script>';
            exit;
        } catch (PDOException $e) {
            $error = " Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_FILES);
    exit;
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $imagePath = $service['image'];

    if (empty($name) || empty($description)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {

        // Vérification et gestion de l'upload d'image
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../../uploads/services/";
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . time() . "_" . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    $imagePath = $targetFilePath; // Met à jour le chemin de l'image
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Format d'image invalide.";
            }
        }

        if (empty($error)) {
            try {
                $query = $pdo->prepare("UPDATE services SET name = :name, description = :description, image = :image WHERE id = :id");
                $query->execute([
                    ':name' => $name,
                    ':description' => $description,
                    ':image' => $imagePath,
                    ':id' => $id
                ]);
                header('Location: manage_services.php');
                exit;
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour du service : " . $e->getMessage();
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
    <title>Modifier un service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Modifier un Service</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($service): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom du service</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($service['name']) ?>">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($service['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Changer l'image</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="manage_services.php" class="btn btn-secondary">Annuler</a>
            </form>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
