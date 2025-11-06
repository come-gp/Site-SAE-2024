<?php
    include_once "../php_utils/header.php";
	//define ("MOD_BDD","MYSQL");
	//define ("MOD_BDD","ORACLE");

    error_reporting(E_ALL & ~E_WARNING);
    ini_set('display_errors', 1);

    //session_start();

    
    $prix_total_ttc = 0;

    // etablissement de la connexion
	if (MOD_BDD == "MYSQL")
	{
		$db_username = $db_usernameMySQL;		
		$db_password = $db_passwordMySQL;
		$db = $dbMySQL;
	}
	else
	{
		$db_username = $db_usernameOracle;		
		$db_password = $db_passwordOracle;	
		$db = $dbOracle;
	}
	
	//$conn = OuvrirConnexionPDO($db,$db_username,$db_password);


    



    //suppression de la commande
    if (isset($_POST['reset_session']) || $_SESSION["commande"]["validee"] == 1) {
        $_SESSION['commande'] = [
            "produits" => [],
            "menus"    => []
        ];
    }

    if (!isset($_SESSION['commande'])) {
        $_SESSION['commande'] = [
            "produits" => [],
            "menus"    => []
        ];
    }




    // ajout d'un produit
    if (isset($_POST["pla_num"])) {
        $pla_num = $_POST["pla_num"]; 
    
        $produit_existant = false;

        $max = sizeof($_SESSION["commande"]["produits"]);

        for ($i = 0; $i < $max; $i++) {
            if ($_SESSION["commande"]["produits"][$i]["PLA_NUM"] == $pla_num) {
                $_SESSION["commande"]["produits"][$i]["quantite"]++;
                $produit_existant = true;
                break; 
            }
        }

      
    
        if (!$produit_existant) {
            $nouvel_article = array(
                "PLA_NUM" => $pla_num,
                "quantite" => 1 
            );
            $_SESSION["commande"]["produits"][] = $nouvel_article;
        }
    
    } 




    // Ajouter un menu
    if (isset($_POST['ajouter_menu'])) {
        // Récupération des valeurs sélectionnées
        $plat_type = $_POST['plat_type'];
        $legume = $_POST['legume'];
        $boisson = $_POST['boisson'];
        $dessert = $_POST['dessert'];
        $plat_selected = $_POST[$plat_type];

        // Génération du pla_num du menu
        $pla_num_menu = substr($plat_selected, 0, 1); // Premier caractère de pizza/kebab
        $pla_num_menu .= substr($legume, 0, 1);  // Premier caractère du légume
        $pla_num_menu .= substr($boisson, 0, 1); // Premier caractère de la boisson
        $pla_num_menu .= substr($dessert, 0, 1); // Premier caractère du dessert

        $sqlTestMenu = "SELECT PLA_NOM, PLA_PRIX_VENTE_UNIT_HT, PLA_TVA FROM rap_plat WHERE PLA_NUM = '" . $pla_num_menu . "'";
        //echo $sqlTestMenu;
        $sqlTestMenuTab = array();
        LireDonneesPDO1($conn, $sqlTestMenu, $sqlTestMenuTab);

        // echo "<pre>";
        // print_r($sqlTestMenuTab);
        // echo "</pre>";
        

        if (sizeof($sqlTestMenuTab) == 0) {
            echo "array null";
            echo '<script language="javascript">';
            echo 'alert("Le menu n\'existe pas, les produits sont donc ajoutés en tant que tel ")'; 
            echo '</script>';

            $produits = [$plat_selected, $dessert, $boisson, $legume];

            foreach ($produits as $p) {
                $pla_num = $p; 
    
                $produit_existant = false;

                $max = sizeof($_SESSION["commande"]["produits"]);

                for ($i = 0; $i < $max; $i++) {
                    if ($_SESSION["commande"]["produits"][$i]["PLA_NUM"] == $pla_num) {
                        $_SESSION["commande"]["produits"][$i]["quantite"]++;
                        $produit_existant = true;
                        break; 
                    }
                }

            
            
                if (!$produit_existant) {
                    $nouvel_article = array(
                        "PLA_NUM" => $pla_num,
                        "quantite" => 1 
                    );
                    $_SESSION["commande"]["produits"][] = $nouvel_article;
                }
            }



        } else {
            $composition_menu = [
                'plat' => $plat_selected,
                'legume' => $legume,
                'boisson' => $boisson,
                'dessert' => $dessert
            ];
    
            $menu_existant = false;
    
    
            $max = sizeof($_SESSION["commande"]["menus"]);
    
            for ($i = 0; $i < $max; $i++) {
                if ($_SESSION["commande"]["menus"][$i]["PLA_NUM"] == $pla_num_menu) {
                    // Si le produit existe déjà, incrémenter la quantité
                    $_SESSION["commande"]["menus"][$i]["quantite"]++;
                    $menu_existant = true;
                    break; // Sortir de la boucle dès qu'on trouve le produit
                }
            }
    
    
            if (!$menu_existant) {
                $nouveau_menu = array(
                    "PLA_NUM" => $pla_num_menu,
                    "quantite" => 1,
                    "composition" => $composition_menu 
                );
                $_SESSION["commande"]["menus"][] = $nouveau_menu;
            }
        }

        

        //print_r($pla_num_menu);
        
    }




    if (isset($_POST["valider_commande"])) {

        $prix_total_ttc = 0;
        $total_points_fidelite = 0;
        $total_duree_preparation = 0;

        foreach ($_SESSION["commande"]["produits"] as $produit) {
            $sqlProduit = "SELECT PLA_PRIX_VENTE_UNIT_HT, PLA_TVA, PLA_POINTS_FIDELITE, PLA_DUREE_PREPARATION FROM rap_plat WHERE pla_num = '" . $produit['PLA_NUM'] . "'";
            $sqlProduitTab = array();;
            LireDonneesPDO1($conn, $sqlProduit, $sqlProduitTab);

            $prix_ttc = $sqlProduitTab['PLA_PRIX_VENTE_UNIT_HT'] * (1 + $sqlProduitTab['PLA_TVA'] / 100.0);
            $prix_total_ttc += $prix_ttc * $produit['quantite'];

            $total_points_fidelite += $sqlProduitTab['PLA_POINTS_FIDELITE'] * $sqlProduitTab['quantite'];
            $total_duree_preparation += $produit_info['PLA_DUREE_PREPARATION'] * $produit['quantite'];
        }

        foreach ($_SESSION["commande"]["menus"] as $menu) {
            $sqlMenu = "SELECT PLA_PRIX_VENTE_UNIT_HT, PLA_TVA, PLA_POINTS_FIDELITE, PLA_DUREE_PREPARATION 
                        FROM rap_plat WHERE pla_num = :pla_num";
            $stmt = $conn->prepare($sqlMenu);
            $stmt->execute(['pla_num' => $menu["PLA_NUM"]]);
            $menu_info = $stmt->fetch();

            $prix_ttc_menu = $menu_info['PLA_PRIX_VENTE_UNIT_HT'] * (1 + $menu_info['PLA_TVA'] / 100.0);
            $prix_total_ttc += $prix_ttc_menu * $menu['quantite'];

            $total_points_fidelite += $menu_info['PLA_POINTS_FIDELITE'] * $menu['quantite'];
            $total_duree_preparation += $menu_info['PLA_DUREE_PREPARATION'] * $menu['quantite'];
        }

        $_SESSION["commande"]["prix_total_ttc"] = $prix_total_ttc;
        $_SESSION["commande"]["total_points_fidelite"] = $total_points_fidelite;
        $_SESSION["commande"]["total_duree_preparation"] = $total_duree_preparation;

        header('Location: ./validation/');
        exit();
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
        
    }






	$sqlLegume = "Select * from rap_legume join rap_plat using(pla_num) ";
    $Legumes = array();
    LireDonneesPDO1($conn, $sqlLegume, $Legumes);

    $sqlBoisson = "Select * from rap_boisson join rap_plat using(pla_num) ";
    $Boissons = array();
    LireDonneesPDO1($conn, $sqlBoisson, $Boissons);

    $sqlDessert = "Select * from rap_dessert join rap_plat using(pla_num)";
    $Desserts = array();
    LireDonneesPDO1($conn, $sqlDessert, $Desserts);

    $sqlPizza = "Select * from rap_pizza join rap_plat using(pla_num) ";
    $Pizzas = array();
    LireDonneesPDO1($conn, $sqlPizza, $Pizzas);

    $sqlKebab = "Select * from rap_kebab join rap_plat using(pla_num) ";
    $Kebabs = array();
    LireDonneesPDO1($conn, $sqlKebab, $Kebabs);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapidC3 - Accueil</title>
    <link rel="stylesheet" href="../styles/styleCommande.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body>

