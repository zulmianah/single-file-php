<?php
require 'my-exception.php';
require 'file.php';
require 'scrap-recursive.php';
function startSingleFileWordpress($link)
{
	$parse=parse_url($link);
	$status = array();
	$status['files']=array();
	$status['extractedLinks'] = array();
	$status['status'] = 'FAIL';
	if(!inputIsALink($parse)){
		writeError(new Exception("Error link input is not parseable: ".$link));
		return $status;
	}
	if(is_404($link)){
		writeError(new Exception("Error 404 link not found: ".$link));
		return $status;
	}
	$folder = linkToFolder($parse['host']);
	$direction = '../../my-single-file-website/';
	$directionAndFolder = $direction.''.$folder;
	checkFolderOrCreate($direction);
	$directionAndFolder=checkFolderOrCreate($direction.''.$folder);
	$link=$parse['scheme'].'://'.$parse['host'].'/';
	stream_context_set_default( [
		'ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false,
		],
	]);
	// $status['extractedLinks'] = getLinksFromWordpress($link,$parse);
	$status['extractedLinks'] = [$link];
	// return $status;
	$j=0;
	$linksSize = sizeof($status['extractedLinks']);
	$iBackGround=22;
	$sleep=60;
	for($j;$j<$linksSize; $j++){
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
		if($j%$iBackGround==0 && $j!=0){
			sleep($sleep);
		}
		// execInBackground($commande);
	}
	// sleep($sleep);
	// return $status;
	$linksSize = sizeof($status['extractedLinks']);
	$filesSize = sizeof($status['files']);
	$iWhere = 0;
	// ifAllLinksDownloaded($sleep,$status['files'],$status['extractedLinks'],$iWhere,$filesSize);
	$regex = str_replace('.','\.',$parse['host']);
	foreach($status['files'] as $file){
		if (file_exists($file)){
			updateLinkToLocalLink(getHtml($file), $regex, $file);
		}else{
			writeError(new Exception("link not downloaded: ".$file));
		}
	}
	$directionAndFolder=checkFolderOrCreate('../../my-single-file-website/');
	$directionAndZipFolder=checkFolderOrCreate('../../my-single-file-zip-website/');
	// zipFile($parse['host'],$directionAndFolder,$directionAndZipFolder);
	$status['status'] = 'SUCCESS';
	return $status;
}
function getLinksFromWordpress($link,$parse)
{
	$links=array();
	$linksFromWordpressPagination = getLinksFromWordpressPagination($link);
	// return $linksFromWordpressPagination;
	$linksFromSitemap = getLinksFromSitemap($link,$parse);
	$links = array_unique(array_merge($linksFromWordpressPagination,$linksFromSitemap));
	return $links;
}
function getLinksFromSitemap($link,$parse)
{
	$links=array();
	$sitemapIndexXmlLink=$parse['scheme'].'://'.$parse['host'].'/sitemap_index.xml';
	$sitemapIndexXml=null;
	if (!is_404($sitemapIndexXmlLink)) {
		$sitemapIndexXml = simplexml_load_file($sitemapIndexXmlLink);
		$sitemapIndex = $sitemapIndexXml->sitemap;
		foreach ($sitemapIndex as $siteMap) {
			$sitemapXml = simplexml_load_file($siteMap->loc);
			if (!is_404(($siteMap->loc))) {
				$sitemapXml = simplexml_load_file($siteMap->loc);
				foreach ($sitemapXml->url as $url) {
					array_push($links, strval($url->loc[0]));
				}
			} else {
				writeError(new Exception($sitemapXml." not found"));
				return $links;
			}
		}
	} else {
		writeError(new Exception($sitemapIndexXmlLink." not found"));
		return $links;
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