<?php
if (isset($_REQUEST[session_name()])) session_start();
$page = 'index';
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title> Крестики-нолики </title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<?php include ("connect_db.php");?>
		<header> Крестики-нолики </header>
    <menu></menu>
    <footer> </footer>
	</body>
</html>