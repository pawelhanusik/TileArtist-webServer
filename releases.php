<?php
include("header.php");
include("menu.php");
?>

<br>
<p>Download link:</p>
<?php
	foreach (glob("releases/*.apk") as $filename) {
		echo "<p><a href=" . $filename . ">" . substr($filename, strpos($filename, '/')+1, strlen($filename) - 4) . "</a></p>";
	}
?>
<hr width="100%" align=left />

<?php
include("footer.php");
?>