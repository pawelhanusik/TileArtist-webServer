<?php
function isLoggedIn()
{
	return ( !empty($_SESSION['loggedin']) && isset($_SESSION['loggedin']) );
}
function isConnectionFromApp()
{
	return ( isset($_GET['app']) && !empty($_GET['app']) && $_GET['app'] == 1 );
}
function showAds()
{
	return false;
}
?>