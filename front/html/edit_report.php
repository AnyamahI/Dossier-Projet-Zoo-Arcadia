<?php
session_start();
require_once __DIR__ . '/../../lib/session.php';
require_once __DIR__ . '/../../lib/pdo.php';

if (!isVeterinaire()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';
$report = null;

// Vérification de l'ID du rapport
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_reports.php');
    exit;
}

$id = (int)$_GET['id'];

// Récupérer les informations du rapport
try {
    $query = $pdo->prepare("
        SELECT * 
        FROM vet_reports 
        WHERE id = :id
    ");
    $query->execute([':id' => $id]);
    $report = $query->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        $error = "Rapport introuvable.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération du rapport : " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $state = $_POST['state'] ?? '';
    $food = $_POST['food'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $visit_date = $_POST['visit_date'] ?? '';
    $details = $_POST['details'] ?? '';

    if (empty($state) || empty($food) || empty($weight) || empty($visit_date)) {
        $error = 'Tous les champs sauf les détails sont obligatoires.';
    } else {
        try {
            $query = $pdo->prepare("
                UPDATE vet_reports
                SET state = :state, food = :food, weight = :weight, visit_date = :visit_date, details = :details
                WHERE id = :id
            ");
            $query->execute([
                ':state' => $state,
                ':food' => $food,
                ':weight' => $weight,
                ':visit_date' => $visit_date,
                ':details' => $details,
                ':id' => $id
            ]);

            $success = "Rapport modifié avec succès.";
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour du rapport : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Rapport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Modifier un Rapport</h1>
    </header>
    <main class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($report): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="state" class="form-label">État de l’animal</label>
                    <input type="text" name="state" id="state" class="form-control" value="<?= htmlspecialchars($report['state']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="food" class="form-label">Nourriture proposée</label>
                    <input type="text" name="food" id="food" class="form-control" value="<?= htmlspecialchars($report['food']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="weight" class="form-label">Grammage (en grammes)</label>
                    <input type="number" name="weight" id="weight" class="form-control" value="<?= htmlspecialchars($report['weight']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="visit_date" class="form-label">Date de passage</label>
                    <input type="date" name="visit_date" id="visit_date" class="form-control" value="<?= htmlspecialchars($report['visit_date']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="details" class="form-label">Détails (facultatif)</label>
                    <textarea name="details" id="details" class="form-control"><?= htmlspecialchars($report['details']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Modifier le rapport</button>
                <a href="manage_reports.php" class="btn btn-secondary">Retour</a>
            </form>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>