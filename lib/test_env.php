<?php
require 'mail.php';

$to = "isacanyamah@gmail.com"; // Mets ton vrai email ici
$subject = "Test Gmail SMTP avec Variable d'Environnement";
$body = "<h1>Test Réussi 🎉</h1><p>Ton projet utilise maintenant des variables d'environnement pour le SMTP !</p>";

$test = sendEmail($to, $subject, $body);

if ($test) {
    echo "✅ Email envoyé avec succès !";
} else {
    echo "❌ Échec de l'envoi.";
}
