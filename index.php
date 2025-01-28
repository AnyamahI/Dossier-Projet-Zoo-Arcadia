<?php require_once __DIR__ . '/templates/header.html';
$password = 'test'; // Votre mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
var_dump($hashedPassword); // Affiche le hachage

?>

<section class="sec1">
    <h1> ARCADIA </h1>
    <h2>Zoo de Broceliand </h2>
    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/média/média/flamant rose.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="/média/média/loup_acceuil-unsplash.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="/média/média/loup_acceuil-unsplash.jpg" class="d-block w-100" alt="...">
            </div>
        </div>
        <div class="shap-divider">
            <img src="/média/shap_divider_blanc.png" alt="">
        </div>
    </div>
</section>

<section class="sec2">

    <div class="container-fluide">

        <div class="shap-divider">
            <img src="/média/média/shap_divider_vert.png" alt="">
        </div>

        <div class="container-info">

            <div class="horaires">
                <i class="fa-solid fa-clock fa-xl"></i>
                <p>Zoo ouvert toute <br>l’année de 9h à 19h </p>
            </div>
            <div class="guide">
                <i class="fa-solid fa-users fa-xl"></i>
                Visite guider du Zoo <br>Gratuit
            </div>
            <div class="petit-train">
                <i class="fa-solid fa-train fa-xl"></i>
                Départ du petit train <br>toues les 30min
            </div>
            <div class="restauration">
                <i class="fa-solid fa-utensils fa-xl"></i>
                Restaurant ouverture:<br>11h30 à 18h
            </div>
            <div class="adresse">
                <i class="fa-solid fa-map-pin fa-xl"></i>
                6 Forêt de Roi Arthur <br>17570 Brocéliande
            </div>
        </div>

        <div class="container-intro">
            <div class="photo-into"></div>
            <img src="/média/média/furret-removebg.png" alt="">

            <div class="intro-zoo">
                Venez explorer le Zoo Arcadia, niché au cœur de la légendaire forêt de Brocéliande. Plongez dans un monde fascinant où la magie de la nat ure rencontre la beauté des animaux,
                offrant une aventure inoubliable pour toute la famille !
            </div>
        </div>

    </div>
</section>

<section class="sec3">

    <h1>Les Territoires D'Arcadia</h1>
    <div class="container-savane">

        <div class="savane-blop">
            <a href="/front/html/habitats.php">
                <img src="/média/média/Blop-Savane.png" alt="">
            </a>
        </div>
        <div class="savane-text">
            <h2>La Savane</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Sint pariatur atque sunt laborum et dolore vitae provident
                deleniti nam odit quod nihil, eveniet ullam natus expedita, vero deserunt mollitia autem!
                Eligendi sit voluptatum praesentium ipsam. Autem maiores illum est
                facilis corrupti! Vitae corrupti atque autem architecto, maiores ipsum eaque labore.
            </p>
        </div>
    </div>

    <div class="container-jungle">

        <div class="jungle-blop">
            <img src="/média/média/Blop-Jungle.png" alt="">
        </div>
        <div class="jungle-text">
            <h2>La Savane</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Sint pariatur atque sunt laborum et dolore vitae provident
                deleniti nam odit quod nihil, eveniet ullam natus expedita, vero deserunt mollitia autem!
                Eligendi sit voluptatum praesentium ipsam. Autem maiores illum est
                facilis corrupti! Vitae corrupti atque autem architecto, maiores ipsum eaque labore.
            </p>
        </div>
    </div>

    <div class="container-marais">

        <div class="marais-blop">
            <img src="/média/média/Blop-Marais.png" alt="">
        </div>
        <div class="marais-text">
            <h2>La Savane</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Sint pariatur atque sunt laborum et dolore vitae provident
                deleniti nam odit quod nihil, eveniet ullam natus expedita, vero deserunt mollitia autem!
                Eligendi sit voluptatum praesentium ipsam. Autem maiores illum est
                facilis corrupti! Vitae corrupti atque autem architecto, maiores ipsum eaque labore.
            </p>
        </div>
    </div>
</section>

<section class="sec4">
    <h1>Nos Animaux</h1>
    <div class="container-animaux">
        <div class="container-tigre_loup">
            <img class="tigre" src="/média/média/Tigre_acceuil-unsplash.jpg" alt="">
            <img class="loup" src="/média/loup_acceuil-unsplash.jpg" alt="">
        </div>
        <div class="container-zebre_goril_loutre">
            <img class="zebre" src="/média/média/zebre-acceuil-unsplash.jpg " alt="">
            <img class="goril" src="/média/média/goril-acceuil-unsplash.jpg" alt="">
            <img class="loutre" src="/média/média ani/loutre-unsplash.jpg" alt="">
        </div>
    </div>
    </div>
    <h3>Plus d'animaux...</h3>
</section>

<section class="sec5">
    <h2>Partagez votre experance</h2>

    <div class="avis">
        <img src="/média/média/avis-prototype.png" alt="">
    </div>

    <div class="shap-divider">
        <img src="/média/média/shap_divider_vert.png" alt="">
    </div>

</section>

<section class="sec6">
    <h2>Les partenair du Zoo</h2>
    <div class="partenaire">
        <img class="logo_BornFree" src="/média/média/logo-Born-free.png" alt="">
        <img class="logo_SPA" src="/média/média/logo-SPA.png" alt="">
        <img class="logo_WWF" src="/média/média/Panda_contours_only.png" alt="">
        <img class="logo_AFDPZ" src="/média/média/AFDPZ_logo_bg_vide.png" alt="">
    </div>
</section>

<div class="contact" id="contact"></div>
<h2 class="title-category">Partagez nous votres expérience</h2>
<div class="form-contact-container">
    <form action="contact.php" method="post" class="form-contact">
        <div class="form-top">
            <div class="form-left">
                <div><label for="prenom">Prénom</label>
                    <input type="text" name="prenom" placeholder="arcadia">
                </div>
                <div><label for="mail">E-mail</label>
                    <input type="text" name="mail" placeholder="arcadia@mail.fr">
                </div>
            </div>
            <div class="form-right">
                <label for="message">Message</label>
                <textarea name="message" cols="30" rows="5" placeholder="Je laisse mon avis sur le zoo..."></textarea>
            </div>
        </div>
        <div class="form-bottom">
            <input type="submit" name="envoyer" value="Envoyer">
        </div>
    </form>
    <?php include('back/php/contact.php'); ?>

</div>


<?php

$user = "root";
$pwd = "";
$db = "mysql:host=localhost;dbname=commentaire;charset=utf8;port=3307";

try {
    $cx = new PDO($db, $user, $pwd);
    $cx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT nom, commentaire, date FROM commentaires ORDER BY date DESC";
    $stmt = $cx->query($sql);

    echo "<h2 class='title-category'>Avis des visiteurs</h2>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div class='commentaire'>";
        echo "<img src='/média/profile.png' class='commentaire-pp'>";
        echo "<div class='commentaire-text'>";
        echo "<h3 class='commentaire-nom'>" . htmlspecialchars($row['nom']) . "</h3>";
        echo "<p class='commentaire-message'>" . htmlspecialchars($row['commentaire']) . "</p>";
        echo "<small>Posté le " . htmlspecialchars($row['date']) . "</small>";
        echo "</div></div>";
    }
} catch (PDOException $e) {
    echo "Une erreur est survenue lors de la connexion : " . $e->getMessage();
}

?>
</div>
<?php require_once __DIR__ . '/templates/footer.php'; ?>
</body>