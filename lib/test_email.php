<?php
require 'mail.php';

$to = "isacanyamah@gmail.com"; // Mets ton email personnel ici
$subject = "Test Gmail SMTP";
$body = "<h1>Test réussi 🎉</h1><p>Ton site peut maintenant envoyer des e-mails via Gmail.</p>";

$test = sendEmail($to, $subject, $body);

if ($test) {
    echo "✅ Email envoyé avec succès !";
} else {
    echo "❌ Échec de l'envoi.";
}
?>
