<?php include("header.php"); ?>
<?php include("menu.php"); ?>

<?php
if(isLoggedIn()){
	?>
	
<form action="upload.php" method="post" enctype="multipart/form-data">
	<?php if( !empty($_REQUEST['err']) && isset($_REQUEST['err']) ) echo "<div class='error'>" . $_REQUEST['err'] . "</div><br>"; ?>
	<legend><h3>Send your Masterpiece</h3></legend>
	<div>
		<input name="title" type="text" placeholder="Tytul" autofocus>
	</div>
	<div>
		<input name="author" type="text" placeholder="Autor">
	</div>
	<div>
		<select name="category">
			<?php
				include('DB.php');
				$db = new DB();
				$categories = $db->getAllCategories();
				foreach($categories as $c){
					echo "<option value='$c'>$c</option>\n";
				}
			?>
		</select>
	</div>
	<div>
		<input name="file" type="file" id='file'>
	</div>
	<div>
		<input name="submit" type="submit" value="Wyślij">
	</div>
</form>
	
	<?php
}else{
	header("Location: /index.php");
}
?>

<?php include("footer.php"); ?>