<?php
	$files = array_merge(glob("*.html"), glob("*.html"));
	$files = array_combine($files, array_map("filemtime", $files));
	arsort($files);
	$latest_file = key($files);
	header('Content-disposition: attachment; filename=' . $latest_file);
	header('Content-type: text/html');
	readfile($latest_file);
	// header("Location: ".$actual_link);
	// exit();
?>