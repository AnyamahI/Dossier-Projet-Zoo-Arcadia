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
    $name = $_POST['name'];
    $description = $_POST['description'];

    try {
        $query = $pdo->prepare("UPDATE habitats SET name = :name, description = :description WHERE id = :id");
        $query->bindParam(':name', $name);
        $query->bindParam(':description', $description);
        $query->bindParam(':id', $id);
        $query->execute();

        header('Location: manage_habitats.php');
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour de l'habitat : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Modifier un habitat</title>
</head>

<body>
    <h1>Modifier un habitat</h1>
    <form method="post">
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($habitat['name']) ?>" required>

        <label for="description">Description :</label>
        <textarea name="description" id="description" required><?= htmlspecialchars($habitat['description']) ?></textarea>

        <button type="submit">Modifier</button>
    </form>
</body>

</html>