<?php
	include("header.php");
	include("menu.php");
	/*if( isset($_GET['app']) && !empty($_GET['app']) ) {
		include("functions.php");
	}*/

	$errorMsg = "";
	$displayForm = true;
	
	/*if(isConnectionFromApp()){
		session_start();
	}*/
	
	if( !empty($_REQUEST)
		&& !empty($_POST['username']) && !empty($_POST['password'])
		&& isset($_POST['username']) && isset($_POST['password'])
		)
	{		
		include('DB.php');
		$db = new DB();
		$res = $db->verifyUser($_POST['username'], $_POST['password']);
		if($res){
			//Password correct
			$displayForm = false;
			$_SESSION['loggedin'] = true;
			$_SESSION['username'] = $_POST['username'];
			if( !isConnectionFromApp() ){
				echo "You logged in successfully!";
				echo "As " . $_SESSION['username'];
				header('Location: panel.php');
			}else{
				echo "Success";
			}
			
			$db->addHytra($db->getUserID($_SESSION['username']), $_SERVER['REMOTE_ADDR']);
		}else{
			$errorMsg = "Incorrect login or password.";
			$db->addHytra(-1, $_SERVER['REMOTE_ADDR']);
		}
	}
	
	if($displayForm)
	{
		if( !isConnectionFromApp() ){
			?>
<form action="login.php" method="post">
	<?php echo $errorMsg . "<br>"; ?>
	<legend><h3>Login</h3></legend>
	<div>
		<input name="username" type="text" placeholder="Login" required autofocus>
	</div>
	<div>
		<input name="password" type="password" placeholder="Password" required>
	</div>
	<div>
		<input name="submit" type="submit" value="Login">
	</div>
</form>
<?php
		}else{
			echo $errorMsg;
		}
	}
?>
<?php
//DONT DISPLAY ADS:
if( !showAds() ){
	echo ".";
}

include("footer.php");
?>