<?php
include "../php_utils/navbar.php";
?>

    <div class="choix">
        <button class="btn btn-warning btn-choix " id="btn-produit" onclick="changeProduit()">Produit</button>
        <button class="btn btn-outline-warning btn-choix"  id="btn-menu" onclick="changeMenu()">Menu</button>
        
        
    </div>

    <div class="choix-commande">


    <div class="menus p-5" id="container-menus">
    <h4>Créer un Menu</h4>
    <form method="post" action="./index.php">
        <div class="form-group">
            <label for="legume">Légume</label>
            <select name="legume" id="legume" class="form-control mb-4">
                <?php foreach ($Legumes as $x): ?>
                    <option value="<?= $x['PLA_NUM'] ?>"><?= $x['PLA_NOM'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="boisson">Boisson</label>
            <select name="boisson" id="boisson" class="form-control mb-4">
                <?php foreach ($Boissons as $x): ?>
                    <option value="<?= $x['PLA_NUM'] ?>"><?= $x['PLA_NOM'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="dessert">Dessert</label>
            <select name="dessert" id="dessert" class="form-control mb-4">
                <?php foreach ($Desserts as $x): ?>
                    <option value="<?= $x['PLA_NUM'] ?>"><?= $x['PLA_NOM'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" class=" m2-4">
            <label>Choisissez entre Pizza ou Kebab</label><br>
            <input type="radio" name="plat_type" id="pizza" value="pizza">
            <label for="pizza">Pizza</label>
            <input type="radio" name="plat_type" id="kebab" value="kebab">
            <label for="kebab">Kebab</label>
        </div>

        <div class="form-group mb-4" id="plat_select">

        </div>


        <button type="submit" name="ajouter_menu" class="btn btn-success mt-3">Ajouter le menu</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour remplir dynamiquement les options du plat
        function remplirPlatSelect(platType) {
            var platSelect = document.getElementById('plat_select');
            platSelect.innerHTML = '';  // On vide la section avant de la remplir

            var select = document.createElement('select');
            select.classList.add('form-control');
            select.name = platType;

            var items = platType === 'pizza' ? <?= json_encode($Pizzas) ?> : <?= json_encode($Kebabs) ?>;
            
            items.forEach(function(item) {
                var option = document.createElement('option');
                option.value = item['PLA_NUM'];
                option.textContent = item['PLA_NOM'];
                select.appendChild(option);
            });

            platSelect.appendChild(select);
        }

        // Déclencher le remplissage immédiat avec "pizza" par défaut
        remplirPlatSelect('pizza');

        // Sélectionner le radio "pizza" au lancement de la page
        document.getElementById('pizza').checked = true;

        // Ajouter l'écouteur pour le changement d'option entre Pizza et Kebab
        document.querySelectorAll('input[name="plat_type"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var platType = this.value;
                remplirPlatSelect(platType);  // Remplir avec les pizzas ou kebabs selon le choix
            });
        });
    });
