<style>
    
</style>



<?php
include_once "chemin.php";


$navbarStyle = '';
if (array_key_exists('USER_ID', $_SESSION)) {
    // si t'es co
    $sqlLogins = "select cre_login, cre_gerant from rap_client join rap_credentials using(cli_num) where cli_num = ". $_SESSION['USER_ID'];
    $Logins = array();
    LireDonneesPDO1($conn, $sqlLogins, $Logins);
    
    // Si c'est un gérant
    if($Logins[0]['CRE_GERANT'] == 1){
        $navbarStyle = 'background-color: #690702;';
    }
}

echo '<header class="navbar" style=" position:sticky; top:0; font-size: 20px;   '.$navbarStyle.'">
    <div class="logo nav-links" style="margin-left: 15px;">
      <a href="'.$accueil.'">
        <img src="'.$img.'logo.PNG" alt="Logo" style="height: 35px; vertical-align: middle; margin-right: 8px;">
        RapidC3
      </a>
    </div>
    <nav class="nav-links">
        <a class="" href="'.$accueil.'"> Accueil </a>';

if (array_key_exists('USER_ID', $_SESSION)) {
    if($Logins[0]['CRE_GERANT'] == 1){
        echo '<a href="'.$gestion.'"> Dashboard </a>';
        echo '<a href="#">'. $Logins[0]['CRE_LOGIN']. '</a>';
    } else {
        echo '<a href="'.$compte.'">'. $Logins[0]['CRE_LOGIN']. '</a>';
    }
    echo '<a href="'.$php_deco.'" class="btn btn-danger">Se déconnecter</a>';
} else {
    echo '<a href="'.$connexion.'"> Se connecter</a>';
}

echo '<a href="'.$commande.'" class="btn btn-warning py-1 px-3 rounded-pill"> Commander</a>
    </nav>
</header>';
?>
