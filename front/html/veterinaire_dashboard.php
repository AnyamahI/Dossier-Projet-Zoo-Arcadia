<?php
session_start();
require '../../lib/session.php';

if (!isVeterinaire()) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Vétérinaire</title>
    <link rel="stylesheet" href="../css/veterinaire_dashboard.css">
</head>

<body>
    <header>
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']['name']); ?> !</h1>
        <a href="../logou.php">Se déconnecter</a>
    </header>
    <main>
        <section>
            <h2>Mes Animaux</h2>
            <a href="view_animals.php">Consulter les animaux</a>
        </section>
        <section>
            <h2>Ajouter un Rapport</h2>
            <a href="add_report.php">Créer un nouveau rapport</a>
        </section>
        <section>
            <h2>Consulter les Rapports</h2>
            <a href="view_reports.php">Voir tous les rapports</a>
        </section>
    </main>
</body>

</html>