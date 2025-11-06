<?php
include_once "../../php_utils/header.php";

error_reporting(E_ALL & ~E_WARNING);
    ini_set('display_errors', 1);


$prix_total_ttc = 0;
$total_points_fidelite = 0;
$total_duree_preparation = 0;

if (!isset($_SESSION["commande"]["validee"])){
    $_SESSION["commande"]["validee"] = 0;
} 

// if ($_POST['restautant'] == ""){
//     echo "veuillez selectionner un restaurant";
// }

if(isset($_SESSION["USER_ID"])){
    $sqlFidelisation = 'select total_points from rap_fidelisation where sui_date_points = (
        SELECT MAX(sui_date_points)
        FROM rap_fidelisation
        WHERE cli_num =  '.$_SESSION['USER_ID'].') and cli_num = '.$_SESSION['USER_ID'];

    //echo "<br>";
    //echo $sqlFidelisation;
    $sqlFidelisationTab = array();
    LireDonneesPDO1($conn, $sqlFidelisation, $sqlFidelisationTab);

    $_SESSION["NB_POINTS"] = $sqlFidelisationTab[0]["TOTAL_POINTS"];

    //print_r()
}



if (isset($_POST['restautant']) && $_POST['restautant'] !== "") {
    
    if(isset($_SESSION["USER_ID"])){
        //echo"connecté";
        $sqlComNum = 'select max(com_num) + 1 from rap_commande';
        $sqlComNumTab = array();
        LireDonneesPDO1($conn, $sqlComNum, $sqlComNumTab);

        $comCourNum = $sqlComNumTab[0]['MAX(COM_NUM)+1'];
        //print_r( $comCourNum);
        
        $tempsSec = strval( $_SESSION["commande"]["total_duree_preparation"]*60);
        //print_r($tempsSec);

        $sqlInserCommande = 'insert into rap_commande values ('.$_POST['restautant'].','.$comCourNum.', ' . $_SESSION['USER_ID'] . ', sysdate, sysdate + interval \'' . $tempsSec . '\' second,'. round($_SESSION["commande"]["prix_total_ttc"] - $_POST['nbPointsUtilises']/100 , 2).','.$_SESSION["commande"]["total_points_fidelite"].', 0,'.$_SESSION["commande"]["total_duree_preparation"].')';
        //echo $sqlInserCommande;
        majDonneesPDO($conn, $sqlInserCommande);



        $sqlFidelisation = 'select total_points from rap_fidelisation where sui_date_points = (
                                SELECT MAX(sui_date_points)
                                FROM rap_fidelisation
                                WHERE cli_num =  '.$_SESSION['USER_ID'].') and cli_num = '.$_SESSION['USER_ID'];

        //echo "<br>";
        //echo $sqlFidelisation;
        $sqlFidelisationTab = array();
        LireDonneesPDO1($conn, $sqlFidelisation, $sqlFidelisationTab);

        //print_r($sqlFidelisationTab[0]["TOTAL_POINTS"]);

     

        //$_SESSION['USER_ID']['nbPoints'] = $sqlFidelisationTab[0]["TOTAL_POINTS"];


        $sqlInserFidelisation = 'insert into rap_fidelisation values ('.$_SESSION["USER_ID"]. ', sysdate, ' . $sqlFidelisationTab[0]["TOTAL_POINTS"] + $_SESSION["commande"]["total_points_fidelite"] - $_POST['nbPointsUtilises'].')';
        //echo "<br>";
        //echo $sqlInserFidelisation;
        majDonneesPDO($conn, $sqlInserFidelisation);

        $_SESSION['commande']['prixAvecPoints'] = round($_SESSION["commande"]["prix_total_ttc"] - $_POST['nbPointsUtilises']/100 , 2);

        //echo'commande effectué';
        
    }else{
        //echo"pas connecter";
        $sqlComNum = 'select max(com_num) + 1 from rap_commande';
        $sqlComNumTab = array();
        LireDonneesPDO1($conn, $sqlComNum, $sqlComNumTab);

        $comCourNum = $sqlComNumTab[0]['MAX(COM_NUM)+1'];
        //print_r( $comCourNum);
        
        $tempsSec = strval( $_SESSION["commande"]["total_duree_preparation"]);
        //print_r($tempsSec);

        $sqlInserCommande = 'insert into rap_commande values ('.$_POST['restautant'].','.$comCourNum.', 0, sysdate, sysdate + interval \'' . $tempsSec . '\' second,'. round($_SESSION["commande"]["prix_total_ttc"], 2).','.$_SESSION["commande"]["total_points_fidelite"].', 0,'.$_SESSION["commande"]["total_duree_preparation"].')';
        //echo $sqlInserCommande;
        majDonneesPDO($conn, $sqlInserCommande);

        //echo'commande effectué';
       
        
        $_SESSION['commande']['prixAvecPoints'] = round($_SESSION["commande"]["prix_total_ttc"], 2);
    }
    // echo "<pre>";
    // print_r($_SESSION);
    // echo "</pre>";

    foreach ($_SESSION["commande"]["produits"] as $p) {
        $sqlInserAppartenir = 'insert into rap_appartenir values ('.$_POST['restautant'].','.$comCourNum.',\'' . $p['PLA_NUM'] .'\','.$p['quantite'].')';
        //echo "<br>";
        //echo $sqlInserAppartenir;
        majDonneesPDO($conn, $sqlInserAppartenir);
        
    }

    foreach ($_SESSION["commande"]["menus"] as $m) {
        $sqlInserAppartenir = 'insert into rap_appartenir values ('.$_POST['restautant'].','.$comCourNum.',\'' . $m['PLA_NUM'] .'\','.$m['quantite'].')';
        majDonneesPDO($conn, $sqlInserAppartenir);

    }

    
    

    $_SESSION["commande"]["validee"] = 1;
    //echo "zepdfjpojùpo";    
    //echo $_SESSION["commande"]["validee"];
}


