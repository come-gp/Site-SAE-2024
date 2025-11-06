<?php
        include_once "../php_utils/header.php";


    $sqlPlats = "Select * from rap_legume join rap_plat using(pla_num)
                union
                Select pla_num, pla_nom, pla_menu, pla_prix_vente_unit_ht, pla_prix_achat_unit_ht, pla_tva, pla_promotion, pla_nb_points, pla_duree_preparation from rap_pizza join rap_plat using(pla_num)
                union
                Select * from rap_kebab join rap_plat using(pla_num)
                union
                Select * from rap_boisson join rap_plat using(pla_num)
                union
                Select * from rap_dessert join rap_plat using(pla_num)";
    $sqlPlatsTab = array();
    LireDonneesPDO1($conn, $sqlPlats, $sqlPlatsTab);


    $numProduitActu = "100";

    if (isset($_POST["legume"])) {
        $numProduitActu = $_POST["legume"];
    }

    $sqlProduit = "Select * from rap_plat where pla_num = '". $numProduitActu ."'";
    $sqlProduitTab = array();
    LireDonneesPDO1($conn, $sqlProduit, $sqlProduitTab);

    $prixHtFinal =  (float)  str_replace(",",".", $sqlProduitTab[0]['PLA_PRIX_VENTE_UNIT_HT']);
    $prixAchatFinal =  (float)  str_replace(",",".", $sqlProduitTab[0]['PLA_PRIX_ACHAT_UNIT_HT']);
    $tvaFinal =  (float)  str_replace(",",".", $sqlProduitTab[0]['PLA_TVA']);
    $prix_ttc_produit = (float) $prixHtFinal * (1.00 + $tvaFinal / 100.00);

    $marge = (float) $prixHtFinal - $prixAchatFinal;
    $margeApresProm = (float) $prixHtFinal - $prixAchatFinal;//enlever promotion au prix
    $prixHtApresProm =  (float) $prixHtFinal ;//enlever promotion
    $prixTTCApresProm = (float) $prixHtApresProm * (1.00 + $tvaFinal / 100.00);
    print_r($numProduitActu);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RapidC3 - Accueil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>



<body>
    <?php
        include_once "../php_utils/navbar.php";
    ?>

    <form action="./index.php" method="post">
        <div class="form-group">
            <label for="legume">Produits</label>
            <select name="legume" id="legume" class="form-control mb-4">
                <?php foreach ($sqlPlatsTab as $x): ?>
                    <option value="<?= $x['PLA_NUM'] ?>"><?= $x['PLA_NOM'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Choisi le produit</button>
        </div>
    </form>

<h1 class="h1 text-center p-3">Promotions</h1>
<form action="./index.php" method="post">
    <div class="p-2 m-2 rounded border border-dark">
        <div class="row mb-3 align-items-center">
            <h4 class="col-sm-3 text-center">
                Nom
            </h4>
            <div class="col-sm-5">
                <input type="PLA_NOM" class="form-control" disabled placeholder="<?php echo $sqlProduitTab[0]['PLA_NOM'] ?>">
            </div>
        </div>
        <div class="row mb-3">
            <p class="col-sm-3 text-center">
                Prix Hors Taxes avant la promotion
            </p>
            <div class="col-sm-5">
                <input type="PLA_PRIX_VENTE_UNIT_HT" class="form-control" disabled placeholder="<?php echo $sqlProduitTab[0]['PLA_PRIX_VENTE_UNIT_HT'] ?>">
            </div>
        </div>
        <div class="row mb-3">
            <p class="col-sm-3 text-center">
                Prix Toutes Taxes Comprises(TTC) avant la promotion
            </p>
            <div class="col-sm-5">
                <input type="PLA_VENTE_UNIT_HT*(1+PLA_TVA/100)" class="form-control" disabled  placeholder="<?php echo  round($prix_ttc_produit, 2) ?>">
            </div>
        </div>
        <div class="row mb-3">
            <p class="col-sm-3 text-center">
                Marge faite sur le produit
            </p>
            <div class="col-sm-5">
                <input onchange="" type="PLA_VENTE_UNIT_HT-PLA_ACHAT_UNIT_HT" class="form-control" disabled placeholder="<?php echo  round($marge, 2) ?>">
            </div>
        </div>
        <div class="row mb-3">
            <p class="col-sm-3 text-center">
                Montant de la promotion (en %)
            </p>
            <div class="col-sm-5">
                <input type="number" min="0" max="100" class="form-control" placeholder="0">
            </div>
        </div>
        <div class="row mb-3">
            <p class="col-sm-3 text-center">
                Marge apres la promotion
            </p>
            <div class="col-sm-5">
                <input type="PLA_VENTE_UNIT_HT-PLA_ACHAT_UNIT_HT-PLA_REDUCTION" class="form-control" disabled placeholder="<?php echo  round($margeApresProm, 2) ?>">
            </div>
        </div>
        <div class="row mb-3">
            <p class="col-sm-3 text-center">
                Prix Hors Taxes apres la reduction
            </p>
            <div class="col-sm-5">
                <input type="PLA_VENTE_UNIT_HT-PLA_ACHAT_UNIT_HT-PLA_REDUCTION" class="form-control" disabled placeholder="<?php echo  round($prixHtApresProm, 2) ?>">
            </div>
        </div>
        <div class="row mb-3">
            <p class="col-sm-3 text-center">
                Prix Toutes Taxes Comprise(TTC) apres la reduction
            </p>
            <div class="col-sm-5">
                <input type="PLA_VENTE_UNIT_HT-PLA_REDUCTION*(1+PLA_TVA/100)" class="form-control" disabled placeholder="<?php echo  round($prixTTCApresProm, 2) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success">Valider</button>
            </div>
        </div>
    </div>
    </form>

    <?php
        include_once "../php_utils/navbar.php";
    ?>
</body>
</html>
