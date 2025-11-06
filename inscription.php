<?php
  include_once "header.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RapidC3 - Inscription</title>
    <link rel="stylesheet" href="./styles/styleinscription.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php
  include_once "navbar.php";
?>
<body>
    <main class="login-container">
        <form class="login-form" action="formulaireDeCreationDeCompte.php" method="POST">
            <h2>Inscription</h2>

            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required>

    <label for="mail">Courriel</label>
            <input type="text" id="mail" name="mail" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

    <span id="not_same_password" class="incorrect"></span>
    <span id="not_valid_mail" class="incorrect"></span>

            <div class="inscription inscription-button" id="submit-zone">
                <button type="submit" id="submit">
                    Créer un compte
                </button>
            </div>
        </form>

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
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (regex.test(mail) || mail=="") {
        not_valid_mail.textContent = "";
        return true;
        
    } else {
        not_valid_mail.textContent = "Veuillez entrer une adresse mail valide.";
        return false;
    }
}    

function verify_form(){
    if(verify_mail() && compare_passwords()){   
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

document.getElementById("mail").addEventListener('input', verify_mail);
document.getElementById("password").addEventListener('input', compare_passwords);
document.getElementById("confirm_password").addEventListener('input', compare_passwords);

</script>
    </main>
    <?php
      include_once "footer.php";
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>