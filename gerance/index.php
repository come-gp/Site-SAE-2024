<?php
include '../php_utils/header.php';
include $php_secure;


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

$sqlProduits = "select pla_nom, sum(app_quantite) as total_q from rap_commande join rap_appartenir using(res_num, com_num) join rap_plat using(pla_num) group by pla_nom order by total_q desc fetch first 10 rows only";
$Produits = array();
LireDonneesPDO1($conn, $sqlProduits, $Produits);

$noms = json_encode(array_column($Produits, 'PLA_NOM'));
$quantites = json_encode(array_column($Produits, 'TOTAL_Q'));

$sqlMarges = "select distinct pla_nom, pla_prix_vente_unit_ht - pla_prix_achat_unit_ht as marge from rap_plat order by marge desc fetch first 5 rows only";
$Marges = array();
LireDonneesPDO1($conn, $sqlMarges, $Marges);
$filtre = isset($_GET['inactif']) ? intval($_GET['inactif']) : 0;

$conditionInactif = "";
if ($filtre === 2) {
    $conditionInactif = "AND (SYSDATE - (
        SELECT MAX(com_date)  
        FROM rap_commande rc2  
        WHERE rc2.cli_num = c.cli_num
    ) > 365*2)";
} elseif ($filtre === 4) {
    $conditionInactif = "AND (
        (SELECT MAX(com_date)  
         FROM rap_commande rc2  
         WHERE rc2.cli_num = c.cli_num) IS NULL  
         OR SYSDATE - (
            SELECT MAX(com_date)  
            FROM rap_commande rc2  
            WHERE rc2.cli_num = c.cli_num
         ) > 365*4
    )";
}

$sql = "SELECT  
            c.cli_num,  
            c.cli_nom,  
            c.cli_prenom,  
            c.cli_courriel,  
            rc.com_date AS derniere_commande_date,  
            rc.com_prix_total AS prix_derniere_commande,
            total_client.somme_commandes AS total_commandes_client
        FROM rap_client c
        LEFT JOIN rap_commande rc ON c.cli_num = rc.cli_num
            AND (rc.cli_num, rc.com_date) IN (
                SELECT cli_num, MAX(com_date)
                FROM rap_commande
                GROUP BY cli_num
            )
        LEFT JOIN (
            SELECT cli_num, SUM(com_prix_total) AS somme_commandes
            FROM rap_commande
            GROUP BY cli_num
        ) total_client ON c.cli_num = total_client.cli_num
        WHERE c.cli_num != 0 AND c.cli_num != 1
        $conditionInactif
        ORDER BY cli_nom, cli_prenom";

$tab = array();
LireDonneesPDO1($conn, $sql, $tab);

$sql = 'SELECT com_date, TO_CHAR(com_heure_recup, \'HH24:MI:SS\') as com_heure_recup, com_reduc_points, com_duree_totale_prepa, com_prix_total FROM rap_commande';

$coms = array();

LireDonneesPDO1($conn, $sql, $coms);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapidC3 - Dashboard</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?php echo $css_style1; ?>">
    <link rel="stylesheet" href="<?php echo $css_gestion; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $css_stats; ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</head>
