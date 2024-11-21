<?php
include 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $animal_id = $_POST['animal_id'];
    $health_status = $_POST['health_status'];
    $food = $_POST['food'];
    $food_quantity = $_POST['food_quantity'];
    $visit_date = $_POST['visit_date'];
    $health_details = $_POST['health_details'];

    // Valider et insérer les données dans la base de données
    $stmt = $pdo->prepare("INSERT INTO veterinary_records (animal_id, health_status, food, food_quantity, visit_date, health_details) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$animal_id, $health_status, $food, $food_quantity, $visit_date, $health_details]);

    // Rediriger vers le tableau de bord après l'enregistrement
    header('Location: veterinary_dashboard.php');
    exit;
}
?>
