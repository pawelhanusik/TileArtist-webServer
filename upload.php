<?php include("header.php"); ?>
<?php include("menu.php"); ?>
<?php
function throwError($msg)
{
	if( !isConnectionFromApp() ){
		header('Location: masterpiece.php?err=' . htmlspecialchars($msg));
		exit();
	}else{
		echo $msg;
		die();
	}
}

///REMOVE Masterpiece
if( isset($_GET['del']) && !empty($_GET['del']) ) {
	if( !isLoggedIn() ) {
		throwError("You are not logged into iour account.");
	}
	
	include("DB.php");
	$db = new DB();
	
	if( $db->removeEntry($_GET['del'], $db->getUserID($_SESSION['username'])) ){
		echo "Success";
	}else{
		echo "Unknown error occured.";
	}
	
	die();
}
///END OF REMOVE


if(	$_FILES['file']['error'] !== UPLOAD_ERR_OK ){
	throwError("You have to select a file to upload.");
}

$title = "untitled";
$category = "Uncategorized";
$author = "unknown";

if(	isset($_POST['title']) && !empty($_POST['title']) ){
	$title = $_POST['title'];
}
if(	isset($_POST['category']) && !empty($_POST['category']) ){
	$category = $_POST['category'];
}
if(	isset($_SESSION['username']) && !empty($_SESSION['username']) ){
	$author = $_SESSION['username'];
}else{
	throwError("You have to be logged in to add a Masterpiece.");
}

include("DB.php");
$db = new DB();
$target_dir = "masterpieces/";
$target_id = $db->getFirstFreeID();
$target_file = $target_dir . $target_id . ".png";//basename($_FILES["file"]["name"]);

$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if($check === false) {
        throwError("File is not an image.");
	}
}

// Allow certain file formats
if($imageFileType != "png") {
    throwError("Only PNG files are allowed.");
}
// Check if file already exists
if (file_exists($target_file)) {
    //throwError("Wystąpił (wiel)błąd. Proszę zgłoś go administracji.");
	rename ( $target_file , "dropped/" . $target_file . date('_d-m-Y_H-i-s_') . rand() . ".png" );
}

if ( !move_uploaded_file($_FILES["file"]["tmp_name"], $target_file) ) {
	throwError("Unfortunately, an error occured while receiving your Masterpiece. Please try again later.");
}

//TODO: check if adding new entry works(after change author to authorID)
if( !$db->addNewEntry($title, $category, $db->getUserID($author)) ){
	throwError("Nobody knows why, but an awful error occured.");
}
//echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";

$created_entry = $db->getMasterpiece($target_id);

if( isConnectionFromApp() )
{
	echo "Success";
}
else
{
?>
<div class="masterpiece">
	<p><?php echo $created_entry['title']; ?></p>
	<img src="<?php echo $target_file; ?>" alt="<?php echo $created_entry['title']; ?>" />
	<p><?php echo $created_entry['author']; ?></p>
	<p><?php echo $created_entry['category']; ?></p>
</div>
<?php 
}
include("footer.php");
?>