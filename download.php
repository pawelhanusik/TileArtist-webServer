<?php
session_start();
include("functions.php");

include("DB.php");
$db = new DB();

if( isset($_GET['id']) && !empty($_GET['id']) )
{
	$masterpeace_entry = $db->getMasterpiece($_GET['id']);
	
	echo $masterpeace_entry['ID'] . "\n";
	echo $masterpeace_entry['title'] . "\n";
	echo $masterpeace_entry['category'] . "\n";
	echo $db->getUserName($masterpeace_entry['authorID']) . "\n";
	
}
else if( isset($_GET['img']) && !empty($_GET['img']) )
{
	echo file_get_contents("masterpieces/" . $_GET['img'] . ".png");
	//header('Location: masterpieces/' . $_GET['img'] . ".png");
}
else
{
	if( isLoggedIn() ) {
		$IDs = $db->getAllFriendsIDs( $db->getUserID($_SESSION['username']) );
		foreach($IDs as $id){
			echo "$id ";
		}
	} else {
		$IDs = $db->getAllIDs();
		foreach($IDs as $id){
			echo "$id ";
		}
	}
}

?>