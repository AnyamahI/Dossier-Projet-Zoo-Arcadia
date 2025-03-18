<?php
require_once __DIR__ . '/../../lib/pdo.php';

header('Content-Type: application/json');

// Vérifier si un ID d'animal est passé et est valide
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "ID invalide."]);
    exit;
}

$animal_id = intval($_GET['id']);

// Charger Redis uniquement après avoir validé l'ID
require_once __DIR__ . '/../../lib/redis.php';

// 🔹 Incrémenter le compteur de consultations Redis pour cet animal
$visitor_ip = $_SERVER['REMOTE_ADDR']; // Récupérer l'adresse IP du visiteur
$visit_key = "visits:animal:$animal_id:ip:$visitor_ip";

// Vérifier si l'IP a déjà visité l'animal dans la dernière minute
if (!$redis->exists($visit_key)) {
    $redis->incr("visits:animal:" . $animal_id);
    $redis->setex($visit_key, 60, 1); // Expiration après 60 secondes (1 minute)
}

try {
    // 🔹 Récupérer les informations de l'animal avec son image et l'image de l'habitat
    $query = $pdo->prepare("
        SELECT 
            a.id, a.name, a.image AS animal_image, 
            s.name AS species_name, 
            h.name AS habitat_name, h.image AS habitat_image
        FROM animals a
        LEFT JOIN species s ON a.species_id = s.id
        LEFT JOIN habitats h ON a.habitat_id = h.id
        WHERE a.id = :id
    ");
    $query->execute([':id' => $animal_id]);
    $animal = $query->fetch(PDO::FETCH_ASSOC);

    if (!$animal) {
        echo json_encode(["error" => "Animal introuvable."]);
        exit;
    }

    // 🔹 Récupérer les rapports vétérinaires
    $query = $pdo->prepare("
        SELECT r.*, u.name AS vet_name
        FROM vet_reports r
        JOIN users u ON r.vet_id = u.id
        WHERE r.animal_id = :animal_id
        ORDER BY r.visit_date DESC
    ");
    $query->execute([':animal_id' => $animal_id]);
    $reports = $query->fetchAll(PDO::FETCH_ASSOC);

    // 🔹 Envoyer la réponse JSON
    echo json_encode([
        "animal" => $animal,
        "reports" => $reports
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur de base de données : " . $e->getMessage()]);
}
