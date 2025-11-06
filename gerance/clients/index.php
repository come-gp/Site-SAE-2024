<?php
  include_once "../../header.php";

  $sql = "SELECT 
              rc.cli_num, 
              c.cli_nom, 
              c.cli_prenom, 
              c.cli_courriel, 
              rc.com_date AS derniere_commande_date, 
              rc.com_prix_total AS prix_derniere_commande,
              total_client.somme_commandes AS total_commandes_client
          FROM rap_commande rc
          JOIN rap_client c ON rc.cli_num = c.cli_num
          JOIN (
              SELECT cli_num, SUM(com_prix_total) AS somme_commandes
              FROM rap_commande
              GROUP BY cli_num
          ) total_client ON rc.cli_num = total_client.cli_num
          WHERE (rc.cli_num, rc.com_date) IN (
              SELECT cli_num, MAX(com_date)
              FROM rap_commande
              GROUP BY cli_num
          ) and rc.cli_num != 0";

  $tab = array();

  LireDonneesPDO1($conn, $sql, $tab);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapidC3 - Accueil</title>
    <link rel="stylesheet" href="../../styles/styleIndex.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php include_once "../../navbar.php"; ?>

<body>
    <div class="text-center">
        <h1>
            <strong>
                Gestion des clients
            </strong>
        </h1>
    </div>
    <div class="border border-dark-subtle m-2 rounded-bottom">
        <table class="table text-center">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Prenom</th>
                    <th scope="col">Courriel</th>
                    <th scope="col">Date dernière commande</th>
                    <th scope="col">Montant total des commandes</th>
                    <th scope="col">Montant dernière commande</th>
                </tr>
            </thead>
            <tbody>
              <?php
                foreach ($tab as $client) {
                  echo '<tr>
                            <th scope="row">'.$client["CLI_NUM"].'</th>
                            <td>'.$client["CLI_NOM"].'</td>
                            <td>'.$client["CLI_PRENOM"].'</td>
                            <td>'.$client["CLI_COURRIEL"].'</td>
                            <td>'.$client["DERNIERE_COMMANDE_DATE"].'</td>
                            <td>'.$client["TOTAL_COMMANDES_CLIENT"].' €</td>
                            <td>'.$client["PRIX_DERNIERE_COMMANDE"].' €</td>
                        </tr>';
                }
              ?>

            </tbody>
        </table>
    </div>
    <?php include_once "../../footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>