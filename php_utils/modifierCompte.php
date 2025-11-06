<?php
include_once "header.php";

function escapeApostrophesSQL($chaine) {
    return str_replace("'", "", $chaine);
}

// Vérification des champs
if (
    isset($_POST['cli_id']) &&
    isset($_POST['nom']) &&
    isset($_POST['prenom']) &&
    isset($_POST['email']) &&
    isset($_POST['login']) &&
    isset($_POST['mot_passe'])
    
) {
    $id = $_POST['cli_id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $mdp = $_POST['mot_passe'];

    $mdp=escapeApostrophesSQL($mdp);
    $nom=escapeApostrophesSQL($nom);
    $prenom=escapeApostrophesSQL($prenom);
    $email=escapeApostrophesSQL($email);
    $login=escapeApostrophesSQL($login);

    if(
        mb_strlen($nom)<=32 &&
        mb_strlen($prenom)<=32 &&
        mb_strlen($email)<=254 &&
        mb_strlen($login)<=32 &&
        mb_strlen($mdp)<=32){
    

        $client = "update rap_client set cli_nom = :nom, cli_prenom = :prenom, cli_courriel = :email where cli_num = :id";
        
        $stmtcli = $conn->prepare($client);

        $resclient = $stmtcli->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':id' => $id
        ]);

        $credentials = "update rap_credentials set cre_login = :login, cre_mdp = :mdp where cli_num = :id";

        $stmtcre = $conn->prepare($credentials);

        $rescredential = $stmtcre->execute([
            ':login' => $login,
            ':mdp' => $mdp,
            ':id' => $id
        ]);
    }
}

header("Location: /compte");
exit();
?>