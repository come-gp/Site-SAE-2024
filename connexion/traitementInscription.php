<?php

include_once "../php_utils/header.php";

$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

if($password == $confirm_password){

    $indice = 0;
    $tab_id = array();

    $sql = "SELECT MAX(cli_num) as max from rap_client";
    LireDonneesPDO1($conn,$sql,$tab_id);
    $indice = (int) $tab_id[0]["MAX"];
    $indice++;

    
	$nom = htmlspecialchars($_POST["nom"]);
	$prenom = htmlspecialchars($_POST["prenom"]);
	$mail = htmlspecialchars($_POST["mail"]);
	$username = htmlspecialchars($_POST["username"]);


    $username_count = 0;
    $tab_username_count = array();
    $sql = "SELECT count(*) as username_count from rap_credentials where cre_login ='".$username."'";
    LireDonneesPDO1($conn,$sql,$tab_username_count);
    $username_count = (int) $tab_username_count[0]["USERNAME_COUNT"];


    if(mb_strlen($mail)>32 || mb_strlen($nom)>32 || mb_strlen($prenom)>32 || mb_strlen($username)>32 || mb_strlen($password)>32){
        header('Location: '.$inscription);
        exit();
    } else {

        if($username_count > 0){
            $_SESSION['already_taken_username']="already_taken_username";
            $_SESSION['previous_name']=$nom;
            $_SESSION['previous_surname']=$prenom;
            $_SESSION['previous_mail']=$mail;


            header('Location: '.$inscription);
            exit();
        } else {
            $sql = "INSERT INTO rap_client (cli_num, cli_nom, cli_prenom, cli_courriel) values(".$indice.", '".$nom."','".$prenom."',lower('".$mail."'))";
            majDonneesPDO($conn,$sql);

            $sql = "INSERT INTO rap_credentials (cre_login, cre_mdp, cli_num) values('".$username."','".$password."',".$indice.")";
            majDonneesPDO($conn,$sql);

            $sql = "INSERT INTO rap_fidelisation (cli_num, sui_date_points, total_points) values(".$indice.",  sysdate, 0)";
            majDonneesPDO($conn,$sql);

            $_SESSION['USER_ID']=$indice;


            header('Location: '.$accueil);
            exit();
        }
    }
}
exit();
?>