<?php  
function appendTextFile($fileName,$text)
{
	$fp = fopen($fileName, 'a'); 
	fwrite($fp, $text.',');
	fclose($fp); 
}  
function getTextFile($fileName)
{

	return $text;
}
function getLinksFromFile($fileName)
{
	$text = getTextFile($fileName);
	return explode("\n", $text);
}
function linkToFolder($host){
	return $host.'/';
}
?>  