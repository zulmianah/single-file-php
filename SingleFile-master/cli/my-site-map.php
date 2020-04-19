<?php
require 'file.php';
require 'scrap-recursive.php';
// error_reporting(0);
// ini_set('display_errors', 0);
function startSingleFileWordpress($link)
{
	$parse=parse_url($link);
	$status = array();
	$status['files']=array();
	$status['extractedLinks'] = array();
	$status['status'] = 'FAIL';
	if(sizeof($parse)<2){
		writeError(new Exception("Error link input: ".$link));
		return $status;
	}
	$folder = linkToFolder($parse['host']);
	$direction = '../../my-single-file-website/';
	$directionAndFolder = $direction.''.$folder;
	$directionAndFolder=checkFolderOrCreate($direction.''.$folder);
	$link=$parse['scheme'].'://'.$parse['host'];
	// $status['extractedLinks'] = getLinksFromWordpress($link,$parse);
	$status['extractedLinks'] = [$link,$link,$link,$link,$link,$link,$link,$link];
	$filesSize = sizeof($status['files']);
	$filesLink = sizeof($status['extractedLinks']);
	stream_context_set_default( [
		'ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false,
		],
	]);
	for($j=$filesSize;$j<$filesLink; $j++){
		$linkLeft = $status['extractedLinks'][$j];
		if(statusExist($linkLeft)){
			$file = $directionAndFolder.''.nameFile($linkLeft);
			$commande = commandeSingleFile($file,$linkLeft);
			array_push($status['files'], $file);
			execInBackground($commande);
		}else{
			writeError(new Exception($linkLeft." return an error 404"));
		}
	}
	$iWhere = 0;
	$filesSize = sizeof($status['files']);
	ifAllLinksDownloaded($status['files'],$status['extractedLinks'],$iWhere,$filesSize);
	$regex = str_replace('.','\.',$parse['host']);
	foreach($status['files'] as $file){
		updateLinkToLocalLink(getHtml($file), $regex, $file);
	}
	$directionAndFolder=checkFolderOrCreate('../../my-single-file-website/');
	$directionAndZipFolder=checkFolderOrCreate('../../my-single-file-zip-website/');
	zipFile($parse['host'],$directionAndFolder,$directionAndZipFolder);
	$status['status'] = 'SUCCESS';
	return $status;
}
function getLinksFromWordpress($link,$parse)
{
	$linksFromSitemap = getLinksFromSitemap($link,$parse);
	$linksFromWordpressPagination = getLinksFromWordpressPagination($link);
	return $links = array_unique(array_merge($linksFromSitemap,$linksFromWordpressPagination));
}
function getLinksFromSitemap($link,$parse)
{
	$links=array();
	$sitemapIndexXmlLink=$parse['scheme'].'://'.$parse['host'].'/sitemap_index.xml';
	$sitemapIndexXml=simplexml_load_file($sitemapIndexXmlLink);
	if($sitemapIndexXml===FALSE) {
		writeError(new Exception($sitemapIndexXmlLink." not found"));
	} else {
		$sitemapIndex = $sitemapIndexXml->sitemap;
		foreach ($sitemapIndex as $siteMap) {
			$sitemapXml = simplexml_load_file($siteMap->loc);
			if($sitemapXml===FALSE) {
				writeError(new Exception($sitemapXml." not found"));
			} else {
				foreach ($sitemapXml->url as $url) {
					array_push($links, $url->loc);
				}
			}
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
			$linkPage=$linkPagination.$iPage.'/';
			if(statusExist($linkPage)) {
				array_push($links, $linkPage);
				$iPage++;
				$iError=0;
			}else{
				array_push($links, $linkPage);
				writeError(new Exception($linkPage." not found"));
				$iPage++;
				$iError++;
			}
		}
		$i=0;
		while ($i<2) {
			array_pop($links);
			$i++;
		}
	} catch (Exception $e) {
		writeError($e);
	}
	return $links;
}
try {
	$start = time();
	// $link = $_POST["name"];
	$link = 'https://www.alacase.fr/';
	$status = startSingleFileWordpress($link);
	$sizeFile = sizeof($status['files']);
	$sizeLink = sizeof($status['extractedLinks']);
	$finnish = time();
	$interval = ($finnish - $start)/60;
	$host = getHost($link);
	$folder = linkToFolder($host);
	$direction = '../../my-single-file-website/';
	$directionAndZipFolder=checkFolderOrCreate('../../my-single-file-zip-website/');
	$zipFile=$directionAndZipFolder.$host.'.zip';
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
	<h2><?php echo $status['status'] ?>!</h2>
	<p>You downloaded <?php echo $host; ?> for offline viewing in <?php echo $interval; ?> minutes</p>
	<p><?php echo $sizeFile; ?>/<?php echo $sizeLink; ?> files</p>
	<li>
		<a href="<?php echo $zipFile; ?>"><?php echo $host; ?>.zip</a>
	</li>
	<li>
		<a href="<?php echo $directionAndZipFolder; ?>">my offline websites</a>
	</li>
	<li>
		<a href="bug.txt">view bugs</a>
	</li>
</body>
</html>