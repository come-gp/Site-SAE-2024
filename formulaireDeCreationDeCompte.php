<?php

include_once "../php_utils/header.php";

$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

function escapeApostrophesSQL($chaine) {
    return str_replace("'", "\'", $chaine);
}


if($password == $confirm_password){

    $indice = 0;
    $tab_id = array();

    $sql = "SELECT MAX(cli_num) as max from rap_client";
    LireDonneesPDOPreparee(preparerRequetePDO($conn,$sql),$tab_id);
    $indice = (int) $tab_id[0]["MAX"];
    $indice++;

    
	$nom = $_POST["nom"];
	$prenom = $_POST["prenom"];
	$mail = $_POST["mail"];
	$username = $_POST["username"];

    $password=escapeApostrophesSQL($password);
    $confirm_password=escapeApostrophesSQL($confirm_password);
    $nom=escapeApostrophesSQL($nom);
    $prenom=escapeApostrophesSQL($prenom);
    $mail=escapeApostrophesSQL($mail);
    $username=escapeApostrophesSQL($username);


    $username_count = 0;
    $tab_username_count = array();
    $sql = "SELECT count(*) as username_count from rap_credentials where cre_login ='".$username."'";
    LireDonneesPDOPreparee(preparerRequetePDO($conn,$sql),$tab_username_count);
    $username_count = (int) $tab_username_count[0]["USERNAME_COUNT"];


    if(mb_strlen($mail)>254 || mb_strlen($nom)>32 || mb_strlen($prenom)>32 || mb_strlen($username)>32 || mb_strlen($password)>32){
        header("Location: ./inscription.php");
        exit();
    } else {

        if($username_count > 0){
            $_SESSION['already_taken_username']="already_taken_username";
            $_SESSION['previous_name']=$nom;
            $_SESSION['previous_surname']=$prenom;
            $_SESSION['previous_mail']=$mail;


            header("Location: ./inscription.php");
            exit();
        } else {
            $sql = "INSERT INTO rap_client (cli_num, cli_nom, cli_prenom, cli_courriel) values(".$indice.", '".$nom."','".$prenom."',lower('".$mail."'))";
            majDonneesPrepareesPDO(preparerRequetePDO($conn,$sql));

            $sql = "INSERT INTO rap_credentials (cre_login, cre_mdp, cli_num) values('".$username."','".$password."',".$indice.")";
            majDonneesPrepareesPDO(preparerRequetePDO($conn,$sql));

            $sql = "INSERT INTO rap_fidelisation (cli_num, sui_date_points, total_points) values(".$indice.",  sysdate, 0)";
            majDonneesPrepareesPDO(preparerRequetePDO($conn,$sql));

            $_SESSION['USER_ID']=$indice;


            header("Location: ../index.php");
            exit();
        }
    }
}
exit();
?>