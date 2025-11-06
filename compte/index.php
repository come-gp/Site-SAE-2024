<?php
  include_once "../php_utils/header.php";

  if (isset($_SESSION["USER_ID"])) {
      $id = $_SESSION["USER_ID"];
    } else {
      header('Location: '.$accueil);
      exit();
    }

    $sql = 'SELECT 
              cli_nom, 
              cli_prenom, 
              cli_courriel, 
              cre_login, 
              cre_mdp, 
              total_points 
            FROM rap_client 
            join rap_credentials 
            using(cli_num) 
            join rap_fidelisation 
            using(cli_num) 
            where cli_num = :id
            AND sui_date_points = (
              SELECT MAX(sui_date_points)
              FROM rap_fidelisation
              WHERE cli_num = :id)';
    
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':id', $id);

    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RapidC3 - Mon Compte</title>
    <link rel="stylesheet" href="<?php echo $css_compte; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>

<body>
  <?php
    include_once $php_navbar;
  ?>
    <main class="container mt-5">
        <h2 class="text-center mb-4">Mon compte</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form class="border p-4 rounded shadow bg-light" method="POST">
                    <div class="mb-3">
                        <label for="nom" class="form-label"><b>Nom</b></label>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?php echo $row["CLI_NOM"]; ?>" readonly>

                        <label for="prenom" class="form-label mt-3"><b>Prénom</b></label>
                        <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo $row["CLI_PRENOM"]; ?>" readonly>

                        <label for="mail" class="form-label mt-3"><b>Courriel</b></label>
                        <input type="email" id="mail" name="mail" class="form-control" value="<?php echo $row["CLI_COURRIEL"]; ?>" readonly>

                        <label for="login" class="form-label mt-3"><b>Nom d'utilisateur</b></label>
                        <input type="text" id="login" name="login" class="form-control" value="<?php echo $row["CRE_LOGIN"]; ?>" readonly>

                        <label for="password" class="form-label mt-3"><b>Mot de passe</b></label>
                        <input type="password" id="password" name="password" class="form-control" value="<?php echo $row["CRE_MDP"]; ?>" readonly>

                        <label for="points" class="form-label mt-3"><b>Points restants</b></label>
                        <input type="text" id="points" name="points" class="form-control" value="<?php echo $row["TOTAL_POINTS"]; ?>" readonly>
                    </div>
                    <div class="modifier modifier-button" id="submit-zone">
                      <button type="button" id="modifier-btn" class="btn btn-warning py-1 px-3 rounded-pill">
                          <a href="<?php echo $php_modifMonCompte; ?>"> Modifier mes informations</a>
                      </button>
                      <button type="button" id="historique-btn" class="btn btn-warning py-1 px-3 rounded-pill">
                          <a href="<?php echo $histoCom; ?>"> Historique de mes commandes </a>
                      </button>   
                  </div>                            
                </form>
                <form action="<?php echo $php_deco; ?>" method="post">
                  <input type="hidden" name="id" value="<?php echo $id; ?>">
                  <button type="submit" class="btn btn-danger w-100">Se déconnecter</button>
                </form>
            </div>
        </div>
    </main>
    <?php
      include_once $php_footer;
    ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
