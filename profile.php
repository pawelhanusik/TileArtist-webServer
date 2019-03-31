<?php include("header.php"); ?>
<?php include("menu.php"); ?>
<?php
if( isLoggedIn() )
{
	$errorMsg = "";
	include("DB.php");
	$db = new DB();

	if(isset($_REQUEST['user']) && !empty($_REQUEST['user'])) {
		
		if($db->userExistsID($_REQUEST['user'])){
			$res = $db->getAuthorsMasterpieces($_REQUEST['user']);
			
			if( !isConnectionFromApp() )
			{
				//WEB
				?>
				
				<p>Username: <?php echo $db->getUserName($_REQUEST['user']); ?></p>
				<?php
				if( $db->isAFollowingB($db->getUserID($_SESSION['username']), $_REQUEST['user']) ) {
					echo "<a>FOLLOWING</a>";
				} else {
					echo "<a href='addFriend.php?add=" . $_REQUEST['user'] . "'>FOLLOW <3</a>";
				}
				?>
				<p>Masterpieces:</p><br>
				<?php
				///WEB
				foreach($res as $mp){
					?>
					<div class="masterpiece">
						<p><?php echo $mp['title']; ?></p>
						<img src="<?php echo "masterpieces/" . $mp['ID'] . ".png"; ?>" alt="<?php echo $mp['title']; ?>" />
						<p><?php echo $mp['category']; ?></p>
						<hr>
					</div>
					<?php
				}
			}
			else
			{
				///MOBILE APP
				echo $_REQUEST['user'] . "\n";
				echo $db->getUserName($_REQUEST['user']) . "\n";
				if( $db->isAFollowingB($db->getUserID($_SESSION['username']), $_REQUEST['user']) ) {
					echo "1\n";
				}else{
					echo "0\n";
				}
				
				foreach($res as $mp){
					echo $mp['ID'] . "\n";
					echo $mp['title'] . "\n";
					echo $mp['category'] . "\n";
					echo "=====\n";
				}
			}
			?>
		<?php
		}else{
			echo "This user doesn't exists.";
		}
	}

}
else
{
	header('Location: index.php');
}
?>
<?php include("footer.php"); ?>