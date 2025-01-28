<?php
session_start();
require '../../lib/session.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Administrateur</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>

<body>
    <header>
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']['name']); ?> !</h1>
        <a href="../logou.php" class="logout-btn">Se déconnecter</a>
    </header>
    <main>
        <section>
            <h2>Gestion des utilisateurs</h2>
            <a href="/front/html/manage_user.php" class="btn">Créer/Modifier/Supprimer des utilisateurs</a>
        </section>
        <section>
            <h2>Gestion des habitats</h2>
            <a href="/front/html/manage_habitats.php" class="btn">Ajouter/Modifier/Supprimer des habitats</a>
        </section>
        <section>
            <h2>Gestion des services</h2>
            <a href="manage_services.php" class="btn">Gérer les services du zoo</a>
        </section>
        <section>
            <h2>Consultation des rapports</h2>
            <a href="view_reports.php" class="btn">Consulter les rapports des vétérinaires</a>
        </section>
    </main>
</body>

</html>