<?php
require 'scrap.php';
require 'single-file.php';
function scrapLinksWebsite($link, $host,$extractedLinks,$i,$t1,$status,$directionAndFolder)
{
	$stop=1;
	$html = '';
	// $file = $directionAndFolder.''.nameFile($link);
	// $commande = commandeSingleFile($file,$link);
	// array_push($status['files'], $file);
	// exec($commande);
	try{
		$html = file_get_contents($link);
		$status['extractedLinks'] = extracteLinks($link, $host, $html, $status['extractedLinks']);
	}catch(Exception $ex){
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

function updateLinkToLocalLink($html, $regex, $file){
	libxml_use_internal_errors(true);
	$pattern = '/https?:\/\/'.$regex.'\//';
	$newLink = preg_replace($pattern,'', $html);
	$doc = new DOMDocument;
	$doc->loadHTML($newLink);
	$links = $doc->getElementsByTagName('a');
	foreach ($links as $newLink1) {
		$hrefLink = parse_url($newLink1->getAttribute('href'));
		if(!isset($hrefLink['host'])){
			if(isset($hrefLink['path'])){
				$str = str_replace('/', "-", $hrefLink['path']);
				$newLink1->setAttribute('href',$str.'.html');
			}
		}
	}
	file_put_contents($file,'');
	file_put_contents($file,$doc->saveHTML());
	libxml_use_internal_errors(false);
}
function linkToFolder($host){
	return $host.'/';
}
function createCommande($link,$host,$nameFile){
	$nameFile = nameFile($link);
	return $host.'/'.$nameFile;
}
// $link = $_POST["name"];
$start = time();
$link = 'https://www.alacase.fr/';
$host = getHost($link);
$direction = '../../my-single-file-website/';
$directionAndFolder = $direction.''.$host.'/';
$status = startScrapLinksWebsite($link,$host,$direction,$directionAndFolder);
$sizeFile = sizeof($status['files']);
$sizeLink = sizeof($status['extractedLinks']);
$finnish = time();
$interval = ($finnish - $start)/60;
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h2>SUCCESS!</h2>
	<p>you downloaded <?php echo $host; ?> for offline viewing in <?php echo $interval; ?> minutes</p>
	<p><?php echo $sizeFile; ?>/<?php echo $sizeLink; ?> files</p>
	<a href="<?php echo $directionAndFolder; ?>">go to <?php echo $host; ?> offline</a>
</body>
</html>
<script>
	// while (true) {
	// 	var xhttp = new XMLHttpRequest();
	// 	xhttp.onreadystatechange = function() {
	// 		if (this.readyState == 4 && this.status == 200) {
	// 			document.getElementById("demo").innerHTML = this.responseText;
	// 		}
	// 	};
	// 	xhttp.open("GET", "ajax_info.txt", true);
	// 	xhttp.send();
	// }
</script>