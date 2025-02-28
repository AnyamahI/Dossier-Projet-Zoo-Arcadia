<?php
$host = "smtp.gmail.com";
$port = 587;

echo "🔍 Test de connexion SMTP avec fsockopen()...<br>";

$connection = fsockopen($host, $port, $errno, $errstr, 10);

if (!$connection) {
    echo "❌ Échec de connexion à Gmail SMTP : $errstr ($errno)<br>";
} else {
    echo "✅ Connexion SMTP réussie à Gmail !<br>";
    fclose($connection);
}
?>
