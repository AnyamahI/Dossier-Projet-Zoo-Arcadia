<?php
ob_start(); // Démarrer la mise en tampon de sortie
session_start();
require_once __DIR__ . '/templates/header.html';
require './lib/pdo.php';
require './lib/user.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        try {
            // ✅ Correction : Ajouter "name" dans la requête SQL
            $query = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = :email");
            $query->execute([':email' => $email]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // ✅ Vérifier si le champ "name" est NULL
                $userName = $user['name'] ?? "Utilisateur";

                // Stocker données utilisateur dans session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $userName,
                    'role' => $user['role']
                ];

                // Vérification rôle et redirection
                switch ($user['role']) {
                    case 'admin':
                        header('Location: front/html/admin_dashboard.php');
                        exit;
                    case 'veterinaire':
                        header('Location: front/html/veterinaire_dashboard.php');
                        exit;
                    case 'employee':
                        header('Location: front/html/employee_dashboard.php');
                        exit;
                    default:
                        $error = "Rôle utilisateur inconnu.";
                        session_destroy();
                }
                exit;
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $error = 'Erreur lors de la connexion : ' . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="container col-xxl-4 px-4 py-5 bg-white shadow rounded">
            <h1 class="text-center mb-4">Se connecter</h1>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php } ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="d-grid">
                    <button type="submit" name="loginUser" class="btn btn-primary">Connexion</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>