</script>







        <div id="container-produits"  class="produits">



            <div class="produit">
                <h4 class="text">Legumes</h4> 
                <div class=" mt-5">
                    <div class="position-relative">
                        <div class="scroll-container" id="cardScroll">

                            <?php 

                                    foreach ($Legumes as $x) { 
                                        echo  '<div class="card-wrapper">
                                                    <div class="card">
                                                        <img src="../img/' .  $x["PLA_NOM"] . '.png " class="card-img-top testimg" alt="image">
                                                        <div class="card-body">
                                                            <h5 class="card-title"> <b>' . $x["PLA_NOM"] . ' </b></h5> 
                                                            <p class="card-text">
                                                                Prix : <b> ' . $x["PLA_PRIX_VENTE_UNIT_HT"] . '€ </b><br> 
                                                                Duree de préparation : <b>' . $x["PLA_DUREE_PREPARATION"] . ' minutes </b><br>
                                                                Points de fidélités : <b>' . $x["PLA_NB_POINTS"] . ' </b>
                                                            </p>
                                                            <form method="post" action="./index.php" style="display:inline;">
                                                                <input type="hidden" name="pla_num" value="' . $x['PLA_NUM'] .'">
                                                                <button type="submit" class="btn btn-warning">Ajouter</button>
                                                             </form>
                                                            
                                                        </div> 
                                                    </div>
                                                </div>';
                                    }
                                    
                            ?>

                        </div>
                    </div> 
                </div>
            </div>

            <div class="produit">
                <h4 class="text">Pizza</h4> 
                <div class=" mt-5">
                    <div class="position-relative">
                        <div class="scroll-container" id="cardScroll">

                            <?php 

                                    foreach ($Pizzas as $x) { 
                                        echo  '<div class="card-wrapper">
                                                    <div class="card">
                                                        <img src="../img/' .  $x["PLA_NOM"] . '.png "  class="card-img-top" alt="image">
                                                        <div class="card-body">
                                                            <h5 class="card-title"> <b>' . $x["PLA_NOM"] . ' </b></h5> 
                                                            <p class="card-text">
                                                                Prix : <b> ' . $x["PLA_PRIX_VENTE_UNIT_HT"] . '€ </b><br> 
                                                                Duree de préparation : <b>' . $x["PLA_DUREE_PREPARATION"] . ' minutes </b><br>
                                                                Points de fidélités : <b>' . $x["PLA_NB_POINTS"] . ' </b>
                                                            </p>
                                                            <form method="post" action="./index.php" style="display:inline;">
                                                                <input type="hidden" name="pla_num" value="' . $x['PLA_NUM'] .'">
                                                                <button type="submit" class="btn btn-warning">Ajouter</button>
                                                             </form>
                                                        </div> 
                                                    </div>
                                                </div>';
                                    }
                                    
                            ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="produit">
                <h4 class="text">Kebab</h4> 
                <div class=" mt-5">
                    <div class="position-relative">
                        <div class="scroll-container" id="cardScroll">

                            <?php 

                                    foreach ($Kebabs as $x) { 
                                        echo  '<div class="card-wrapper">
                                                    <div class="card">
                                                        <img src="../img/' .  $x["PLA_NOM"] . '.png " class="card-img-top" alt="image">
                                                        <div class="card-body">
                                                            <h5 class="card-title"> <b>' . $x["PLA_NOM"] . ' </b></h5> 
                                                            <p class="card-text">
                                                                Prix : <b> ' . $x["PLA_PRIX_VENTE_UNIT_HT"] . '€ </b><br> 
                                                                Duree de préparation : <b>' . $x["PLA_DUREE_PREPARATION"] . ' minutes </b><br>
                                                                Points de fidélités : <b>' . $x["PLA_NB_POINTS"] . ' </b>
                                                            </p>
                                                            <form method="post" action="./index.php" style="display:inline;">
                                                                <input type="hidden" name="pla_num" value="' . $x['PLA_NUM'] .'">
                                                                <button type="submit" class="btn btn-warning">Ajouter</button>
                                                             </form>
                                                        </div> 
                                                    </div>
                                                </div>';
                                    }
                                    
                            ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="produit">
                <h4 class="text">Boisson</h4> 
                <div class=" mt-5">
                    <div class="position-relative">
                        <div class="scroll-container" id="cardScroll">

                            <?php 

                                    foreach ($Boissons as $x) { 
                                        echo  '<div class="card-wrapper">
                                                    <div class="card">
                                                        <img src="../img/' .  $x["PLA_NOM"] . '.png " class="card-img-top" alt="image">
                                                        <div class="card-body">
                                                            <h5 class="card-title"> <b>' . $x["PLA_NOM"] . ' </b></h5> 
                                                            <p class="card-text">
                                                                Prix : <b> ' . $x["PLA_PRIX_VENTE_UNIT_HT"] . '€ </b><br> 
                                                                Duree de préparation : <b>' . $x["PLA_DUREE_PREPARATION"] . ' minutes </b><br>
                                                                Points de fidélités : <b>' . $x["PLA_NB_POINTS"] . ' </b>
                                                            </p>
                                                            <form method="post" action="./index.php" style="display:inline;">
                                                                <input type="hidden" name="pla_num" value="' . $x['PLA_NUM'] .'">
                                                                <button type="submit" class="btn btn-warning">Ajouter</button>
                                                             </form>
                                                        </div> 
                                                    </div>
                                                </div>';
                                    }
                                    
                            ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="produit">
                <h4 class="text">Dessert</h4> 
                <div class=" mt-5">
                    <div class="position-relative">
                        <div class="scroll-container" id="cardScroll">

                            <?php 

                                foreach ($Desserts as $x) { 
                                    if ($x['PLA_NOM'] !== 'pas de dessert') {
                                    echo  '<div class="card-wrapper">
                                                <div class="card">
                                                        <img src="../img/' .  $x["PLA_NOM"] . '.png " class="card-img-top" alt="image">
                                                    <div class="card-body">
                                                        <h5 class="card-title"> <b>' . $x["PLA_NOM"] . ' </b></h5> 
                                                        <p class="card-text">
                                                            Prix : <b> ' . $x["PLA_PRIX_VENTE_UNIT_HT"] . '€ </b><br> 
                                                            Duree de préparation : <b>' . $x["PLA_DUREE_PREPARATION"] . ' minutes </b><br>
                                                            Points de fidélités : <b>' . $x["PLA_NB_POINTS"] . ' </b>
                                                        </p>
                                                        <form method="post" action="./index.php" style="display:inline;">
                                                                <input type="hidden" name="pla_num" value="' . $x['PLA_NUM'] .'">
                                                                <button type="submit" class="btn btn-warning">Ajouter</button>
                                                             </form>
                                                    </div> 
                                                </div>
                                            </div>';
                                    }
                                }
                                    
                            ?>

                        </div>
                    </div>
                </div>
            </div>


        </div>




        <div class="commande">
           

            <h4 class="text">Commande</h4>
            <form method="post">
                <button type="submit" name="reset_session" class="btn btn-danger btn-vider">🗑️ Vider</button>
            </form>

            <form method="post" action="./index.php">
                <a href="validation/" class="btn btn-success mt-3 btn-valider <?php if(sizeof($_SESSION["commande"]["menus"]) == 0 && sizeof($_SESSION["commande"]["produits"]) == 0){echo "disabled";} ?> ">✔️ Valider</a>
            </form>

            <?php
                print_r(  "Total : " .round($prix_total_ttc, 2) . " €");
            ?>

            <br>
            <h5 class="text">Menus(s) :</h5> 
            
            
            <ul class="list-group list-group-flush">
                <?php
                    foreach ($_SESSION["commande"]["menus"] as $menu) {
                        $sqlNomMenu = "SELECT PLA_NOM, PLA_PRIX_VENTE_UNIT_HT, PLA_TVA FROM rap_plat WHERE PLA_NUM = '" . $menu['PLA_NUM'] . "'";
                        $nomMenu = array();
                        LireDonneesPDO1($conn, $sqlNomMenu, $nomMenu);

                        //echo $sqlNomMenu ;

                        $ht = $nomMenu[0]['PLA_PRIX_VENTE_UNIT_HT'];
                        $tva = $nomMenu[0]['PLA_TVA'];

                        //print_r($nomMenu);


                        // echo $ht;
                        // echo "<br>";
                        // echo $tva;
                        // echo "<br>";

                        
                        $prixHtFinal =  (float)  str_replace(",",".", $ht);
                        $tvaFinal =  (float)  str_replace(",",".", $tva);

                        $prix_ttc_menu = (float) $prixHtFinal * (1.00 + $tvaFinal / 100.00);



                        //$prix_ttc = $nomMenu[0]['PLA_PRIX_VENTE_UNIT_HT'] * ( 1.00 + $nomMenu[0]['PLA_TVA'] / 100.00);

                        echo '<li class="list-group-item"><b>' . $nomMenu[0]['PLA_NOM'] . '</b> x ' . $menu["quantite"] . ' :  ('. round($prix_ttc_menu, 2)* $menu["quantite"] .  '€)';

                        $composition = $menu['composition']; // Composition du menu

                        echo '<ul>';
                        foreach ($composition as $type => $pla_num) {
                            $sqlNomProduit = "SELECT PLA_NOM FROM rap_plat WHERE PLA_NUM = '" . $pla_num . "'";
                            $nomProduit = array();
                            LireDonneesPDO1($conn, $sqlNomProduit, $nomProduit);

                            echo '<li>' . ucfirst($type) . ': ' . $nomProduit[0]['PLA_NOM'] . '</li>';
                        }
                        echo '</ul>';

                        echo '</li>';
                    }
                ?>
            </ul>
            <hr class="hr" />
            <h5 class="text">Produit(s) :</h5> 
            <ul class="list-group list-group-flush">
                <?php
                    $s = $_SESSION["commande"];
                    $commande = $_SESSION["commande"]["produits"];
                    
                    foreach ($_SESSION["commande"]["produits"] as $produit) { 
                        $sqlNom = "SELECT PLA_NOM, PLA_PRIX_VENTE_UNIT_HT, PLA_TVA FROM rap_plat WHERE pla_num = '" . $produit['PLA_NUM'] . "'";
                        $sqlNomTab = array();
                        LireDonneesPDO1($conn, $sqlNom, $sqlNomTab);

                        // $stmt = $conn->prepare($sqlNom);
                        // $stmt->execute(['pla_num' => $x['PLA_NUM']]);
                        // $nom = $stmt->fetch();

                        // echo $sqlNomTab[0]['PLA_PRIX_VENTE_UNIT_HT'];
                        // echo "<br>";
                        // echo $sqlNomTab[0]['PLA_TVA'];
                        // echo "<br>";



                        $prixHtFinal =  (float)  str_replace(",",".", $sqlNomTab[0]['PLA_PRIX_VENTE_UNIT_HT']);
                        $tvaFinal =  (float)  str_replace(",",".", $sqlNomTab[0]['PLA_TVA']);

                        $prix_ttc_produit = (float) $prixHtFinal * (1.00 + $tvaFinal / 100.00);
                        
                        $prix_total_produit = $prix_ttc_produit * $produit['quantite'];

                        echo ' <li class="list-group-item">' . $sqlNomTab[0]['PLA_NOM'] . ' x ' . $produit['quantite'] . ' : ' . number_format($prix_total_produit, 2) . '€ </li>';
                    }

                    // echo "<pre>";
                    // print_r($s);
                    // echo "</pre>";
                ?>
            </ul>
                 
            <br>

           
            

        </div>
    </div>

    <div class="container-footer-ououou">

    
        <footer style="width: 70%;" class="bg-dark text-white pt-5 pb-3 mt-5">
            <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                <h4>🍔 RapidC3</h4>
                <p>Restauration rapide, commandes en ligne et fidélité clients.</p>
                </div>
                <div class="col-md-4 mb-4">
                <h5>Liens utiles</h5>
                <ul class="list-unstyled">
                    <li><a href="../" class="text-white text-decoration-none">Accueil</a></li>
                    <li><a href="./" class="text-white text-decoration-none">Menu</a></li>
                    <li><a href="../mentions-legales/" class="text-white text-decoration-none">Mentions légales</a></li>
                </ul>
                </div>
                <div class="col-md-4 mb-4">
                <h5>Contact</h5>
                <p><strong>Email :</strong> contact@rapidc3.fr</p>
                <p><strong>Tél :</strong> 01 23 45 67 89</p>
                </div>
            </div>
            <div class="text-center border-top pt-3 mt-4">
                &copy; 2025 RapidC3 – Tous droits réservés.
            </div>
            </div>
        </footer>

    </div>
    <script src="../js/search.js"></script>
    <script src="../js/commande.js"></script>


</body>
</html>