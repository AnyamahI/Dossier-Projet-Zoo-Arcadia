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
    header('Location: manage_users.php');
    exit;
}

$user = null;
$error = "";
$message = "";

try {
    // Récupérer les informations de l'utilisateur
    $query = $pdo->prepare("SELECT id, email, role FROM users WHERE id = :id");
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: manage_users.php');
        exit;
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération de l'utilisateur : " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $email = htmlspecialchars(trim($_POST['email']));
    $role = htmlspecialchars(trim($_POST['role']));

    if (empty($email) || empty($role)) {
        $error = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (!in_array($role, ['employe', 'veterinaire'])) {
        $error = "Rôle invalide.";
    } else {
        try {
            $query = $pdo->prepare("UPDATE users SET email = :email, role = :role WHERE id = :id");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':role', $role, PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            $message = "Utilisateur mis à jour avec succès.";
            $user['email'] = $email;
            $user['role'] = $role;
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur</title>
</head>

<body>
    <h1>Modifier un utilisateur</h1>
    <a href="manage_users.php">Retour à la gestion des utilisateurs</a><br><br>

    <?php if (!empty($message)): ?>
        <p style="color: green;"> <?= htmlspecialchars($message) ?> </p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
    <?php endif; ?>

    <form action="" method="post">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required><br><br>

        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="employe" <?= ($user['role'] === 'employe') ? 'selected' : '' ?>>Employé</option>
            <option value="veterinaire" <?= ($user['role'] === 'veterinaire') ? 'selected' : '' ?>>Vétérinaire</option>
        </select><br><br>

        <button type="submit" name="update_user">Mettre à jour</button>
    </form>
</body>

</html>