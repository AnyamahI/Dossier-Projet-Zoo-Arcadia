<?php
require '../../templates/header.html';
session_start();
require '../../lib/session.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}
?>
<header class="bg-dark text-white text-center py-3">
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']['name']); ?> !</h1>
    <a href="/logout.php" class="btn btn-danger">Se déconnecter</a>
</header>

<main class="container mt-4">
    <div class="row">
        <!-- Gestion des utilisateurs -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card p-3 shadow-sm">
                <h5>Gestion des utilisateurs</h5>
                <p>Créer, modifier ou supprimer des utilisateurs.</p>
                <a href="/front/html/manage_user.php" class="btn btn-primary w-100">Accéder</a>
            </div>
        </div>

        <!-- Gestion des habitats -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card p-3 shadow-sm">
                <h5>Gestion des habitats</h5>
                <p>Ajouter, modifier ou supprimer des habitats.</p>
                <a href="/front/html/manage_habitats.php" class="btn btn-primary w-100">Accéder</a>
            </div>
        </div>

        <!-- Gestion des services -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card p-3 shadow-sm">
                <h5>Gestion des services</h5>
                <p>Gérer les services du zoo.</p>
                <a href="/front/html/manage_services.php" class="btn btn-primary w-100">Accéder</a>
            </div>
        </div>

        <!-- Gestion des animaux -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card p-3 shadow-sm">
                <h5>Gestion des animaux</h5>
                <p>Ajouter, modifier ou supprimer des animaux.</p>
                <a href="/front/html/manage_animals.php" class="btn btn-primary w-100">Accéder</a>
            </div>
        </div>

        <!-- Consultation des rapports -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card p-3 shadow-sm">
                <h5>Consultation des rapports</h5>
                <p>Consulter les rapports des vétérinaires.</p>
                <a href="/front/html/manage_reports.php" class="btn btn-primary w-100">Accéder</a>
            </div>
        </div>

        <!-- Gestion des avis -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card p-3 shadow-sm">
                <h5>Gestion des avis</h5>
                <p>Modérer et valider les avis des visiteurs.</p>
                <a href="/front/html/manage_reviews.php" class="btn btn-primary w-100">Accéder</a>
            </div>
        </div>
    </div>
</main>