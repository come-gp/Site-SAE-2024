<?php include_once "../php_utils/header.php";

$login = $_POST["login"];
$password = $_POST["password"];


$sql = 'SELECT cli_num from rap_credentials where cre_login = :login and cre_mdp = :password';

$stmt = $conn->prepare($sql);

$stmt->bindParam(':login', $login);
$stmt->bindParam(':password', $password);

$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);



if ($row !== false) {
    $cli_num = $row['CLI_NUM'];
    $_SESSION["USER_ID"] = $cli_num;
    header('Location: '.$accueil);
	exit();

} else {
    $_SESSION['incorrect_login_datas']="incorrect_login_datas";
    $_SESSION['previous_username']=$login;
    header('Location: '.$connexion);
	exit();
}
exit();   
?>