<?php
include("header.php");
include("menu.php");

if( !empty($_POST['email']) ){
	//Update email	
	
	echo "Failed.\n";
	echo "Email left untouched.\n";
}
if( !empty($_POST['password']) ){
	//Update password
	include("DB.php");
	$db = new DB();
	$wasSuccessfull = $db->changePassword( $db->getUserID($_SESSION['username']), $_POST['password'] );
	
	if($wasSuccessfull){
		echo "Success.\n";
		echo "Password updated.\n";
	}else{
		echo "Failed.\n";
		echo "Password left untouched.\n";
	}
}

include("footer.php");
?>