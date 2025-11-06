<?php
include_once "header.php";

// Vérification des champs
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Préparation des requêtes
    $stmt1 = $conn->prepare("update rap_commande set cli_num = 0 where cli_num = :id");
    $stmt2 = $conn->prepare("delete from rap_fidelisation where cli_num = :id");
    $stmt3 = $conn->prepare("delete from rap_credentials where cli_num = :id");
    $stmt4 = $conn->prepare("delete from rap_client where cli_num = :id");

    $params = [':id' => $id];

    // Exécution des requêtes
    $success1 = $stmt1->execute($params);
    $success2 = $stmt2->execute($params);
    $success3 = $stmt3->execute($params);
    $success4 = $stmt4->execute($params);
}
header('Location: '.$gestion);
exit();
?>
