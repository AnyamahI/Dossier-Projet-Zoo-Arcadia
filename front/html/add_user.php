<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';
require '../../vendor/autoload.php'; // Chargement de PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier que seul un admin peut accéder à cette page
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = htmlspecialchars(trim($_POST['role']));

    // Vérifications des champs
    if (empty($email) || empty($password) || empty($role)) {
        $error = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (!in_array($role, ['employee', 'veterinaire'])) {
        $error = "Rôle invalide.";
    } else {
        try {
            // Vérifier si l'utilisateur existe déjà
            $query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $query->bindParam(':email', $email);
            $query->execute();
            $userExists = $query->fetchColumn();

            if ($userExists) {
                $error = "Un utilisateur avec cet email existe déjà.";
            } else {
                // Hasher le mot de passe
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Insérer l'utilisateur en BDD
                $query = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, :role)");
                $query->bindParam(':email', $email);
                $query->bindParam(':password', $hashedPassword);
                $query->bindParam(':role', $role);
                $query->execute();

                // ✅ Envoi de l'email avec PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = getenv('SMTP_HOST');
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv('SMTP_USER');
                    $mail->Password = getenv('SMTP_PASS');
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = getenv('SMTP_PORT');

                    $mail->setFrom('admin@arcadia.com', 'Arcadia Zoo');
                    $mail->addAddress($email); // Destinataire = nouvel utilisateur

                    $mail->isHTML(true);
                    $mail->Subject = 'Bienvenue sur Arcadia Zoo !';

                    // 💌 Nouveau contenu de l'email
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; border-radius: 10px;'>
                            <h2 style='color: #2a9d8f;'>Bienvenue sur Arcadia Zoo 🎉</h2>
                            <p>Bonjour,</p>
                            <p>Nous avons le plaisir de vous informer que votre compte sur <strong>Arcadia Zoo</strong> a été créé avec succès.</p>
                            <p><strong>Votre adresse email de connexion :</strong> <br> <strong>$email</strong></p>
                            <p><strong>🔐 Important :</strong> Pour des raisons de sécurité, votre mot de passe ne peut pas être envoyé par email.</p>
                            <p>Veuillez vous rapprocher de votre administrateur pour récupérer votre mot de passe.</p>
                            <hr>
                            <p>À bientôt sur <strong>Arcadia Zoo</strong> ! 🦁🐘</p>
                        </div>
                    ";

                    $mail->send();
                    $message = "✅ Utilisateur créé avec succès. Un email a été envoyé à $email.";
                } catch (Exception $e) {
                    $error = "Utilisateur créé, mais l'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la création de l'utilisateur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Ajouter un utilisateur</h1>
    </header>

    <main class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <a href="manage_user.php" class="btn btn-secondary">Retour à la gestion des utilisateurs</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="post" class="p-4 bg-light shadow-sm rounded">
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rôle :</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="employee">Employé</option>
                    <option value="veterinaire">Vétérinaire</option>
                </select>
            </div>

            <button type="submit" name="add_user" class="btn btn-primary w-100">Ajouter</button>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>