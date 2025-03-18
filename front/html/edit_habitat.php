<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: manage_habitats.php');
    exit;
}

$habitat = null;
$error = '';

try {
    $query = $pdo->prepare("SELECT * FROM habitats WHERE id = :id");
    $query->bindParam(':id', $id);
    $query->execute();
    $habitat = $query->fetch();

    if (!$habitat) {
        header('Location: manage_habitats.php');
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération de l'habitat : " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $imagePath = $habitat['image']; // Image actuelle

    // Validation des champs
    if (empty($name) || empty($description)) {
        $error = "⚠️ Tous les champs sont obligatoires.";
    } else {
        // Gestion de l'upload d'une nouvelle image
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
                $query = $pdo->prepare("UPDATE habitats SET name = :name, description = :description, image = :image WHERE id = :id");
                $query->execute([
                    ':name' => $name,
                    ':description' => $description,
                    ':image' => $imagePath,
                    ':id' => $id
                ]);

                header('Location: manage_habitats.php');
                exit;
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour de l'habitat : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier un habitat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Modifier un habitat</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name">Nom de l'habitat</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($habitat['name']) ?>">
            </div>
            <div class="mb-3">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($habitat['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image">Modifier l'image de l'habitat (optionnel)</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Modifier</button>
        </form>
    </div>
</body>

</html>