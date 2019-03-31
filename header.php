<?php
session_start();
include("functions.php");

if(!isConnectionFromApp()){
?>

<!DOCTYPE html>
<html>
<head>
	<!--<meta http-equiv="refresh" content="1" >-->
	<meta charset="UTF-8">
	<title>Tile Artist</title>
	<meta name="author" content="Gemstones">
	<link rel="icon" href="imgs/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="styl.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>

<div>
	<a href="."> <img src="imgs/bg_top.png" width="100%" alt="Logo"> </a>
</div>
<?php
}
?>