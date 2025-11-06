<?php  
  include_once "../../php_utils/header.php";
  include_once $php_secure;
 
  if (isset($_GET["idClient"])) {
    $id = $_GET["idClient"];
  } else {
    header('Location: '.$gestion);
    exit();
  }
 
  $sql = 'SELECT com_date, com_heure_recup, com_reduc_points, com_duree_totale_prepa, com_prix_total FROM rap_commande
                WHERE CLI_NUM = :id';

  $stmt = $conn->prepare($sql);
 
  $stmt->bindParam(':id', $id);
 
  $stmt->execute();
 
  $coms = $stmt->fetchALL(PDO::FETCH_ASSOC);
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
                                <th>Points Utilisés</th>
                                <th>Durée de la préparation</th>
                                <th>Heure de récupération</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($coms as $com){
                                    echo '<tr>
                                            <td>'. $com["COM_DATE"] .'</td>
                                            <td>'. $com["COM_PRIX_TOTAL"] .'</td>
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
                                    $gestionCli.'?idClient='.$id.'" class="btn btn-warning">
                                    Retour au compte client
                                    </a>'; 
                        ?>
                    </div>
                    <div class="espace">
                        <?php echo 
                                '<a href="'.
                                    $gestion.'" class="btn btn-secondary">
                                    Retourner à la liste
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