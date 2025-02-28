<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


function sendEmail($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // ✅ Désactiver la vérification SSL pour contourner le problème de certificat
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // ✅ Configuration SMTP
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER');
        $mail->Password = getenv('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getenv('SMTP_PORT');
        echo "🔍 getenv('SMTP_USER') : " . getenv('SMTP_USER') . "<br>";
        echo "🔍 \$_ENV['SMTP_USER'] : " . ($_ENV['SMTP_USER'] ?? 'Non défini') . "<br>";
        echo "🔍 \$_SERVER['SMTP_USER'] : " . ($_SERVER['SMTP_USER'] ?? 'Non défini') . "<br>";

        // ✅ Expéditeur et Destinataire
        $mail->setFrom($_ENV['SMTP_USER'], 'Zoo Arcadia');
        $mail->addAddress($to);

        // ✅ Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // ✅ Mode Debug SMTP pour voir les erreurs (à désactiver en production)
        $mail->SMTPDebug = 3;
        $mail->Debugoutput = function ($str, $level) {
            echo "🔍 SMTP Debug [$level]: $str <br>";
        };

        // ✅ Envoi de l'email
        if ($mail->send()) {
            return true;
        } else {
            echo "❌ Erreur SMTP : " . $mail->ErrorInfo;
            return false;
        }
    } catch (Exception $e) {
        echo "❌ Exception : " . $e->getMessage();
        return false;
    }
}
