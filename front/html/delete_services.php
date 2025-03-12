<?php
session_start();
require '../../lib/session.php';
require '../../lib/pdo.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';

// Vérifiez si un ID est fourni dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_services.php');
    exit;
}

$id = $_GET['id'];

// Vérifiez si l'utilisateur a confirmé la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        try {
            // Supprimer le service de la base de données
            $query = $pdo->prepare("DELETE FROM services WHERE id = :id");
            $query->execute([':id' => $id]);

            // Rediriger après suppression
            $_SESSION['success'] = "Le service a été supprimé avec succès.";
            header('Location: manage_services.php');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la suppression du service : " . $e->getMessage();
        }
    } else {
        // Annuler la suppression
        header('Location: manage_services.php');
        exit;
    }
}

// Récupérer le service à supprimer pour affichage
try {
    $query = $pdo->prepare("SELECT name FROM services WHERE id = :id");
    $query->execute([':id' => $id]);
    $service = $query->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        header('Location: manage_services.php');
        exit;
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération du service : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Supprimer un Service</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <h3 class="text-danger">Êtes-vous sûr de vouloir supprimer ce service ?</h3>
            <p><strong>Nom du service :</strong> <?= htmlspecialchars($service['name']) ?></p>
            <form method="POST" class="d-flex gap-3">
                <button type="submit" name="confirm" value="yes" class="btn btn-danger">Oui, supprimer</button>
                <a href="manage_services.php" class="btn btn-secondary">Annuler</a>
            </form>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>