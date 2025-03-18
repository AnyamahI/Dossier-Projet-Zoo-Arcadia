<?php
require_once dirname(__DIR__) . '/lib/pdo.php';
require_once dirname(__DIR__) . '/lib/session.php';

use PHPUnit\Framework\TestCase;

class FormValidationTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        // ✅ Connexion à la base de données de test
        $this->pdo = new PDO('mysql:host=localhost;dbname=zoo_arcadia;charset=utf8;port=3307', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // ✅ Test 1 : Soumission d’un avis
    public function testSubmitReview()
    {
        // Simuler l'envoi d'un avis par un utilisateur
        $_POST = [
            'pseudo' => 'TestUser',
            'review' => 'Ceci est un test'
        ];

        ob_start(); // ✅ Empêcher l'affichage du HTML
        require_once __DIR__ . '/../front/html/submit_review.php';
        ob_get_clean(); // ✅ Nettoyer la sortie

        $this->assertTrue(true); // Ajoute une assertion pour éviter le statut "risky"

        // Vérifier que l'avis a été inséré en base de données
        $query = $this->pdo->prepare("SELECT * FROM visitor_reviews WHERE pseudo = :pseudo");
        $query->execute([':pseudo' => 'TestUser']);
        $review = $query->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($review, "L'avis n'a pas été trouvé en base.");
        $this->assertEquals('TestUser', $review['pseudo']);
        $this->assertEquals('Ceci est un test', $review['review']);
    }

    // ✅ Test 2 : Validation d’un avis
    public function testValidateReview()
    {
        // Trouver un avis non validé
        $query = $this->pdo->query("SELECT id FROM visitor_reviews WHERE is_validated = 0 LIMIT 1");
        $review = $query->fetch(PDO::FETCH_ASSOC);

        if (!$review) {
            $this->markTestSkipped("Aucun avis en attente de validation.");
        }

        $reviewId = $review['id'];
        $_GET['validate'] = $reviewId;

        ob_start();
        require __DIR__ . '/../front/html/manage_reviews.php';
        ob_get_clean();

        // Vérifier que l'avis est bien marqué comme validé
        $query = $this->pdo->prepare("SELECT is_validated FROM visitor_reviews WHERE id = :id");
        $query->execute([':id' => $reviewId]);
        $updatedReview = $query->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(1, $updatedReview['is_validated'], "L'avis n'a pas été validé.");
    }

    // ✅ Test 3 : Vérification du carrousel d'avis
    public function testCarouselReviews()
    {
        // Récupérer les avis validés
        $query = $this->pdo->query("SELECT pseudo, review FROM visitor_reviews WHERE is_validated = 1 ORDER BY created_at DESC LIMIT 5");
        $reviews = $query->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        require __DIR__ . '/../front/html/view_review.php';
        $output = ob_get_clean();

        // Vérifier si les avis sont affichés correctement dans le carrousel
        foreach ($reviews as $review) {
            $this->assertStringContainsString(htmlspecialchars($review['pseudo']), $output);
            $this->assertStringContainsString(htmlspecialchars($review['review']), $output);
        }
    }
}
