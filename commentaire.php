<!DOCTYPE html>
<html>
<head>
    <title>Formulaire de Contact</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="nom">Nom</label>
        <input type="text" name="nom" required><br>
        <label for="commentaire">Commentaire</label><br>
        <textarea name="commentaire" cols="30" rows="10" required></textarea><br>
        <input type="submit" value="Envoyer">
    </form>

    <?php
    // Activer l'affichage des erreurs
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Définir le fichier log pour enregistrer les erreurs
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');

    // Informations de connexion à la base de données
    $user = "root";
    $pwd = ""; // Mot de passe vide
    $db = "mysql:host=localhost;dbname=commentaire;charset=utf8;port=3307";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = htmlspecialchars(trim($_POST["nom"]));
        $commentaire = htmlspecialchars(trim($_POST["commentaire"]));

        try {
            $cx = new PDO($db, $user, $pwd);
            $cx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insertion des données dans la base
            $sql = "INSERT INTO commentaires (nom, commentaire) VALUES (:nom, :commentaire)";
            $stmt = $cx->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':commentaire', $commentaire);
            $stmt->execute();
            echo "Votre commentaire a été ajouté avec succès.";
        } catch (PDOException $e) {
            echo "Une erreur est survenue lors de la connexion : " . $e->getMessage();
        }
    }
    ?>
</body>
</html>
