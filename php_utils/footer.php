<?php
  include_once "chemin.php";

  $footerStyle = 'style="background-color: #212529;"'; 

  if (array_key_exists('USER_ID', $_SESSION)) {
    $sqlLogins = "select cre_login, cre_gerant from rap_client join rap_credentials using(cli_num) where cli_num = ". $_SESSION['USER_ID'];
    $Logins = array();
    LireDonneesPDO1($conn, $sqlLogins, $Logins);

    // Si t'es gérant
    if ($Logins[0]['CRE_GERANT'] == 1) {
      $footerStyle = 'style="background-color: #690702;"';
    }
  }

  echo '<footer class="text-white pt-5 pb-3 mt-5" '.$footerStyle.'>
  <div class="container">
    <div class="row d-flex justify-content-between flex-wrap">
      <div class="col-auto mb-4">
        <h4><img src="/img/logo.PNG" alt="Logo" style="height: 35px; vertical-align: middle; margin-right: 8px;"> RapidC3</h4>
        <p>Restauration rapide, commandes en ligne et fidélité clients.</p>
      </div>
      <div class="col-auto mb-4">
        <h5>Liens utiles</h5>
        <ul class="list-unstyled">
          <li><a href="'.$accueil.'" class="text-white text-decoration-none">Accueil</a></li>
          <li><a href="'.$commande.'" class="text-white text-decoration-none">Menu</a></li>
          <li><a href="' . $mentions . '" class="text-white text-decoration-none">Mentions légales</a></li>
        </ul>
      </div>
      <div class="col-auto mb-4">
        <h5>Contact</h5>
        <p><strong>📧Email :</strong> contact@rapidc3.fr</p>
        <p><strong>📞Tél :</strong> 01 23 45 67 89</p>
        <a href="https://www.instagram.com/rapid_c3/" target="_blank">
          <p><img src="/img/insta.png" alt="insta" style="height: 35px; vertical-align: middle; margin-right: 8px;"></p>
        </a>
      </div>
    </div>
    <div class="text-center border-top pt-3 mt-4">
      &copy; 2025 RapidC3 – Tous droits réservés.
    </div>
  </div>
</footer>';
        
?>
