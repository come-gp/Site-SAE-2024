<?php 
    include_once "header.php";
    unset($_SESSION["USER_ID"]);
    header('Location: '.$accueil);
    exit();
?>