foreach ($_SESSION["commande"]["produits"] as $produit) {
    $sqlProduit = "SELECT PLA_PRIX_VENTE_UNIT_HT, PLA_TVA, PLA_NB_POINTS, PLA_DUREE_PREPARATION FROM rap_plat WHERE pla_num = '" . $produit['PLA_NUM'] . "'";
    $sqlProduitTab = array();
    LireDonneesPDO1($conn, $sqlProduit, $sqlProduitTab);

    //$prix_ttc = $sqlProduitTab[0]['PLA_PRIX_VENTE_UNIT_HT'] * (1 + $sqlProduitTab[0]['PLA_TVA'] / 100.0);

    $prixHtFinal =  (float)  str_replace(",",".", $sqlProduitTab[0]['PLA_PRIX_VENTE_UNIT_HT']);
    $tvaFinal =  (float)  str_replace(",",".", $sqlProduitTab[0]['PLA_TVA']);
    $prix_ttc_produit = (float) $prixHtFinal * (1.00 + $tvaFinal / 100.00);

    $prix_total_ttc += $prix_ttc_produit * $produit['quantite'];

    $total_points_fidelite += $sqlProduitTab[0]['PLA_NB_POINTS'] * $produit['quantite'];
    $total_duree_preparation += $sqlProduitTab[0]['PLA_DUREE_PREPARATION'] * $produit['quantite'];

}

foreach ($_SESSION["commande"]["menus"] as $menu) {
    $sqlMenu = "SELECT PLA_PRIX_VENTE_UNIT_HT, PLA_TVA, PLA_NB_POINTS, PLA_DUREE_PREPARATION FROM rap_plat WHERE pla_num = '" . $menu['PLA_NUM'] . "'";
    $sqlMenuTab = array();
    LireDonneesPDO1($conn, $sqlMenu, $sqlMenuTab);

    //$prix_ttc_menu = $sqlMenuTab[0]['PLA_PRIX_VENTE_UNIT_HT'] * (1.0 + $sqlMenuTab[0]['PLA_TVA'] / 100.0);

    $prixHtFinal =  (float)  str_replace(",",".", $sqlMenuTab[0]['PLA_PRIX_VENTE_UNIT_HT']);
    $tvaFinal =  (float)  str_replace(",",".", $sqlMenuTab[0]['PLA_TVA']);

    $prix_ttc_menu = (float) $prixHtFinal * (1.00 + $tvaFinal / 100.00);

    $prix_total_ttc += $prix_ttc_menu * $menu['quantite'];

    $total_points_fidelite += $sqlMenuTab[0]['PLA_NB_POINTS'] * $menu['quantite'];
    $total_duree_preparation += $sqlMenuTab[0]['PLA_DUREE_PREPARATION'] * $menu['quantite'];

    
}

