<?php


$email_admin = "isacanyamah@gmail.com";
$objet = "contact via le site web";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $mail = htmlspecialchars(trim($_POST['mail']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!empty($prenom) && !empty($mail) && !empty($message) && filter_var($mail, FILTER_VALIDATE_EMAIL)) {

        $to = "isac.anyamah@gmail.com";
        $subject = "New Contact Form Submission";
        
        $body = "Prénom: $prenom\n";
        $body .= "E-mail: $mail\n";
        $body .= "Message:\n$message\n";

        $headers = "From: $prenom <$mail>\r\n";
        $headers .= "Reply-To: $mail\r\n";

        if (mail($to, $subject, $body, $headers)) {
            echo "<p>Votre message a bien été envoyer</p>";
        } else {
            echo "<p>Désolé, une erreur s'est produite lors de l'envoi de votre message.</p>";
        }
    } else {
        echo "<p>Veuillez remplir tous les champs correctement.</p>";
    }
}
?>

