<?php
    include_once "../php_utils/header.php";

    $sql = 'SELECT com_date, com_reduc_points, TO_CHAR(com_heure_recup, \'HH24:MI:SS\') AS com_heure_recup, com_duree_totale_prepa, com_prix_total FROM rap_commande
            WHERE CLI_NUM = '.$_SESSION["USER_ID"]. '
            ORDER BY com_date desc';
    
    $coms = array();

    LireDonneesPDO1($conn,$sql,$coms);

    
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>RapidC3 - Historique des Commandes</title>
        <link rel="stylesheet" href="<?php echo $css_histoCom; ?>">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>

        <?php include_once $php_navbar; ?>

        <main class="container mt-5">
            <h2 class="text-center mb-4">Historique des Commandes</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <table class="table table-bordered table-striped" id="historiqueTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Date Commande</th>
                                <th>Montant Commande</th>
                                <th>Points Gagnés</th>
                                <th>Points Utiliséés</th>
                                <th>Durée de la préparation</th>
                                <th>Heure de récupération</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($coms as $com){
                                    //$sql2 = 'SELECT com_date, com_heure_recup, com_reduc_points, com_duree_totale_prepa, com_prix_total FROM rap_fidelisation
                                    //        WHERE CLI_NUM = '.$_SESSION["USER_ID"];
    
                                    //$coms2 = array();

                                    //LireDonneesPDO1($conn,$sql2,$coms2);
                                    echo '<tr>
                                            <td>'. $com["COM_DATE"] .'</td>
                                            <td>'. $com["COM_PRIX_TOTAL"] .'</td>
                                            <td>'. $com["COM_REDUC_POINTS"] .'</td>
                                            <td>'. $com["COM_REDUC_POINTS"] .'</td>
                                            <td>'. $com["COM_DUREE_TOTALE_PREPA"] .'</td>
                                            <td>'. $com["COM_HEURE_RECUP"] .'</td>
                                        </tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                    <div class="espace">
                        <?php echo 
                                '<a href="'.
                                    $gestionCli.'?idClient='.$_SESSION["USER_ID"].'" class="btn btn-warning">
                                    Retour au compte
                                    </a>'; 
                        ?>
                    </div>
                </div>
            </div>
        </main>

        <?php include_once $php_footer; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
