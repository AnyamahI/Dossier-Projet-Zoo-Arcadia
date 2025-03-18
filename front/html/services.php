<?php
require '../../lib/pdo.php'; // Connexion à la base de données
require __DIR__ . '/../../templates/header.html';
$query = $pdo->query("SELECT * FROM services ORDER BY id ASC");
$services = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<body>

    <section class="sec1 text-center text-white d-flex align-items-center justify-content-center"
        style="background: url('/média/service-BG.png') center/cover no-repeat; height: 80vh;">
        <div class="shap-divider position-absolute bottom-0 w-100">
            <img src="/média/shap_divider_vert.png" class="img-fluid" alt="Shape Divider">
        </div>
        <h1 class="display-1 fw-light" style="font-family: 'Ryman Eco', sans-serif">Les services d'ARCADIA</h1>
    </section>

    <section class="sec2 py-5" style="background-color: #036759; color: #fffff0">
        <div class="container">
            <div class="row justify-content-center gap-4">
                <?php foreach ($services as $index => $service): ?>
                    <div class="col-12 w-80 d-flex flex-column flex-md-row align-items-center gap-4 <?= $index % 2 != 0 ? 'flex-md-row-reverse' : '' ?>">
                        <img src="<?= !empty($service['image']) ? htmlspecialchars($service['image']) : '/média/default-service.png' ?>"
                            class="img-fluid"
                            alt="<?= htmlspecialchars($service['name']) ?>"
                            style="width: 30%;">
                        <p class="fs-4"><?= htmlspecialchars($service['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php require __DIR__ . '/../../templates/footer.php'; ?>

</body>

</html>