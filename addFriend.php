<?php 	
include("header.php");
include("menu.php");
?>
<?php
//ADD FRIEND
if(isset($_REQUEST['add']) && !empty($_REQUEST['add']))
{
	if( !isLoggedIn() ){
		echo "Please log in to your account.";
		die();
	}
	
	include("DB.php");
	$db = new DB();
	
	if( !$db->addFriend($db->getUserID($_SESSION['username']), $_REQUEST['add']) ) {
		echo "An unknown error occurred. Please try again later.";
		die();
	}
	
	echo "Success";
}
else if(isset($_REQUEST['remove']) && !empty($_REQUEST['remove']))
{
	if( !isLoggedIn() ){
		echo "Please log in to your account.";
		die();
	}
	
	include("DB.php");
	$db = new DB();
	
	if( !$db->removeFriend($db->getUserID($_SESSION['username']), $_REQUEST['remove']) ) {
		echo "An unknown error occurred. Please try again later.";
		die();
	}
	
	echo "Success";
}
//SEARCH FOR FRIEND
else if( isLoggedIn() )
{
	$errorMsg = "";
	include("DB.php");
	$db = new DB();

	if( !isConnectionFromApp() ) {
?>
<form action="addFriend.php" method="post">
	<?php echo $errorMsg . "<br>"; ?>
	<p>Search username: </p>
	<input name="search_username" type="text" placeholder="Username" autofocus>
	<input name="submit" type="submit" value="search">
</form>
<?php
	}
	
	if(isset($_POST['search_username']) /*&& !empty($_POST['search_username'])*/) {
		if($_POST['search_username'] !== ""){
			$searchResults = $db->searchForUsers($_POST['search_username']);
		}else{
			$searchResults = $db->getPopularUsernames();
		}
		foreach($searchResults as $sr){
			if( isConnectionFromApp() ){
				echo $sr['ID'] . ";" . $sr['username'] . "\n";
			}else{
				echo "<p><a href=profile.php?user=" . $sr['ID'] . ">" . $sr['ID'] . " -> " . $sr['username'] . "</a></p>";
			}
		}
	}

}
else
{
	header('Location: index.php');
}
?>
<?php include("footer.php"); ?>