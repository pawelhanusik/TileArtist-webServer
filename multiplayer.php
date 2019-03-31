<?php
function isConnectionFromApp()
{
	return ( isset($_GET['app']) && !empty($_GET['app']) && $_GET['app'] == 1 );
}


if( !isConnectionFromApp() ){
	die("");
}

if( !isset($_GET['mode']) ){
	echo "Incorrect mode";
	echo $_GET['mode'];
	die();
}

$mode = $_GET['mode'];
session_start();
include("DB.php");
$db = new DB();
if( $mode == 0 )
{
	//INIT
	if( !isset($_POST['imageID']) ) {
		echo "Image not selected.";
		die();
	}
	if( !isset($_POST['opponent']) ) {
		echo "Opponent not selecter.";
		die();
	}
	
	$imageID = $_POST['imageID'];
	
	$player1_name = $_SESSION['username'];
	$player2_name = $_POST['opponent'];
	
	if ( !$db->usernExists($player2_name) ){
		echo "Opponent with given nickname doesn't exists.";
		die();
	}
	
	$player1_id = $db->getUserID($player1_name);
	$player2_id = $db->getUserID($player2_name);
	
	if( $player1_id === false || $player2_id === false) {
		echo "Bad players ids.";
		die();
	}
	
	if ( $db->multiplayerGames_initNew($imageID, $player1_id, $player2_id) === false){
		echo "Unknown error occured.";
		die();
	}
	
	echo "Success";	
}
else if( $mode == 1 )
{
	//START
	if( !isset($_GET['gameID']) ){
		echo "Unselected gameID.";
		die();
	}
	$imageID = $db->multiplayerGames_getImageID($_GET['gameID']);
	$_SESSION['multiplayer_time_start'] = microtime(true);
	echo $imageID;
}
else if( $mode == 2 )
{
	//FINISH
	if( !isset($_GET['gameID']) ){
		die();
	}
	if( !isset($_SESSION['username']) ){
		die();
	}
	
	$timeDiff = round( (microtime(true) - $_SESSION['multiplayer_time_start']) * 1000);
	if($timeDiff <= 0){
		die();
	}
	if($timeDiff >= 2147483647){
		die();
	}
	
	$playerID = $db->getUserID($_SESSION['username']);
	
	if( $db->multiplayerGames_update($playerID, $_GET['gameID'], $timeDiff) ){
		echo "Success";
	}else{
		echo "Error";
	}
}
else if( $mode == 3 )
{
	//CKECK
	if( !isset($_SESSION['username']) ){
		echo "ERRPlease login to your account.";
		die();
	}
	
	$games = $db->multiplayerGames_check($_SESSION['username']);
	for($i = 0; $i < count($games); ++$i ){
		echo $games[$i]['ID'] . ';' . $games[$i]['imageID'] . ';' .
		$games[$i]['player1_name'] . ';' . $games[$i]['player2_name'] . ';' .
		$games[$i]['player1_time'] . ';' . $games[$i]['player2_time'] . "\n";
	}
}

?>