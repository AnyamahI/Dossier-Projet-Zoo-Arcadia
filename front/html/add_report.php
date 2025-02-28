<?php
// Activer l'affichage des erreurs pour le débogage
session_start();
require_once __DIR__ . '/../../lib/session.php';
require_once __DIR__ . '/../../lib/pdo.php';

// Vérification du rôle
if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
    die("Accès refusé. Les administrateurs ne peuvent pas ajouter de rapports.");
}



$error = '';
$success = '';

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $animal_id = $_POST['animal_id'] ?? '';
    $state = trim($_POST['state'] ?? '');
    $food = trim($_POST['food'] ?? '');
    $weight = $_POST['weight'] ?? '';
    $visit_date = $_POST['visit_date'] ?? '';
    $details = trim($_POST['details'] ?? '');
    $vet_id = $_SESSION['user']['id']; // ID du vétérinaire connecté

    // Vérifier que tous les champs obligatoires sont remplis
    if (empty($animal_id) || empty($state) || empty($food) || empty($weight) || empty($visit_date)) {
        $error = 'Tous les champs sauf les détails sont obligatoires.';
    } else {
        try {
            // Insérer le rapport dans la base de données
            $query = $pdo->prepare("
                INSERT INTO vet_reports (animal_id, vet_id, state, food, weight, visit_date, details) 
                VALUES (:animal_id, :vet_id, :state, :food, :weight, :visit_date, :details)
            ");
            $query->execute([
                ':animal_id' => $animal_id,
                ':vet_id' => $vet_id,
                ':state' => $state,
                ':food' => $food,
                ':weight' => $weight,
                ':visit_date' => $visit_date,
                ':details' => $details
            ]);

            $success = "Rapport ajouté avec succès.";
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du rapport : " . $e->getMessage();
        }
    }
}

// Récupérer la liste des animaux
$animals = [];
try {
    $query = $pdo->query("SELECT id, name FROM animals");
    $animals = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des animaux : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Rapport Vétérinaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Ajouter un Rapport Vétérinaire</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="animal_id" class="form-label">Animal</label>
                <select name="animal_id" id="animal_id" class="form-select" required>
                    <option value="">-- Sélectionner un animal --</option>
                    <?php foreach ($animals as $animal): ?>
                        <option value="<?= htmlspecialchars($animal['id']) ?>">
                            <?= htmlspecialchars($animal['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="state" class="form-label">État de l’animal</label>
                <input type="text" name="state" id="state" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="food" class="form-label">Nourriture proposée</label>
                <input type="text" name="food" id="food" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="weight" class="form-label">Grammage (en grammes)</label>
                <input type="number" name="weight" id="weight" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="visit_date" class="form-label">Date de passage</label>
                <input type="date" name="visit_date" id="visit_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="details" class="form-label">Détails (facultatif)</label>
                <textarea name="details" id="details" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter le rapport</button>
            <a href="manage_reports.php" class="btn btn-secondary">Retour</a>
        </form>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>