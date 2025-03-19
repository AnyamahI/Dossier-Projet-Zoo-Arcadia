<?php
$redisUrl = getenv('REDIS_URL') ?: getenv('REDIS');

if ($redisUrl) {
    $parsedUrl = parse_url($redisUrl);
    
    try {
        // Vérifier si c'est du "rediss://" et activer TLS si nécessaire
        $useTls = strpos($redisUrl, 'rediss://') === 0;

        if ($useTls) {
            $redis->connect($parsedUrl['host'], $parsedUrl['port'], 2.5, NULL, 0, 0, ['stream_context' => stream_context_create(['ssl' => ['verify_peer_name' => false, 'verify_peer' => false]])]);
        } else {
            $redis->connect($parsedUrl['host'], $parsedUrl['port']);
        }

        // Vérifie si un mot de passe est requis
        if (!empty($parsedUrl['pass'])) {
            $redis->auth($parsedUrl['pass']);
        }

        // Test de connexion
        if ($redis->ping()) {
            echo "✅ Redis connecté avec succès";
        }
    } catch (Exception $e) {
        die("❌ Erreur de connexion à Redis : " . $e->getMessage());
    }
} else {
    die("❌ REDIS_URL non trouvée dans les variables d’environnement !");
}
?>
