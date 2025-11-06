<?php
if (isset($_POST['email'], $_POST['objet'], $_POST['message'])) {
    $to = $_POST['email'];
    $subject = $_POST['objet'];
    $message = $_POST['message'];
    $headers = "From: promo@rapidc3.fr\r\nReply-To: contact@rapidc3.fr";

    if (mail($to, $subject, $message, $headers)) {
        echo "OK";
    } else {
        http_response_code(500); 
        echo "Erreur lors de l'envoi";
    }
} else {
    http_response_code(400); 
    echo "Champs manquants";
}
?>
