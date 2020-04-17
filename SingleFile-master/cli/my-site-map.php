<?php
require 'file.php';
require 'scrap-recursive.php';
function startSingleFileWordpress($link)
{
	$host = getHost($link);
	$folder = linkToFolder($host);
	$direction = '../../my-single-file-website/';
	$directionAndFolder = $direction.''.$folder;
	$parse=parse_url($link);
	$link=$parse['scheme'].'://'.$parse['host'];
	$status = array();
	$status['files']=array();
	if (!file_exists($directionAndFolder)) {
		mkdir($directionAndFolder);
	}
	$status['extractedLinks'] = getLinksFromWordpress($link,$parse);
	$filesSize = sizeof($status['files']);
	$filesLink = sizeof($status['extractedLinks']);
	for($j=$filesSize;$j<$filesLink; $j++){
		$linkLeft = $status['extractedLinks'][$j];
		$file = $directionAndFolder.''.nameFile($linkLeft);
		$commande = commandeSingleFile($file,$linkLeft);
		array_push($status['files'], $file);
		// exec($commande);
	}
	// $iWhere = 0;
	// $filesSize = sizeof($status['files']);
	// ifAllLinksDownloaded($status['files'],$iWhere,$filesSize);
	// $regex = str_replace('.','\.',$host);
	// foreach($status['files'] as $file){
	// 	updateLinkToLocalLink(getHtml($file), $regex, $file);
	// }
	return $status;
}
function getLinksFromWordpress($link,$parse)
{
	$linksFromSitemap = getLinksFromSitemap($link,$parse);
	$linksFromWordpressPagination = getLinksFromWordpressPagination($link);
	return $links = array_merge($linksFromSitemap,$linksFromWordpressPagination);
}
function getLinksFromSitemap($link,$parse)
{
	$sitemapIndexXmlLink=$parse['scheme'].'://'.$parse['host'].'/sitemap_index.xml';
	$sitemapIndexXml=simplexml_load_file($sitemapIndexXmlLink) or die("Error: Cannot create object");
	$sitemapIndex = $sitemapIndexXml->sitemap;
	$linkSize = 0;
	$links=array();
	foreach ($sitemapIndex as $siteMap) {
		$sitemapXml = simplexml_load_file($siteMap->loc) or die("Error: Cannot create object");
		foreach ($sitemapXml->url as $url) {
			array_push($links, $url->loc);
		}
	}
	return $links;
}
function getLinksFromWordpressPagination($link)
{
	$links=array();
	$linkPagination=$link.'/page/';
	$iPage=0;
	$iError=0;
	try {
		while($iError<2){
			$linkPage=$linkPagination.$iPage;
			if(statusExist($linkPage)) {
				array_push($links, $linkPage);
				$iPage++;
				$iError=0;
			}else{
				array_push($links, $linkPage);
				$iPage++;
				$iError++;
			}
		}
		$i=0;
		while ($i<2) {
			array_pop($links);
			$i++;
		}
		return $links;
	} catch (Exception $e) {
		throw $e;
	}
}
function statusExist($link)
{
	return getStatusLink($link)!=404;
}
function getStatusLink($link)
{
	return substr(get_headers($link, 1)[0], 9, 3);
}
try {
	$start = time();
	$link = $_POST["name"];
	$status = startSingleFileWordpress($link);
	$sizeFile = sizeof($status['files']);
	$sizeLink = sizeof($status['extractedLinks']);
	$finnish = time();
	$interval = ($finnish - $start)/60;
	$host = getHost($link);
} catch (Exception $e) {
	writeError($e);
}
?>
<!DOCTYPE html>
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
</html>