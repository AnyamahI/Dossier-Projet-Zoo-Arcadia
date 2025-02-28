<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_habitat'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $imagePath = NULL;

    if (empty($name) || empty($description)) {
        $error = "Tous les champs sont requis.";
    } else {
        // Gestion de l'upload d'image
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../../uploads/habitats/";
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . time() . "_" . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

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
                $query = $pdo->prepare("INSERT INTO habitats (name, description, image) VALUES (:name, :description, :image)");
                $query->execute([
                    ':name' => $name,
                    ':description' => $description,
                    ':image' => $imagePath,
                ]);
                header('Location: manage_habitats.php');
                exit;
            } catch (PDOException $e) {
                $error = "Erreur lors de l'ajout de l'habitat : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un habitat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Ajouter un habitat</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name">Nom de l'habitat</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="mb-3">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="image">Image de l'habitat</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" name="add_habitat" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>

</html>