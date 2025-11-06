<?php
include_once "../../php_utils/header.php";
$_SESSION['commande']['payee'] = 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Confirmation de paiement</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container text-center mt-5">
  <h1 class="text-success">✅ Paiement effectué avec succès !</h1>
  <p class="lead mt-4">Merci pour votre commande.</p>
  <p class="h4">Montant payé : <strong><?php echo $_SESSION["commande"]["prixAvecPoints"]; ?> €</strong></p>

  <a href="index.php" class="btn btn-primary mt-4">Retour à l'accueil</a>
</body>
</html>
