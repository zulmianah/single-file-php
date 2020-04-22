<?php
require 'file.php';
require 'scrap-recursive.php';
// error_reporting(0);
// ini_set('display_errors', 0);
function startSingleFileWordpress($link)
{
	file_put_contents('log.txt','');
	file_put_contents('bug.txt','');
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
	checkFolderOrCreate($direction);
	$directionAndFolder=checkFolderOrCreate($direction.''.$folder);
	$link=$parse['scheme'].'://'.$parse['host'];
	stream_context_set_default( [
		'ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false,
		],
	]);
	$status['extractedLinks'] = getLinksFromWordpress($link,$parse);
	// array_slice($status['extractedLinks'], 213,7);
	return $status;
	$j=0;
	$linksSize = sizeof($status['extractedLinks']);
	for($j;$j<$linksSize; $j++){
		if($j%25==0 && $j!=0){
			$sleep=25;
			writeLog($sleep.'==>$j'.$j);
			// sleep($sleep);
		}
		if(!isset($status['extractedLinks'][$j])){
			array_splice($status['extractedLinks'],$j,1);
			if($j==($linksSize-1)){
				break;
			}
		}
		$linkLeft = $status['extractedLinks'][$j];
		$nameFile = nameFile($linkLeft);
		$file = $directionAndFolder.''.$nameFile;
		$commande = commandeSingleFile($file,$linkLeft);
		$status['files'][$j] = $file;
		writeLog($commande.'==>'.$linkLeft);
		// execInBackground($commande);
	}
	$linksSize = sizeof($status['extractedLinks']);
	$filesSize = sizeof($status['files']);
	$iWhere = 0;
	$sleep = 0;
	// ifAllLinksDownloaded($sleep,$status['files'],$status['extractedLinks'],$iWhere,$filesSize);
	$regex = str_replace('.','\.',$parse['host']);
	foreach($status['files'] as $file){
		// updateLinkToLocalLink(getHtml($file), $regex, $file);
	}
	$directionAndFolder=checkFolderOrCreate('../../my-single-file-website/');
	$directionAndZipFolder=checkFolderOrCreate('../../my-single-file-zip-website/');
	// zipFile($parse['host'],$directionAndFolder,$directionAndZipFolder);
	$status['status'] = 'SUCCESS';
	return $status;
}
function getLinksFromWordpress($link,$parse)
{
	$linksFromWordpressPagination = getLinksFromWordpressPagination($link);
	return $linksFromWordpressPagination;
	$linksFromSitemap = getLinksFromSitemap($link,$parse);
	$links = array_unique(array_merge($linksFromWordpressPagination,$linksFromSitemap));
	return $links;
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
					array_push($links, strval($url->loc[0]));
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
			if(!is_404($linkPage)) {
				array_push($links, $linkPage);
				$iPage++;
				$iError=0;
			}else{
				writeError(new Exception($linkPage." not found"));
				$iPage++;
				$iError++;
			}
		}
	} catch (Exception $e) {
		writeError($e);
	}
	return $links;
}
try {
	date_default_timezone_set("Africa/Nairobi");
	date_default_timezone_get();
	$start = time();
	if(isset($_POST["name"])){
		$link = $_POST["name"];
	}else{
		writeError(new Exception("Form name empty"));
	}
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