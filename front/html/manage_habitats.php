<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$habitats = [];
$error = '';

try {
    $query = $pdo->query("SELECT * FROM habitats");
    $habitats = $query->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des habitats : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Gestion des habitats</title>
    <link rel="stylesheet" href="../css/manage_habitats.css">
</head>

<body>
    <header>
        <h1>Gestion des Habitats</h1>
        <a href="/front/html/admin_dashboard.php">Retour au tableau de bord</a>
    </header>
    <main>
        <?php if ($error): ?>
            <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>

        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($habitats as $habitat): ?>
                    <tr>
                        <td><?= htmlspecialchars($habitat['id']) ?></td>
                        <td><?= htmlspecialchars($habitat['name']) ?></td>
                        <td><?= htmlspecialchars($habitat['description']) ?></td>
                        <td>
                            <a href="/front/html/edit_habitat.php?id=<?= $habitat['id'] ?>">Modifier</a>
                            <a href="/front/html/delete_habitat.php?id=<?= $habitat['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet habitat ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Ajouter un nouvel habitat</h2>
        <form action="/front/html/add_habitat.php" method="post">
            <label for="name">Nom :</label>
            <input type="text" name="name" id="name" required>

            <label for="description">Description :</label>
            <textarea name="description" id="description" required></textarea>

            <button type="submit">Ajouter</button>
        </form>
    </main>
</body>

</html>