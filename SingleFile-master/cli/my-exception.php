<?php
// error_reporting(0);
// ini_set('display_errors', 0);
function writeError($e)
{
	$file = 'bug.txt';
	$date = date("[d/m/Y-h:i:s]\n");
	$message = "\t".'message: '.$e->getMessage();
	$path = "\n\t".'path: '.$e->getFile();
	$line = "\n\t".'line: '.$e->getLine();
	$trace = "\n\t\t".'trace';
	foreach($e->getTrace() as $eTrace){
		$trace = $trace."\n\t\t".$eTrace['line']."\t\t".$eTrace['file'];
	}
	appendTextFile($file, $date.$message.$path.$line.$trace);
}
function writeLog($message)
{
	$file = 'log.txt';
	appendTextFile($file, $message);
}
// writeError(new Exception(''));
file_put_contents('log.txt','');
file_put_contents('bug.txt','');
?>