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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = htmlspecialchars(trim($_POST['role']));

    if (empty($email) || empty($password) || empty($role)) {
        $error = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (!in_array($role, ['employe', 'veterinaire'])) {
        $error = "Rôle invalide.";
    } else {
        try {
            // Insertion dans la base de données
            $query = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, :role)");
            $query->bindParam(':email', $email);
            $query->bindParam(':password', $password);
            $query->bindParam(':role', $role);
            $query->execute();

            // Envoi de l'email
            $subject = "Création de votre compte utilisateur";
            $messageBody = "
                Bonjour,<br><br>
                Un compte utilisateur a été créé pour vous avec l'email suivant : <strong>$email</strong>.<br><br>
                Votre mot de passe n'est pas communiqué pour des raisons de sécurité. Veuillez contacter l'administrateur pour l'obtenir.<br><br>
                Cordialement,<br>
                L'équipe de gestion.
            ";

            // Configuration de l'en-tête pour l'email
            $headers = "From: admin@arcadia.com\r\n";
            $headers .= "Reply-To: admin@arcadia.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            if (mail($email, $subject, $messageBody, $headers)) {
                $message = "Utilisateur créé avec succès, un email a été envoyé.";
            } else {
                $error = "Utilisateur créé, mais l'email n'a pas pu être envoyé.";
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
</head>

<body>
    <h1>Ajouter un utilisateur</h1>
    <a href="/front/html/manage_user.php">Retour à la gestion des utilisateurs</a>

    <?php if ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="employe">Employé</option>
            <option value="veterinaire">Vétérinaire</option>
        </select><br><br>

        <button type="submit" name="add_user">Ajouter</button>
    </form>
</body>

</html>