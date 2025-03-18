<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $imagePath = NULL;

    if (empty($name) || empty($description)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        // Vérification et gestion de l'upload d'image
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../../uploads/services/"; // Dossier où stocker les images
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . time() . "_" . $fileName; // Ajout d'un timestamp pour éviter les doublons
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Vérifier si le fichier est bien une image
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    $imagePath = $targetFilePath;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Format d'image invalide. Formats acceptés : jpg, jpeg, png, gif.";
            }
        }

        if (empty($error)) {
            try {
                $query = $pdo->prepare("INSERT INTO services (name, description, image) VALUES (:name, :description, :image)");
                $query->execute([
                    ':name' => $name,
                    ':description' => $description,
                    ':image' => $imagePath,
                ]);
                header('Location: manage_services.php');
                exit;
            } catch (PDOException $e) {
                $error = "Erreur lors de l'ajout du service : " . $e->getMessage();
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
    <title>Ajouter un service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Ajouter un Service</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data"> <!-- Ajout de enctype -->
            <div class="mb-3">
                <label for="name" class="form-label">Nom du service</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image du service</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>

    </main>
</body>

</html>