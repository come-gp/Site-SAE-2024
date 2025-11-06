<?php
  include_once "../php_utils/header.php";

  $already_taken_username = $_SESSION['already_taken_username'] ?? null;
  $previous_name = $_SESSION['previous_name'] ?? null;
  $previous_surname = $_SESSION['previous_surname'] ?? null;
  $previous_mail = $_SESSION['previous_mail'] ?? null;
  unset($_SESSION['already_taken_username']);
  unset($_SESSION['previous_name']);
  unset($_SESSION['previous_surname']);
  unset($_SESSION['previous_mail']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RapidC3 - Inscription</title>
    <link rel="stylesheet" href="<?php echo $css_inscription; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php
  include_once "../php_utils/navbar.php";
?>
<body>
    <main class="login-container">
        <div class="box">
            <form class="login-form" action="<?php echo $traiteInscri; ?>" method="POST">
                <h2>Inscription</h2>
                <i id="character_limit">(254 caractères maximum pour l'adresse mail, 32 pour les autres champs, et aucune apostrophe.)</i>

                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" pattern="[^']*" value="<?= htmlspecialchars($previous_name) ?>" required>

                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" pattern="[^']*" value="<?= htmlspecialchars($previous_surname) ?>" required>

                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" pattern="[^']*" required>
                <?php if($already_taken_username): ?>
                    <p class="incorrect">Nom d'utilisateur déjà pris, veuillez en choisir un autre.</p>
                <?php endif; ?>

                <label for="mail">Courriel</label>
                <input type="text" id="mail" name="mail" pattern="[^']*" value="<?= htmlspecialchars($previous_mail) ?>" required>
                

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" pattern="[^']*" required>

                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" pattern="[^']*" required>

                <span id="not_same_password" class="incorrect"></span>
                <span id="not_valid_mail" class="incorrect"></span>

                <div class="inscription inscription-button" id="submit-zone">
                    <button type="submit" id="submit">
                        Créer un compte
                    </button>
                </div>

                    
            </form>
            <hr><br><p1 class="center">Déjà un compte ?</p1><br>
            <div class="connexion connexion-button">
                <button onclick="window.location.href='<?php echo $connexion; ?>'" class="inscriptionbutton">
                    Se connecter
                </button>
            </div>
        </div>
    </main>
    <?php
      include_once $php_footer;
    ?>
    <script>
        function compare_passwords() {
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            if (password !== confirm_password) {
                    not_same_password.textContent = "Les mots de passe ne correspondent pas.";
                        return false;
                } else {
                    not_same_password.textContent = "";
                    return true;
            }
        }


        function verify_mail() {
            const mail = document.getElementById('mail').value;
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{1,}$/;
            if (regex.test(mail) || mail=="") {
                not_valid_mail.textContent = "";
                return true;
                
            } else {
                not_valid_mail.textContent = "Veuillez entrer une adresse mail valide.";
                return false;
            }
        }

        function verify_length(){
            const mail_length = document.getElementById('mail').value.length;
            const password_length = document.getElementById('password').value.length;
            const confirm_password_length = document.getElementById('confirm_password').value.length;
            const nom_length = document.getElementById('nom').value.length;
            const prenom_length = document.getElementById('prenom').value.length;
            const username_length = document.getElementById('username').value.length;

            const mailval = document.getElementById('mail').value;
            const passwordval = document.getElementById('password').value;
            const confirmval = document.getElementById('confirm_password').value;
            const nomval = document.getElementById('nom').value;
            const prenomval = document.getElementById('prenom').value;
            const usernameval = document.getElementById('username').value;

            

            if(mail_length<=254 && password_length<=32 && confirm_password_length<=32 && nom_length<=32 && prenom_length<=32 && username_length<=32 && !(/'/.test(mailval)) && !(/'/.test(passwordval)) && !(/'/.test(confirmval)) && !(/'/.test(nomval)) && !(/'/.test(prenomval)) && !(/'/.test(usernameval))){
                document.getElementById("character_limit").classList.remove("incorrect");
                return true;
            } else {
                document.getElementById("character_limit").classList.add("incorrect");
                return false;
                
            }
            

        }

        function verify_form(){
            if(verify_mail() && compare_passwords() && verify_length()){   
            document.getElementById("submit").disabled = false;
            document.getElementById("submit-zone").classList.remove("disabled");
            document.getElementById("submit-zone").classList.add("inscription-button");
        }
        else{
            document.getElementById("submit-zone").classList.remove("inscription-button");
            document.getElementById("submit-zone").classList.add("disabled");
            document.getElementById("submit").disabled = true;
        }
        }


        document.getElementById("mail").addEventListener('input', verify_form);
        document.getElementById("password").addEventListener('input', verify_form);
        document.getElementById("confirm_password").addEventListener('input', verify_form);
        document.getElementById("nom").addEventListener('input', verify_form);
        document.getElementById("prenom").addEventListener('input', verify_form);
        document.getElementById("username").addEventListener('input', verify_form);

        document.getElementById("mail").addEventListener('input', verify_mail);
        document.getElementById("password").addEventListener('input', compare_passwords);
        document.getElementById("confirm_password").addEventListener('input', compare_passwords);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>