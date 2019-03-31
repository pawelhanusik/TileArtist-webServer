<?php
include("header.php");
include("menu.php");
?>
<?php	
	$errorMsg = "";
	$displayForm = true;
	
	if( !empty($_REQUEST) 
		&& !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['password2'])
		&& isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2'])
		)
	{		
		if($_POST['password'] != $_POST['password2']){
			$errorMsg = "Passwords doesn't match.";
		}else if( strpos($_POST['username'], ";") !== false ){
			$errorMsg = "Semicolons aren't allowed in nicknames.";
		}else{
			include('DB.php');
			$db = new DB();
			$res = $db->addUser($_POST['username'], $_POST['password']);
			if($res != 0){
				if($res == -1){
					$errorMsg = "User with the same username already exists.";
				}else{
					$errorMsg = "Unknown error ocured. Please try again later :(";
				}
			}else{
				//All went nicely and user was created
				$displayForm = false;
				
				if( !isConnectionFromApp() ){
				?>
				
				<p>Username <?php echo $_POST['username']; ?> created.</p>
				<form action="index.php">
					<input name="submit" type="submit" value="OK">
				</form>
				
				<?php
				}else{
					echo "Success";
				}
			}
		}
		
	}
	if($displayForm)
	{
		if( !isConnectionFromApp() ){
			?>
<form action="register.php" method="post">
	<?php echo $errorMsg . "<br>"; ?>
	<legend><h3>Register</h3></legend>
	<div>
		<input name="username" type="text" placeholder="Login" required autofocus>
	</div>
	<div>
		<input name="password" type="password" placeholder="Hasło" required>
	</div>
	<div>
		<input name="password2" type="password" placeholder="Powtórz hasło" required>
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
<?php include("footer.php"); ?>