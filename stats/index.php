<?php
include_once "../pdo_agile.php";
include_once "../param_connexion.php";
//define ("MOD_BDD","MYSQL");
define("MOD_BDD", "ORACLE");

if (MOD_BDD == "MYSQL") {
    $db_username = $db_usernameMySQL;
    $db_password = $db_passwordMySQL;
    $db = $dbMySQL;
} else {
    $db_username = $db_usernameOracle;
    $db_password = $db_passwordOracle;
    $db = $dbOracle;
}

$conn = OuvrirConnexionPDO($db, $db_username, $db_password);

$sqlClients = "Select cli_nom, cli_prenom, sum(com_prix_total) as total, count(*) as nb_cmd from rap_client join rap_commande using(cli_num) where cli_num != 0 group by(cli_nom, cli_prenom) order by total desc fetch first 5 rows only";
$Clients = array();
LireDonneesPDO1($conn, $sqlClients, $Clients);

$sqlVentes = "Select to_char(rap_commande.com_date, 'mm/yyyy') as mois, count(*) as ventes from rap_commande group by to_char(rap_commande.com_date, 'mm/yyyy')";
$Ventes = array();
LireDonneesPDO1($conn, $sqlVentes, $Ventes);

$ventesParMois = [];
foreach ($Ventes as $row) {
    $ventesParMois[$row["MOIS"]] = (int)$row["VENTES"];
}
$start = new DateTime("2022-11-01");
$end = new DateTime();
$end->modify("first day of next month");

$labels = [];
$values = [];

while ($start < $end) {
    $mois1 = $start->format("m/Y");
    $m1_date = clone $start;
    $start->modify("+1 month");

    $mois2 = $start->format("m/Y");
    $start->modify("+1 month");

    $mois3 = $start->format("m/Y");
    $start->modify("+1 month");

    $label = DateTime::createFromFormat("m/Y", $mois1)->format("M Y") . " - " . DateTime::createFromFormat("m/Y", $mois3)->format("M Y");
    $labels[] = $label;

    $total = 0;
    foreach ([$mois1, $mois2, $mois3] as $mois) {
        $total += $ventesParMois[$mois] ?? 0;
    }
    $values[] = $total;
}

$sqlProduits = "select pla_nom, sum(app_quantite) as total_q from rap_commande join rap_appartenir using(res_num, com_num) join rap_plat using(pla_num) group by pla_nom order by total_q desc fetch first 5 rows only";
$Produits = array();
LireDonneesPDO1($conn, $sqlProduits, $Produits);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapidC3 - Statistiques</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../styles/style1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<?php
include '../navbar.php'
?>

<body>

    <h1 class="text-center mt-5 fw-bold">Statistiques</h1>
    <div class="row g-0 mt-5">
        <div class="col w-auto d-flex justify-content-center">

            <?php
            echo  '<div class="card-wrapper w-auto">
        <h2 class="card-title text-center mb-2">Meilleurs Clients</h2>
                        <div class="card">
                            
                            <div class="card-body">
                                <ul class="list-group list-group-flush">';
            foreach ($Clients as $c) {
                echo '<li class="list-group-item">' . $c['CLI_NOM'] . ' ' . $c['CLI_PRENOM'] . ' : <span class="fw-bold">' . $c['TOTAL'] . '€</span> (' . $c['NB_CMD'] . ' commandes)</li>';
            }

            echo '</ul>
                            </div> 
                        </div>
                    </div>';
            ?>
        </div>

        <div class="col d-flex justify-content-center ">
            <div class="text-center">
                <h2 class="">Ventes par trimestre</h2>
                <canvas id="ventesChart" width="800" height="400"></canvas>
            </div>

        </div>
    </div>
    </div>
        <div class="row g-0 mt-5">
            <div class="col d-flex justify-content-center">
                <h2>Produits les plus vendus</h2>

            </div>
        </div>





    <script>
        const ctx = document.getElementById('ventesChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($values) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>




</body>

<?php
include '../footer.php'
?>