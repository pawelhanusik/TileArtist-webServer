<?php
include("header.php");
include("menu.php");

if( isLoggedIn() ){
	include("DB.php");
	$db = new DB();
	foreach($db->getFollows($db->getUserID($_SESSION['username'])) as $row) {
		echo $row['followedID'] . ";" . $row['username'] . "\n";
	}
}else{
	echo "Please log in.";
}

include("footer.php");
?>