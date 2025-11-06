<?php
    include_once "header.php";
    if (!array_key_exists('USER_ID', $_SESSION)) {
        header('Location: '.$accueil);
    }
    else {
        $sqlLogins2 = "select cre_login, cre_gerant from rap_client join rap_credentials using(cli_num) where cli_num = ". $_SESSION['USER_ID'];
        $Logins2 = array();
        LireDonneesPDO1($conn, $sqlLogins2, $Logins2);
        if($Logins2[0]['CRE_GERANT'] == 1){
            // est gerant
            header('Location: '.$accueil);
        }
    }
?>