<?php
include $php_navbar;
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 p-3 rounded m-4 sidebar-custom text-white">

                <h4 class="text-center mb-4 text-decoration-underline fw-bolder fs-3">Dashboard</h4>
                <h2 class="" style="padding-left:15px">
                    <a href="#" style="padding:5px" class="fw-bold fs-4 text-white text-decoration-none liens-stat" data-target="campagne">Campagnes</a>
                </h2>
                <h2 class="" style="padding-left:15px">
                    <a href="#" style="padding:5px" class="fw-bold fs-4 text-white text-decoration-none liens-stat" data-target="commandes">Commandes</a>
                </h2>
                <h2 class="" style="padding-left:15px">
                    <a href="#" style="padding:5px" class="fw-bold fs-4 text-white text-decoration-none liens-stat" data-target="gestion">Clients</a>
                </h2>

                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header fs-4" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <span class="fw-bold fs-4">Statistiques</span>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body text-white">
                                <ul class="pt-0 list-unstyled ">
                                    <li class="mb-4">
                                        <a href="#" class="p-2 text-white text-decoration-none liens-stat" data-target="clients"><i class="bi bi-bar-chart-fill me-2"></i>Meilleurs Clients</a>
                                    </li>
                                    <li class="mb-4">
                                        <a href="#" class="p-2 text-white text-decoration-none liens-stat" data-target="ventes"><i class="bi bi-bar-chart-fill me-2"></i>Ventes par trimestre</a>
                                    </li>
                                    <li class="mb-4">
                                        <a href="#" class="p-2 text-white text-decoration-none liens-stat" data-target="produits"><i class="bi bi-bar-chart-fill me-2"></i>Produits les plus vendus</a>
                                    </li>
                                    <li class="mb-4">
                                        <a href="#" class="p-2 text-white text-decoration-none liens-stat" data-target="marges"><i class="bi bi-bar-chart-fill me-2"></i>Produits avec les meilleures marges</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="col offset-md-1 mt-4">
                <div class="g-0 mt-5">
                    <div class="w-auto d-flex justify-content-center point-stat d-none" id="clients">

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


                </div>
                <div class="g-0 mt-5">
                    <div class="justify-content-center point-stat d-none" id="campagne">
                        <h2 class="text-center fw-bold">Envoyer une campagne promotionnelle</h2>
                        <form id="mailForm" action="./send_mail.php" method="POST" class="mx-auto" style="max-width: 500px;">
                            <div class="mb-3 text-start">
                                <label for="email" class="form-label"><i>Adresse Mail</i></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="nom@exemple.com" required>
                            </div>
                            <div class="mb-3 text-start">
                                <label for="objet" class="form-label"><i>Objet</i></label>
                                <input type="objet" class="form-control" id="objet" name="objet" required>
                            </div>
                            <div class="mb-3 text-start">
                                <label for="message" class="form-label"><i>Message promotionnel</i></label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">Envoyer</button>
                            </div>
                        </form><br>
                        <div class="justify-content-center text-center">
                            <blockquote><b><i>Un envoi, une portée totale : tous nos clients informés !</i></b></blockquote>
                            <div class="center-buttons">
                                <button type="submit" disabled="true" class="btn btn-warning">Envoyer à tous les clients !</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex-column justify-content-center ms-3 point-stat d-none" id="produits">
                        <h2 class="text-center">Produits les plus vendus</h2>
                        <canvas id="produitsChart" width="1200" height="800" style="max-width: 100%; height: auto;"></canvas>
                    </div>
                    <div class="flex-column justify-content-center ms-3 point-stat d-none" id="ventes">
                        <h2 class="text-center">Ventes par trimestre</h2>
                        <canvas id="ventesChart" width="1200" height="800" style="max-width: 100%; height: auto;"></canvas>

                    </div>
                    <div class="w-auto d-flex justify-content-center point-stat d-none" id="marges">
                        <?php
                        echo  '<div class="card-wrapper w-auto">
                            <h2 class="card-title text-center mb-2">Produits avec les meilleures marges</h2>
                        <div class="card">
                             
                            <div class="card-body">
                                <ul class="list-group list-group-flush">';
                        foreach ($Marges as $c) {
                            echo '<li class="list-group-item text-center">' . $c['PLA_NOM'] . ' : <span class="fw-bold">' . $c['MARGE'] . '€</span></li>';
                        }

                        echo '</ul>
                            </div>  
                        </div>
                    </div>';
                        ?>
                    </div>
                </div>

                <div class="g-0 mt-5">
                    <div class="wrapper d-flex flex-column min-vh-100 point-stat" id="gestion">

                        <div class="container mt-4">
                            <div class="text-center">
                                <h1><strong>Gestion des clients</strong></h1>
                            </div>
                            <div class="text-center my-3">
                                <a href="<?php echo $gestion; ?>" class="btn btn-outline-secondary mx-1">Tous les clients</a>
                                <a href="<?php echo $gestion; ?>?inactif=2" class="btn btn-warning mx-1">Inactifs (2 ans)</a>
                                <a href="<?php echo $gestion; ?>?inactif=4" class="btn btn-danger mx-1">Inactifs (4 ans)</a>
                            </div>


                            <div class="border border-dark-subtle m-2 rounded-bottom">
                                <main class="flex-fill">
                                    <table class="table text-center table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom</th>
                                                <th>Prénom</th>
                                                <th>Courriel</th>
                                                <th>Date dernière commande</th>
                                                <th>Total commandes (€)</th>
                                                <th>Dernière commande (€)</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($tab as $client) {
                                                echo '<tr>
                    <td>' . htmlspecialchars($client["CLI_NUM"]) . '</td>
                    <td>' . htmlspecialchars($client["CLI_NOM"]) . '</td>
                    <td>' . htmlspecialchars($client["CLI_PRENOM"]) . '</td>
                    <td>' . htmlspecialchars($client["CLI_COURRIEL"]) . '</td>
                    <td>' . ($client["DERNIERE_COMMANDE_DATE"] ?? 'Aucune') . '</td>
                    <td>' . number_format((float)str_replace(',', '.', $client["TOTAL_COMMANDES_CLIENT"]), 2, ',', ' ') . ' €</td>
                    <td>' . number_format((float)str_replace(',', '.', $client["PRIX_DERNIERE_COMMANDE"]), 2, ',', ' ') . ' €</td>
                    <td>
                                <a href="' . $gestionCli . '?idClient=' . $client["CLI_NUM"] . '" class="btn btn-light">
                                    <img src="' . $img . 'edit.png" alt="Modifier" class="edit">
                                </a>
                            </td>
                    </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </main>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="g-0 mt-5">
                    <div class="flex-column justify-content-center ms-3 point-stat d-none" id="commandes">
                        <h1 class="text-center"><strong>Historique des commandes</strong></h1>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <table class="table table-bordered table-striped" id="historiqueTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Date Commande</th>
                                            <th>Montant Commande</th>
                                            <th>Points Utilisés</th>
                                            <th>Durée de la préparation</th>
                                            <th>Heure de récupération</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($coms as $com) {
                                            echo '<tr>
                                    <td>' . $com["COM_DATE"] . '</td>
                                    <td>' . $com["COM_PRIX_TOTAL"] . '</td>
                                    <td>' . $com["COM_REDUC_POINTS"] . '</td>
                                    <td>' . $com["COM_DUREE_TOTALE_PREPA"] . '</td>
                                    <td>' . $com["COM_HEURE_RECUP"] . '</td>
                                </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.liens-stat').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.point-stat').forEach(section => {
                    section.classList.add('d-none');
                });
                const targetId = this.getAttribute('data-target');
                const target = document.getElementById(targetId);
                if (target) {
                    target.classList.remove('d-none');
                }
            });
        });
    </script>
    <script>
        // envoi le mail
        document.getElementById("mailForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert("Le message a bien été envoyé ! ✅");
                    form.reset();
                })
                .catch(error => {
                    alert("Une erreur est survenue lors de l'envoi ❌");
                    console.error("Erreur :", error);
                });
        });
    </script>

    <script>
        // Ventes par trimestre
        let ctx = document.getElementById('ventesChart').getContext('2d');
        let chart = new Chart(ctx, {
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

        // Produits les plus vendus
        ctx = document.getElementById('produitsChart').getContext('2d');
        const data = {
            labels: <?= $noms ?>,
            datasets: [{
                axis: 'y',
                data: <?= $quantites ?>,
                fill: false,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            }]
        };
        chart = new Chart(ctx, {
            type: 'bar',
            data,
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
            }
        });
    </script>
</body>

<?php
include $php_footer;
?>