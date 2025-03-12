<?php
session_start();
require '../../lib/pdo.php'; // Connexion à la base de données

// Vérifier si la requête est bien un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $review = trim($_POST['review'] ?? '');

    // Vérifier que les champs sont remplis
    if (empty($pseudo) || empty($review)) {
        $_SESSION['error'] = "Tous les champs sont requis.";
    } elseif (strlen($pseudo) > 50 || strlen($review) > 500) {
        $_SESSION['error'] = "Pseudo ou avis trop long.";
    } else {
        try {
            // Préparation de l'insertion
            $query = $pdo->prepare("INSERT INTO visitor_reviews (pseudo, review, is_validated, created_at) VALUES (:pseudo, :review, 0, NOW())");
            $query->execute([
                ':pseudo' => htmlspecialchars($pseudo),
                ':review' => htmlspecialchars($review)
            ]);

            $_SESSION['success'] = "Votre avis a été soumis avec succès. Il sera visible après validation.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de l'envoi de l'avis : " . $e->getMessage();
        }
    }
}

// Rediriger vers la page d'accueil
header('Location: ../../index.php');
exit;
