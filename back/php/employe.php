<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Employé - Validation des avis</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Validation des avis</h1>
    <div class="avis-container">
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

        $sql = "SELECT * FROM avis WHERE statut_validation = 0";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='avis'>";
                echo "<p><strong>Pseudo:</strong> " . $row["pseudo"]. "</p>";
                echo "<p><strong>Avis:</strong> " . $row["avis"]. "</p>";
                echo "<a href='validate_avis.php?id=" . $row["id"] . "' class='btn'>Valider</a>";
                echo "</div>";
            }
        } else {
            echo "<p>Aucun avis en attente de validation.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
