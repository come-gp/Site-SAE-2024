<?php
  include_once "../php_utils/header.php";
 
  if (isset($_SESSION["USER_ID"])) {
      $id = $_SESSION["USER_ID"];
    } else {
      header('Location: '.$accueil);
      exit();
    }
 
    $sql = "SELECT  
              cli_nom,  
              cli_prenom,  
              cli_courriel,  
              cre_login,  
              cre_mdp
            FROM rap_client  
            join rap_credentials  
            using(cli_num)
            where cli_num = :id";
     
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
    <link rel="stylesheet" href="<?php echo $css_modifCompte; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
 
<body>
    <?php
        include_once $php_navbar;
    ?>
    <main class="container mt-5">
        <h2 class="text-center mb-4">Modifier mon compte</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form class="border p-4 rounded shadow bg-light" action="<?php echo $php_modifCompte; ?>" method="POST">
                    <div class="mb-3">
                        <input type="hidden" name="cli_id" value="<?php echo $id; ?>">
 
                        <label for="nom" class="form-label"><b>Nom</b></label>
                        <input type="text" id="nom" name="nom" class="form-control" maxlength="32"  pattern="[^']*" value="<?php echo $row["CLI_NOM"]; ?>" required>
 
                        <label for="prenom" class="form-label mt-3"><b>Prénom</b></label>
                        <input type="text" id="prenom" name="prenom" class="form-control" maxlength="32" pattern="[^']*" value="<?php echo $row["CLI_PRENOM"]; ?>" required>
 
                        <label for="mail" class="form-label mt-3"><b>Courriel</b></label>
                        <input type="email" id="mail" name="email" class="form-control" maxlength="254" pattern="[^']*" value="<?php echo $row["CLI_COURRIEL"]; ?>" required>
 
                        <label for="login" class="form-label mt-3"><b>Nom d'utilisateur</b></label>
                        <input type="text" id="login" name="login" class="form-control" maxlength="32" pattern="[^']*" value="<?php echo $row["CRE_LOGIN"]; ?>" required>
 
                        <label for="password" class="form-label mt-3"><b>Mot de passe</b></label>
                        <input type="password" id="password" name="mot_passe" class="form-control" maxlength="32" pattern="[^']*" value="<?php echo $row["CRE_MDP"]; ?>" required>
                    </div>
                    <div class="modifier modifier-button" id="submit-zone">
                      <button type="submit" id="modifier-btn" class="btn btn-warning py-1 px-3 rounded-pill">
                          Valider mes informations
                      </button>    
                  </div>                             
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
