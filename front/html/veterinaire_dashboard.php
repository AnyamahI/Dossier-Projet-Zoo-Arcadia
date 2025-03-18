<?php
session_start();
require_once __DIR__ . '/../../lib/session.php';
require_once __DIR__ . '/../../lib/pdo.php'; // Assure-toi d'inclure PDO

// Vérifier si l'utilisateur est vétérinaire
if (!isVeterinaire()) {
    header('Location: ../login.php');
    exit;
}

// Vérifier si l'email est bien défini dans la session
if (!isset($_SESSION['user']['email'])) {
    die("Erreur : l'email de l'utilisateur n'est pas défini en session.");
}

$email = $_SESSION['user']['email'];

// Récupérer les informations de l'utilisateur
$query = $pdo->prepare("SELECT id, email, name, role FROM users WHERE email = :email");
$query->execute([':email' => $email]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user']['name'] = $user['name']; // Mettre à jour la session avec le nom
}

// Vérifier si le nom est bien défini après la récupération
if (empty($_SESSION['user']['name'])) {
    $_SESSION['user']['name'] = "Vétérinaire"; // Valeur par défaut si le nom est NULL
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord vétérinaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user']['name']) ?> !</h1>
        <a href="/logout.php" class="btn btn-danger">Se déconnecter</a>
    </header>
    <main class="container mt-4">
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card p-3">
                    <h5>Ajouter un rapport</h5>
                    <p>Ajoutez un nouveau rapport vétérinaire pour un animal.</p>
                    <a href="add_report.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card p-3">
                    <h5>Gérer les rapports</h5>
                    <p>Consultez, modifiez ou supprimez vos rapports vétérinaires.</p>
                    <a href="manage_reports.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card p-3">
                    <h5>Consulter les animaux</h5>
                    <p>Consultez les informations des animaux.</p>
                    <a href="/front/html/manage_animals.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>