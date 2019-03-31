<?php include("header.php"); ?>
<?php include("menu.php"); ?>
<?php
if( isLoggedIn() )
{
	include("DB.php");
	$db = new DB();
	$res = $db->getAuthorsMasterpieces($db->getUserID($_SESSION['username']));
	
	if( !isConnectionFromApp() )
	{
?>
<p>Panel użytkownika.</p><br>
<p>Nazwa użytkownika: <?php echo $_SESSION['username']; ?></p><br>
<p>Galeria:</p><br>
<?php
		foreach($res as $mp){
			?>
			<div class="masterpiece">
				<p><?php echo $mp['title']; ?></p>
				<img src="<?php echo "masterpieces/" . $mp['ID'] . ".png"; ?>" alt="<?php echo $mp['title']; ?>" />
				<p><?php echo $mp['author']; ?></p>
				<p><?php echo $mp['category']; ?></p>
				<hr>
			</div>
			<?php
		}
?>
<?php
	}
	else
	{
		///MOBILE APP
		echo $db->getUserID($_SESSION['username']) . "\n";
		echo $_SESSION['username'] . "\n";
		/*if( $db->isAFollowingB($db->getUserID($_SESSION['username']), $_SESSION['username']) ) {
			echo "1\n";
		}else{
			echo "0\n";
		}*/
		echo "2\n";
		
		foreach($res as $mp){
			echo $mp['ID'] . "\n";
			echo $mp['title'] . "\n";
			echo $mp['category'] . "\n";
			echo "=====\n";
		}
	}
}
else
{

	header('Location: index.php');

}
?>
<?php include("footer.php"); ?>