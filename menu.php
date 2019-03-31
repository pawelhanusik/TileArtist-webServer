<?php
if( !isConnectionFromApp() ){
	if( !empty($_SESSION['loggedin']) && isset($_SESSION['loggedin']) )
	{
	?>
	<div class="navbar">
		<a href="panel.php"><?php echo $_SESSION['username']; ?></a>
		<a href="masterpiece.php">Send Masterpiece</a>
		<a href="addFriend.php">Add friends</a>
		<a href="logout.php">Logout</a>
	</div>
	<?php
	}
	else
	{
	?>
	<div class="navbar">
		<a href="login.php">Login</a>
		<a href="register.php">Register</a>
		<a href="releases">Download</a>
		<a href="privacy.php">Privacy Policy</a>
	</div>
	<?php
	}
}
?>