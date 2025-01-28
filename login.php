<?php
session_start();
require_once __DIR__ . '/templates/header.html';
require './lib/pdo.php';
require './lib/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = verifyUserLoginPassword($pdo, $email, $password);

    if ($user) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        switch ($user['role']) {
            case 'admin':
                header('Location: ../front/html/admin_dashboard.php');
                break;
            case 'employe':
                header('Location: ../front/html/employe_dashboard.php');
                break;
            case 'veterinaire':
                header('Location: ../front/html/veterinaire_dashboard.php');
                break;
        }
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Connexion</title>
</head>

<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="container col-xxl-4 px-4 py-5 bg-white shadow rounded">
            <h1 class="text-center mb-4">Se connecter</h1>

            <?php if (!empty($errors)) { ?>
                <?php foreach ($errors as $error) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php } ?>
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
</body>

</html>