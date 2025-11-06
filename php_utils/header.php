<?php
	// Connexion BDD et ouverture session PHP
	include_once "pdo_agile.php";
	include_once "param_connexion.php";
	include_once "chemin.php";
	
	define ("MOD_BDD","ORACLE");

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
	
	session_start();
	
	$conn = OuvrirConnexionPDO($db,$db_username,$db_password);
?>
