<?php
require 'scrap.php';
require 'my-exception.php';
require 'single-file.php';
// $status['extractedLinks']	les liens a telecharger
// $status['files']				les noms de fichiers telecharges
// dossier ou sont stockes les sites hors ligne
$direction = '../../my-single-file-website/';
// fonction pour avoir les liens possibles d'un site
function scrapLinksWebsite($link, $host,$extractedLinks,$i,$t1,$status,$directionAndFolder)
{
	$stop=1;
	$html = '';
	try{
		$html = file_get_contents($link);
		$status['extractedLinks'] = extracteLinks($link, $host, $html, $status['extractedLinks']);
	}catch(Exception $exception){
		$i++;
		return $status;
	}
	$i++;
	$linksLength = sizeof($status['extractedLinks']);
	for ($i; $i < $linksLength; $i++) { 
		if((time()-$t1)/60>$stop){
			return $status;
		}
		$status = scrapLinksWebsite($status['extractedLinks'][$i], $host,$status['extractedLinks'],$i,$t1,$status,$directionAndFolder);
	}
	return $status;
}
// fonction pour demarer l'absorption des liens possibles d'un site
function startScrapLinksWebsite($link,$host,$direction,$directionAndFolder){
	$status = array();
	$status['files']=array();
	if (!file_exists($directionAndFolder)) {
		mkdir($directionAndFolder);
	}
	$t1 = time();
	$extractedLinks = array();
	array_push($extractedLinks, $link);
	$i=0;
	if(is_null($link)){
		echo "link empty";
		return;
	}
	$status['extractedLinks'] = $extractedLinks;
	$status = scrapLinksWebsite($link, $host,$extractedLinks,$i,$t1,$status,$directionAndFolder);
	$filesSize = sizeof($status['files']);
	$filesLink = sizeof($status['extractedLinks']);
	for($j=$filesSize;$j<$filesLink; $j++){
		$linkLeft = $status['extractedLinks'][$j];
		$file = $directionAndFolder.''.nameFile($linkLeft);
		$commande = commandeSingleFile($file,$linkLeft);
		array_push($status['files'], $file);
		exec($commande);
	}
	$iWhere = 0;
	$filesSize = sizeof($status['files']);
	ifAllLinksDownloaded($status['files'],$iWhere,$filesSize);
	$regex = str_replace('.','\.',$host);
	foreach($status['files'] as $file){
		updateLinkToLocalLink(getHtml($file), $regex, $file);
	}
	return $status;
}
function ifAllLinksDownloaded($files,$i,$filesSize){
	for ($i;$i<$filesSize;$i++){
		if (!file_exists($files[$i])){
			sleep(20);
			return ifAllLinksDownloaded($files,$i,$filesSize);
		}
	}
	return true;
}
// $start = time();
// $link = $_POST["name"];
// $host = getHost($link);
// $folder = linkToFolder($host);
// $directionAndFolder = $direction.''.$folder;
// $status = startScrapLinksWebsite($link,$host,$direction,$directionAndFolder);
// $sizeFile = sizeof($status['files']);
// $sizeLink = sizeof($status['extractedLinks']);
// $finnish = time();
// $interval = ($finnish - $start)/60;
?>
<!-- <!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h2>SUCCESS!</h2>
	<p>You downloaded <?php echo $host; ?> for offline viewing in <?php echo $interval; ?> minutes</p>
	<p><?php echo $sizeFile; ?>/<?php echo $sizeLink; ?> files</p>
	<a href="<?php echo $directionAndFolder; ?>">go to <?php echo $host; ?> offline</a>
</body>
</html> -->