<?php
    include_once "header.php";
    if (array_key_exists('USER_ID', $_SESSION)) {
        header('Location: '.$accueil);
    }
?>