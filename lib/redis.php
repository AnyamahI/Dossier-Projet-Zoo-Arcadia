<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Charger Composer

use Predis\Client;

try {
    $redis = new Client([
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379
    ]);

    // Test de connexion
    $redis->ping();

} catch (Exception $e) {
    die("❌ Erreur de connexion à Redis : " . $e->getMessage());
}
