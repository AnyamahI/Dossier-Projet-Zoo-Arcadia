<?php
require '../../lib/pdo.php';

$habitatId = $_GET['id'] ?? null;

if (!$habitatId) {
    echo "<p class='text-danger'>Erreur : ID d'habitat manquant.</p>";
    exit;
}

// Infos de l'habitat
$queryHabitat = $pdo->prepare("SELECT name, description FROM habitats WHERE id = :id");
$queryHabitat->execute([':id' => $habitatId]);
$habitat = $queryHabitat->fetch(PDO::FETCH_ASSOC);

if (!$habitat) {
    echo "<p class='text-danger'>Aucune information trouvée pour cet habitat.</p>";
    exit;
}

// Animaux avec images
$queryAnimals = $pdo->prepare("SELECT name, image FROM animals WHERE habitat_id = :id");
$queryAnimals->execute([':id' => $habitatId]);
$animals = $queryAnimals->fetchAll(PDO::FETCH_ASSOC);

$habitatName = htmlspecialchars($habitat['name']);
$habitatDescription = htmlspecialchars($habitat['description']);

echo "<p>$habitatDescription</p>";

echo "<p>Animaux présents :</p>";
if ($animals) {
    echo "<div class='row'>";
    foreach ($animals as $animal) {
        $animalName = htmlspecialchars($animal['name']);
        $animalImage = htmlspecialchars($animal['image']);

        echo "<div class='col-md-4 col-sm-6 col-12 mb-4'>";
        echo "<div class='card bg-transparent border-0'>"; // Fond transparent, aucune bordure
        if (!empty($animalImage)) {
            echo "<img src='" . $animalImage . "' class='card-img-top img-fluid rounded' alt='" . $animalName . "' style='width:80%; display: block;margin: 0 auto;'>";
        }
        echo "<div class='card-body p-2'>";
        echo "<p class='card-title text-center' style='color: #FFFFF0;'>" . $animalName . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p>Aucun animal enregistré dans cet habitat.</p>";
}
