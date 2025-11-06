<?php  
  include_once "../../php_utils/header.php";
  include_once $php_secure;
 
  if (isset($_GET["idClient"])) {
    $id = $_GET["idClient"];
  } else {
    header('Location: '.$gestion);
    exit();
  }
 
  $sql = "SELECT cli_nom, cli_prenom, cli_courriel, cre_login, cre_mdp FROM rap_client join rap_credentials using(cli_num) where cli_num =:id";
   
  $stmt = $conn->prepare($sql);
 
  $stmt->bindParam(':id', $id);
 
  $stmt->execute();
 
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapidC3 - Accueil</title>
    <link rel="stylesheet" href="<?php echo $css_index; ?>">
    <link rel="stylesheet" href="<?php echo $css_style1; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .espace{
        margin: 1%;
        padding: 1%;
      }
    </style>
<!-- Google Fonts : Anton -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
 
 
</head>
<?php include_once $php_navbar; ?>
<body>
  <div class="container forum-container">
    <!-- Titre du forum -->
    <div class="text-center">
      <h1>Gérer mon Client</h1>
    </div>
 
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">
            <h4>Informations Client</h4>
          </div>
          <div class="card-body">
            <form action="<?php echo $php_modifCli; ?>" method="post">
              <input type="hidden" name="cli_id" value="<?php echo $id; ?>">
             
              <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" maxlength="32" value="<?php echo $row["CLI_NOM"]; ?>" required>
              </div>
             
              <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" maxlength="32" value="<?php echo $row["CLI_PRENOM"]; ?>" required>
              </div>
             
              <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="254" value="<?php echo $row["CLI_COURRIEL"]; ?>" required>
              </div>
             
              <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" class="form-control" id="login" name="login" maxlength="32" value="<?php echo $row["CRE_LOGIN"]; ?>" required>
              </div>
             
              <div class="mb-3">
                <label for="mot_passe" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mot_passe" name="mot_passe" maxlength="32" value="<?php echo $row["CRE_MDP"]; ?>" required>
              </div>
             
              <div class="espace">
                <button type="submit" class="btn btn-warning w-100">Enregistrer</button>
              </div>
 
               
            </form>
            <div class="espace">
              <?php echo 
                      '<a href="'.
                        $gestionCom.'?idClient='.$id.'" class="e btn btn-warning w-100">
                          Voir les commandes
                        </a>'; 
                  ?>
            </div>
            <div class="espace">
              <form action="<?php echo $php_supprCli; ?>" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit" class="btn btn-danger w-100 mt-10">Supprimer</button>
              </form>
            </div>
            <div class="espace">
              <?php echo 
                      '<a href="'.
                        $gestion.'" class="btn btn-secondary w-100">
                          Retourner à la liste des clients
                        </a>'; 
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
 
 
     
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <?php include_once $php_footer; ?>
</body>
</html>
