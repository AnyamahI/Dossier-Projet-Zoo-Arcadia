<?php
require_once __DIR__ . '/templates/header.html';
require 'lib/pdo.php';

$habitatSavane = [];
$habitatJungle = [];
$habitatMarais = [];
try {
    $query = $pdo->prepare("SELECT name, description FROM habitats WHERE name = 'Marais'");
    $query->execute();
    $habitatMarais = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

try {
    $query = $pdo->prepare("SELECT name, description FROM habitats WHERE name = 'Jungle'");
    $query->execute();
    $habitatJungle = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des habitats : " . $e->getMessage();
}

try {
    $query = $pdo->prepare("SELECT name, description FROM habitats WHERE name = 'Savane'");
    $query->execute();
    $habitatSavane = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des habitats : " . $e->getMessage();
}
?>


    <link rel="stylesheet" href="/front/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<!-- SECTION 1: Carrousel d'accueil -->
<section class="sec1">
    <div class="titre">
        <h1>ARCADIA</h1>
        <h2>Zoo de Brocéliande</h2>
    </div>
    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/média/média/flamant rose.jpg" class="d-block img-fluid" alt="Flamant Rose">
            </div>
            <div class="carousel-item">
                <img src="/média/média/loup_acceuil-unsplash.jpg" class="d-block img-fluid" alt="Loup">
            </div>
            <div class="carousel-item">
                <img src="/média/média/goril-acceuil-unsplash.jpg" class="d-block img-fluid" alt="Gorille">
            </div>
        </div>
    </div>
    <div class="shap-divider">
        <img src="/média/média/shap_divider_blanc.png" alt="">
    </div>
</section>

<!-- SECTION 2: Infos du Zoo -->
<section class="sec2">
    <div class="container-fluid">
        <!-- ✅ Conteneur supérieur avec infos -->
        <div class="row justify-content-center text-center container-info">
            <div class="col-6 col-md-2">
                <i class="bi bi-clock"></i>
                <p>Zoo ouvert toute l’année de 9h à 19h</p>
            </div>
            <div class="col-6 col-md-2">
                <i class="bi bi-person-walking"></i>
                <p>Visite guidée gratuite</p>
            </div>
            <div class="col-6 col-md-2">
                <i class="bi bi-train-front"></i>
                <p>Départ du petit train toutes les 30 min</p>
            </div>
            <div class="col-6 col-md-2">
                <i class="bi bi-shop"></i>
                <p>Restaurant ouvert de 11h30 à 18h</p>
            </div>
            <div class="col-6 col-md-2">
                <i class="bi bi-pin"></i>
                <p>6 Forêt de Roi Arthur, 17570 Brocéliande</p>
            </div>
        </div>

        <!-- ✅ Conteneur inférieur avec image et texte -->
        <div class="row align-items-end container-intro">
            <div class="col-md-7 text-center p-0">
                <img src="/média/média/furret-removebg.png" class="img-fluid furret-img" alt="Furret">
            </div>
            <div class="col-md-5 d-flex align-items-center justify-content-center">
                <p class="intro-zoo">
                    Venez explorer le Zoo Arcadia, niché au cœur de la légendaire forêt de Brocéliande.
                    Plongez dans un monde fascinant où la magie de la nature rencontre la beauté des animaux,
                    offrant une aventure inoubliable pour toute la famille !
                </p>
            </div>
        </div>
        <div class="shap-divider">
            <img src="/média/média/shap_divider_vert.png" alt="">
        </div>
    </div>
</section>


<!-- SECTION 3: Les territoires du zoo -->
<section class="sec3">
    <h1>Les Territoires D'Arcadia</h1>
    <div class="container-savane">
        <div class="savane-blop"><a href="/front/html/habitats.php"><img src="/média/média/Blop-Savane.png" alt=""></a></div>
        <div class="savane-text">
            <h2><?= htmlspecialchars($habitatSavane['name'])?></h2>
            <p><?= htmlspecialchars($habitatSavane['description'])?></p>
        </div>
    </div>
    <div class="container-jungle">
        <div class="jungle-blop"><img src="/média/média/Blop-Jungle.png" alt=""></div>
        <div class="jungle-text">
            <h2><?= htmlspecialchars($habitatJungle['name'])?></h2>
            <p><?= htmlspecialchars($habitatJungle['description']) ?></p>
        </div>
    </div>
    <div class="container-marais">
    <div class="marais-blop">
        <img src="/média/média/Blop-Marais.png" alt="">
    </div>
    <div class="marais-text">
        <h2><?= htmlspecialchars($habitatMarais['name']) ?></h2>
        <p><?= htmlspecialchars($habitatMarais['description']) ?></p>
    </div>
</div>
</section>

<!-- SECTION 4: Nos animaux -->
<section class="sec4">
    <h1>Nos Animaux</h1>
    <div class="container-animaux">
        <div class="container-tigre_loup">
            <img class="tigre" src="/média/média/Tigre_acceuil-unsplash.jpg" alt="Tigre">
            <img class="loup" src="/média/média/loup_acceuil-unsplash.jpg" alt="Loup">
        </div>
        <div class="container-zebre_goril_loutre">
            <img class="zebre" src="/média/média/zebre-acceuil-unsplash.jpg" alt="Zèbre">
            <img class="goril" src="/média/média/goril-acceuil-unsplash.jpg" alt="Gorille">
            <img class="loutre" src="/média/média/loutre-unsplash.jpg" alt="Loutre">
        </div>
        <button class="button-plusAnimaux" onclick="window.location.href='/front/html/animals.php'">
    Plus d'animaux...
</button>
        </div>
    </div>
    </section>

<!-- SECTION 5: Partagez votre expérience (Formulaire + Carrousel Avis) -->
<section class="sec5 py-5">
    <div class="container">
        <h2 class="text-center mb-4">Partagez votre expérience</h2>

        <div class="row justify-content-center">
            <!-- Formulaire d'ajout d'avis -->
            <div class="col-md-6">
                <div class="card p-4 shadow-sm">
                    <h4 class="text-center">Laissez un avis</h4>
                    <form action="/front/html/submit_review.php" method="post" class="avis-form">
                        <div class="mb-3">
                            <label for="pseudo" class="form-label">Votre Nom</label>
                            <input type="text" id="pseudo" name="pseudo" class="form-control" placeholder="Entrez votre nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Votre Avis</label>
                            <textarea id="review" name="review" class="form-control" placeholder="Écrivez votre avis ici..." rows="3" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Carrousel des avis -->
            <div class="col-md-6">
                <div class="card p-4 shadow-sm">
                    <h4 class="text-center">Avis des visiteurs</h4>
                    <div id="carouselAvis" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                            $query = $pdo->query("SELECT pseudo, review FROM visitor_reviews WHERE is_validated = 1 ORDER BY created_at DESC LIMIT 5");
                            $first = true;
                            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="carousel-item ' . ($first ? 'active' : '') . '">
                                        <div class="text-center">
                                            <p class="avis-text fst-italic">« ' . htmlspecialchars($row['review']) . ' »</p>
                                            <h6 class="avis-author text-primary">- ' . htmlspecialchars($row['pseudo']) . '</h6>
                                        </div>
                                      </div>';
                                $first = false;
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselAvis" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselAvis" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 6: Partenaires 
<section class="sec6">
    <h2>Nos Partenaires</h2>
    <div class="partenaire">
        <img src="/média/média/logo-Born-free.png" alt="Born Free">
        <img src="/média/média/logo-SPA.png" alt="SPA">
        <img src="/média/média/Panda_contours_only.png" alt="WWF">
        <img src="/média/média/AFDPZ_logo_bg_vide.png" alt="AFDPZ">
    </div>
</section>-->

<?php require_once __DIR__ . '/templates/footer.php'; ?>