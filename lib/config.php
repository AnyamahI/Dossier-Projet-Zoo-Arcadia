<?php
$redisUrl = getenv('REDIS_URL'); // Récupère l'URL de Redis depuis Heroku

if ($redisUrl) {
    $parsedUrl = parse_url($redisUrl);

    $host = $parsedUrl['host'];
    $port = $parsedUrl['port'];
    $password = isset($parsedUrl['pass']) ? $parsedUrl['pass'] : null;

    $redis->connect($host, $port);

    if ($password) {
        $redis->auth($password);
    }
} else {
    die("❌ Erreur : Impossible de récupérer l'URL de Redis.");
}
