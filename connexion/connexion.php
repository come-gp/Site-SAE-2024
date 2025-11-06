<?php 
    include_once "../php_utils/header.php";

    $previous_username = $_SESSION['previous_username'] ?? null;
    $incorrect_login_datas = $_SESSION['incorrect_login_datas'] ?? null;
    
    unset($_SESSION['previous_username']);
    unset($_SESSION['incorrect_login_datas']);
?>

  

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RapidC3 - Connexion</title>
    <link rel="stylesheet" href="<?php echo $css_connexion; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php 
    include_once $php_navbar; 
?>

<body>
    <nav>
        <main class="login-container">
            <div class="box">
                <form class="login-form" action="<?php echo $verifConn; ?>" method="POST">
                    <h2>Connexion</h2>
                    <label for="login">Login</label>
                    <input type="login" id="login" name="login" value="<?= htmlspecialchars($previous_username) ?>" required>
            
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                    <?php if($incorrect_login_datas): ?>
                        <p class="incorrect">Nom d'utilisateur ou mot de passe incorrecte.</p>
                    <?php endif; ?>
            
                    <button type="submit">Se connecter</button>

                    <p class="small_padding">Pour des raisons de sécurité, veuillez vous déconnecter et fermer votre navigateur lorsque vous avez fini d'accéder aux services authentifiés.</p>
                    
                </form>
                <hr><br><p1 class="center">Pas encore de compte ?</p1><br>
                <div class="inscription inscription-button">
                    <button onclick="window.location.href='<?php echo $inscription; ?>'" class="connexionbutton">
                        Créer un compte
                    </button>
                </div>
            </div>
        </main>
    </nav>
    <?php include_once $php_footer; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
