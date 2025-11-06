<?php
session_start();

if (isset($_POST['montant_ttc'])) {
    $_SESSION['montant_ttc'] = $_POST['montant_ttc'];

    // Simuler un traitement PayPal ici (ou intégrer l’API réelle)

    // Rediriger vers la page de confirmation
    header("Location: confirmationPaiement.php");
    exit;
} else {
    // Si accès direct sans montant
    header("Location: index.php");
    exit;
}
