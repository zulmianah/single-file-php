<?php  
function appendTextFile($fileName,$text)
{
	$fp = fopen($fileName, 'a'); 
	fwrite($fp, $text."\n");
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
function checkFolderOrCreate($folder)
{
	if (!file_exists($folder)) {
		mkdir($folder);
	}
	return $folder;
}
function zipFile($host,$directionAndFolder,$directionAndZipFolder)
{
	$rootPath = realpath($directionAndFolder);
	$zip = new ZipArchive();
	$zip->open($directionAndZipFolder.$host.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY
	);
	foreach ($files as $name => $file)
	{
		if (!$file->isDir())
		{
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);
			$zip->addFile($filePath, $relativePath);
		}
	}
	$zip->close();
}
?>  