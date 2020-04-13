<?php 

require 'single-file.php';
singlefile($_POST["name"]);
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".dirname($_SERVER['PHP_SELF']);
$principal_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"."/singlefile";
?>
<html>
<body>
	<form action="<?php echo $actual_link; ?>/download.php" method="GET">
		singlefile pret
		<input type="submit" value="telecharger">
	</form>
	<a href="<?php echo $principal_link; ?>">revenir</a>
</body>
</html>