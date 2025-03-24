<?php
$redisUrl = getenv('REDIS_URL'); // URL de Redis depuis Heroku

if ($redisUrl) {
    $parsedUrl = parse_url($redisUrl);

    $host = $parsedUrl['host'] ?? '127.0.0.1';
    $port = $parsedUrl['port'] ?? 6379;
    $password = $parsedUrl['pass'] ?? null;

    $redis = new Redis();

    try {
        $redis->connect($host, $port, 2.5); 

        if ($password) {
            $redis->auth($password);
        }

        error_log("✅ Connexion Redis réussie !");
    } catch (Exception $e) {
        error_log("❌ Erreur de connexion à Redis : " . $e->getMessage());
        $redis = null; 
    }
} else {
    error_log("REDIS_URL non défini. Redis désactivé.");
    $redis = null;
}
