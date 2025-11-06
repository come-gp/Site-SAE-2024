<?php
session_start();

// Récupération sécurisée du montant TTC
if (isset($_POST["montant_ttc"])) {
    $_SESSION["montant_ttc"] = $_POST["montant_ttc"];
} elseif (!isset($_SESSION["montant_ttc"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix paiement </title>
    <link rel="stylesheet" href="../../styles/styleCommande.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .espace {
      margin: 1%;
      padding: 1%;
    }
    .btn:hover {
      opacity: 0.9;
      transform: scale(1.02);
      transition: all 0.2s ease-in-out;
    }
    .logo {
      font-family: 'Anton', sans-serif;
      font-size: 2rem;
      padding: 10px;
    }
    
  </style>
  </head>
<body>
<?php
        // include_once "../../php_utils/navbar.php";
    ?>

  <div class="wrapper d-flex flex-column min-vh-100">
    <div class="container my-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card text-center shadow-lg">
            <div class="card-header bg-warning text-dark fw-bold">
              Mode de paiement
            </div>
            <div class="card-body">
              <h5 class="card-title mb-4">Choisissez votre moyen de paiement</h5>

              <!-- PayPal direct -->
              <form action="paiementPaypal.php" method="POST">
                    <input type="hidden" name="montant_ttc" value="<?= htmlspecialchars($_SESSION["commande"]["prixAvecPoints"]) ?>">
                    <button type="submit" class="btn w-100 rounded-pill py-3 m-n mb-2" style="background-color: #FFC439;">
                        <img src="https://www.paypalobjects.com/webstatic/icon/pp258.png" style="height: 20px;">
                        <span class="ms-2">PayPal (<?= number_format($_SESSION["commande"]["prixAvecPoints"], 2) ?> €)</span>
                    </button>
            </form>


             
              <!-- Carte Bancaire bouton factice -->
              <button class="btn w-100 d-flex align-items-center justify-content-center rounded-pill py-3"
                      style="background-color: #001C64; color: white; font-weight: bold;">
                <img src="https://cdn-icons-png.flaticon.com/512/633/633611.png" alt="Carte bancaire" style="height: 20px;">
                <span class="ms-2">Carte bancaire</span>
              </button>
            </div>
            <div class="card-footer text-muted">
              Sécurisé par PayPal & Stripe
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
    include_once "../../php_utils/footer.php";
    ?>

<script src="../../js/validation.js"></script>
</html>
