<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

// Vérifiez que seul un administrateur peut accéder à cette page
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_habitat'])) {
    // Récupération des données du formulaire
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));

    // Validation des données
    if (empty($name) || empty($description)) {
        $error = "Tous les champs sont requis.";
    } else {
        try {
            // Préparer et exécuter la requête d'insertion
            $query = $pdo->prepare("INSERT INTO habitats (name, description) VALUES (:name, :description)");
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->execute();

            $message = "Habitat ajouté avec succès !";
            // Réinitialiser les champs après succès
            $_POST['name'] = "";
            $_POST['description'] = "";
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout de l'habitat : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Habitat</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Lien vers un fichier CSS si nécessaire -->
</head>

<body>
    <header>
        <h1>Ajouter un Nouvel Habitat</h1>
        <a href="manage_habitats.php">Retour à la gestion des habitats</a>
    </header>
    <main>
        <?php if (!empty($message)): ?>
            <p style="color: green;"> <?= $message ?> </p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p style="color: red;"> <?= $error ?> </p>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="name">Nom de l'habitat :</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" name="add_habitat">Ajouter</button>
        </form>
    </main>
</body>

</html>