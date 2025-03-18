<?php
use PHPUnit\Framework\TestCase;

class UserSimulationTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        // ✅ Connexion à la base de données de test
        $this->pdo = new PDO('mysql:host=localhost;dbname=zoo_arcadia;charset=utf8;port=3307', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // ✅ Test 1 : Simulation de plusieurs utilisateurs postant des avis
    public function testMultipleUsersPostReviews()
    {
        $users = [
            ['pseudo' => 'Alice', 'review' => 'Le zoo est incroyable !'],
            ['pseudo' => 'Bob', 'review' => 'J’ai adoré la visite !'],
            ['pseudo' => 'Charlie', 'review' => 'Les animaux ont l’air en bonne santé.']
        ];

        foreach ($users as $user) {
            $_POST = [
                'pseudo' => $user['pseudo'],
                'review' => $user['review']
            ];

            ob_start(); // ✅ Empêche l'affichage HTML
            require dirname(__DIR__) . '/front/html/submit_review.php';
            ob_end_clean(); // ✅ Nettoyer la sortie même en cas d'erreur

            // ✅ Vérifie que l'avis a bien été inséré
            $query = $this->pdo->prepare("SELECT * FROM visitor_reviews WHERE pseudo = :pseudo");
            $query->execute([':pseudo' => $user['pseudo']]);
            $review = $query->fetch(PDO::FETCH_ASSOC);

            $this->assertNotFalse($review, "L'avis de {$user['pseudo']} n'a pas été trouvé.");
            $this->assertEquals($user['review'], $review['review']);
        }
    }

    // ✅ Test 2 : Simulation de l'affichage des avis validés
    public function testDisplayValidatedReviews()
    {
        $query = $this->pdo->query("SELECT pseudo, review FROM visitor_reviews WHERE is_validated = 1 ORDER BY created_at DESC LIMIT 5");
        $reviews = $query->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        require dirname(__DIR__) . '/front/html/view_review.php';
        $output = ob_get_clean();

        foreach ($reviews as $review) {
            $this->assertStringContainsString(htmlspecialchars($review['pseudo']), $output);
            $this->assertStringContainsString(htmlspecialchars($review['review']), $output);
        }
    }

    // ✅ Test 3 : Simulation d’un admin validant un avis
    public function testAdminValidatesReview()
    {
        // ✅ Récupérer un avis en attente de validation
        $query = $this->pdo->query("SELECT id FROM visitor_reviews WHERE is_validated = 0 LIMIT 1");
        $review = $query->fetch(PDO::FETCH_ASSOC);

        if (!$review) {
            $this->markTestSkipped("Aucun avis à valider.");
        }

        $reviewId = $review['id'];
        $_GET['validate'] = $reviewId;

        ob_start();
        require dirname(__DIR__) . '/front/html/manage_reviews.php';
        ob_end_clean();

        // ✅ Vérifie que l'avis est bien validé
        $query = $this->pdo->prepare("SELECT is_validated FROM visitor_reviews WHERE id = :id");
        $query->execute([':id' => $reviewId]);
        $updatedReview = $query->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(1, $updatedReview['is_validated'], "L'avis n'a pas été validé.");
    }

    // ✅ Test 4 : Simulation d’un admin supprimant un avis
    public function testAdminDeletesReview()
    {
        // ✅ Ajouter un avis pour le test
        $query = $this->pdo->prepare("INSERT INTO visitor_reviews (pseudo, review, is_validated, created_at) VALUES ('TestDelete', 'Cet avis va être supprimé.', 1, NOW())");
        $query->execute();

        // ✅ Récupérer l'ID de l'avis
        $reviewId = $this->pdo->lastInsertId();
        $_GET['delete'] = $reviewId;

        ob_start();
        require dirname(__DIR__) . '/front/html/manage_reviews.php';
        ob_end_clean();

        // ✅ Vérifie que l'avis est supprimé
        $query = $this->pdo->prepare("SELECT * FROM visitor_reviews WHERE id = :id");
        $query->execute([':id' => $reviewId]);
        $review = $query->fetch(PDO::FETCH_ASSOC);

        $this->assertFalse($review, "L'avis n'a pas été supprimé.");
    }
}
