<?php
$redisUrl = getenv('REDIS_URL') ?: getenv('REDIS');
$redis = null; // Assure qu'il est bien défini avant d'essayer de l'utiliser

if ($redisUrl) {
    $parsedUrl = parse_url($redisUrl);
    $redis = new Redis();

    try {
        $useTls = strpos($redisUrl, 'rediss://') === 0;

        if ($useTls) {
            $redis->connect(
                $parsedUrl['host'],
                $parsedUrl['port'],
                2.5, // Timeout
                NULL,
                0,
                0,
                ['stream_context' => stream_context_create(['ssl' => ['verify_peer_name' => false, 'verify_peer' => false]])]
            );
        } else {
            $redis->connect($parsedUrl['host'], $parsedUrl['port']);
        }

        // Vérifier si un mot de passe est requis et s'authentifier
        if (!empty($parsedUrl['pass'])) {
            $redis->auth($parsedUrl['pass']);
        }

        // Vérifier la connexion
        if ($redis->ping()) {
            error_log("✅ Redis connecté avec succès");
        }
    } catch (Exception $e) {
        error_log("❌ Erreur de connexion à Redis : " . $e->getMessage());
        $redis = null; // Assure que Redis est bien null en cas d'erreur
    }
} else {
    error_log("❌ REDIS_URL non trouvée dans les variables d’environnement !");
    $redis = null;
}
?>
