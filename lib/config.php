<?php
$redisUrl = getenv('REDIS_URL'); // Récupère l'URL de Redis depuis Heroku

if ($redisUrl) {
    $parsedUrl = parse_url($redisUrl);

    $host = $parsedUrl['host'] ?? '127.0.0.1';
    $port = $parsedUrl['port'] ?? 6379;
    $password = $parsedUrl['pass'] ?? null;

    $redis = new Redis(); // ← Cette ligne manquait !

    try {
        $redis->connect($host, $port, 2.5); // timeout de 2.5s

        if ($password) {
            $redis->auth($password);
        }

        error_log("✅ Connexion Redis réussie !");
    } catch (Exception $e) {
        error_log("❌ Erreur de connexion à Redis : " . $e->getMessage());
        $redis = null; // Ne pas bloquer tout le site si Redis échoue
    }
} else {
    error_log("REDIS_URL non défini. Redis désactivé.");
    $redis = null;
}
