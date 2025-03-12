<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isEmployee()) {
    header('Location: ../login.php');
    exit;
}

// Vérifier si l'email est bien défini en session
if (!isset($_SESSION['user']['email'])) {
    die("Erreur : L'email de l'utilisateur n'est pas défini en session.");
}

$email = $_SESSION['user']['email'];

// Récupérer le nom de l'employé depuis la base de données
$query = $pdo->prepare("SELECT id, email, name FROM users WHERE email = :email");
$query->execute([':email' => $email]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user']['name'] = $user['name'] ?? 'Employé'; // Valeur par défaut si NULL
}

// Vérifier si le nom est bien défini après récupération
$employee_name = $_SESSION['user']['name'] ?? 'Employé';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Employé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Bienvenue, <?= htmlspecialchars($employee_name) ?> !</h1>
        <a href="/logout.php" class="btn btn-danger">Se déconnecter</a>
    </header>
    <main class="container mt-4">
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card p-3">
                    <h5>Liste des animaux</h5>
                    <p>Consultez les animaux du zoo.</p>
                    <a href="manage_animals.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card p-3">
                    <h5>Services</h5>
                    <p>Consultez les services disponibles.</p>
                    <a href="manage_services.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
