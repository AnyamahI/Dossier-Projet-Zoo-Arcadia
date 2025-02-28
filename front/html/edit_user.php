<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Vérifier que l'ID de l'utilisateur est bien passé en paramètre GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID utilisateur invalide.";
    header('Location: manage_user.php');
    exit;
}

$id = $_GET['id'];
$error = "";
$message = "";

// Récupérer les informations actuelles de l'utilisateur
try {
    $query = $pdo->prepare("SELECT id, email, role FROM users WHERE id = :id");
    $query->execute([':id' => $id]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "Utilisateur introuvable.";
        header('Location: manage_user.php');
        exit;
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération de l'utilisateur : " . $e->getMessage();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $role = htmlspecialchars(trim($_POST['role']));

    // Vérifier les champs
    if (empty($email) || empty($role)) {
        $error = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (!in_array($role, ['admin', 'employee', 'veterinaire'])) {
        $error = "Rôle invalide.";
    } else {
        try {
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
            $query->execute([':email' => $email, ':id' => $id]);
            $emailExists = $query->fetchColumn();

            if ($emailExists) {
                $error = "Cet email est déjà utilisé par un autre utilisateur.";
            } else {
                // Mise à jour de l'utilisateur
                $query = $pdo->prepare("UPDATE users SET email = :email, role = :role WHERE id = :id");
                $query->execute([
                    ':email' => $email,
                    ':role' => $role,
                    ':id' => $id
                ]);

                $_SESSION['success'] = "Utilisateur mis à jour avec succès.";
                header('Location: manage_user.php');
                exit;
            }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Modifier un utilisateur</h1>
    </header>

    <main class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <a href="manage_user.php" class="btn btn-secondary">Retour à la gestion des utilisateurs</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" class="p-4 bg-light shadow-sm rounded">
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rôle :</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
                    <option value="employee" <?= $user['role'] == 'employee' ? 'selected' : '' ?>>Employé</option>
                    <option value="veterinaire" <?= $user['role'] == 'veterinaire' ? 'selected' : '' ?>>Vétérinaire</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Modifier</button>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>