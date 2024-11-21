<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars(trim($_POST["nom"]));
    $commentaire = htmlspecialchars(trim($_POST["commentaire"]));

    $user = "root";
    $pwd = ""; // Mot de passe vide ou mdp emplyer, admin, veterinaire
    $db = "mysql:host=localhost;dbname=zoo_arcadia";

    try {
        $cx = new PDO($db, $user, $pwd);
        $cx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insertion des données dans la base
        $sql = "INSERT INTO commentaires (nom, commentaire) VALUES (:nom, :commentaire)";
        $stmt = $cx->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->execute();
        echo "Votre avis a été soumis pour validation.";
    } catch (PDOException $e) {
        echo "Une erreur est survenue lors de la connexion : " . $e->getMessage();
    }
}
?>
