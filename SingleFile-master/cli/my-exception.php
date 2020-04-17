<?php
function writeError($e)
{
	$file = 'bug.txt';
	$date = date("[d/m/Y-h:i:s]\n");
	$message = "\tmessage: ".$e->getMessage();
	$path = "\n\tpath: ".$e->getFile();
	$line = "\n\tline: ".$e->getLine();
	$trace = "\n\t\ttrace";
	foreach($e->getTrace() as $eTrace){
		$trace = $trace."\n\t\t".$eTrace['line']."\t\t".$eTrace['file'];
	}
	appendTextFile($file, $date.$message.$path.$line.$trace);
}
?>