$_SESSION["commande"]["prix_total_ttc"] = $prix_total_ttc;
$_SESSION["commande"]["total_points_fidelite"] = $total_points_fidelite;
$_SESSION["commande"]["total_duree_preparation"] = $total_duree_preparation;







?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de la commande</title>
    <link rel="stylesheet" href="../../styles/styleCommande.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
        include_once $php_navbar;
    ?>
    <div class="container mt-5">
        <h1>Passer une commande</h1>

        <?php if (isset($message)) { ?>
            <div class="alert <?php echo $message_class; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <!-- Formulaire pour sélectionner un restaurant -->
        <form method="post" action="./index.php">
            <div class="mb-3">
                <label for="restautant" class="form-label">Choisissez un restaurant</label>
                <select name="restautant" id="restautant" class="form-select">
                    <option value="">--Veuillez choisir un restaurant--</option>
                    <?php
                        $sqlRestaurant = "SELECT * FROM rap_restaurant";
                        $sqlRestaurantTab = array();
                        LireDonneesPDO1($conn, $sqlRestaurant, $sqlRestaurantTab);
                        foreach ($sqlRestaurantTab as $r) {
                            echo '<option value="'. $r["RES_NUM"] .'">'. $r["RES_ADRESSE"] .'</option>';
                        }
                    ?>
                </select>


                <div class="fidelisation <?php if(!isset($_SESSION["USER_ID"]) || $_SESSION["NB_POINTS"] < 1000){ echo "d-none";} ?>">
                    <label for="nbPointsUtilises">Nombre de points fidélité à utiliser :</label>
                    <input min="0" max="<?php   
                        $maxPrixPoints = round($prix_total_ttc, 2) * 100;
                        $maxPoints = $_SESSION["NB_POINTS"];
                        if ($maxPoints < $maxPrixPoints){
                            echo $maxPoints;
                        } else {
                            echo $maxPrixPoints;
                        }

                    ?>" type="number" class="form-number" name="nbPointsUtilises" onChange="changePrix()" id="nbPointsUtilises" value = 0>
                    <p class="text">Vous avez : <?php echo $_SESSION["NB_POINTS"] ?></p>
                
                    
                </div>

                <button type="submit" class="btn btn-primary mt-2 <?php if ($_SESSION["commande"]["validee"] == 1 ) { echo "disabled";} ?> ">Valider la commande</button>

                

            <h3 class="mt-5">Résumé de votre commande</h3>
            <ul class="list-group">
                <li class="list-group-item"><strong>Prix total TTC :</strong> <p> <span id="ancien-prix"><?php echo number_format($prix_total_ttc, 2)?>  </span> €</p> <p class="nouveau-prix" id="nouveau-prix">( nouveau prix : 0 €)</p></li> 
                <li class="list-group-item"><strong>Points de fidélité :</strong> <?php echo $total_points_fidelite; ?> (attention, si vous n'êtes pas connecté, ces points irons à l'association du Tour de France...)</li> 
                <li class="list-group-item"><strong>Durée de préparation totale :</strong> <?php echo $total_duree_preparation . " minutes"; ?></li>
            </ul>
        </form>


        

        <!-- Résumé de la commande -->
        <?php 
        //print_r($_SESSION);
        if ($_SESSION["commande"]["validee"] == 1 ) { ?>
            
            <div class="alert alert-success mt-3" role="alert">
                Commande validée ! Merci pour votre achat.
            </div>


            <?php if (isset($_SESSION["commande"]["validee"])): ?>
                        <form class="form-group" action="choix_paiement.php" method="POST">
                            <input type="hidden" name="montant_ttc" value="<?= htmlspecialchars($_SESSION["commande"]["prix_total_ttc"]) ?>">
                            <button type="submit" class="btn btn-warning  <?php if ($_SESSION["commande"]["payee"] == 1 ) { echo "disabled";} ?>">
                                Payer (<?php  echo $_SESSION['commande']['prixAvecPoints'] ; ?> €)
                            </button>
                        </form>
                        <p class="text">Si vous ne payez pas en ligne, il faudra le faire lors de la recuperation de la commande</p>
            <?php endif; ?>


        <?php } else { ?>
            <div class="alert alert-info mt-3" role="alert">
                Commande non validée.
            </div>
        <?php } ?>
    </div>
    

    </div>

    <?php
    include_once "../../php_utils/footer.php";
    ?>

<script src="../../js/validation.js"></script>

</body>
</html>