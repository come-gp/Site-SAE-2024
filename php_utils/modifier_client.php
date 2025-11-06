<?php
include_once "header.php";
include '../../php_utils/security.php';

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

header('Location: '.$gestion);
exit();
?>
