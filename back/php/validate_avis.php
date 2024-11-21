<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zoo_arcadia";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql = "UPDATE avis SET statut_validation = 1 WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Avis validé avec succès !";
} else {
    echo "Erreur : " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
