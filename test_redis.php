<?php
// Connexion à Redis
$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379); // Localhost et port 6379
    echo "✅ Connexion à Redis réussie !<br>";

    // Test de stockage d'une valeur
    $redis->set("test_key", "Hello Redis !");
    echo "✅ Valeur enregistrée dans Redis : " . $redis->get("test_key");
} catch (Exception $e) {
    echo "❌ Erreur de connexion à Redis : " . $e->getMessage();
}
?>
