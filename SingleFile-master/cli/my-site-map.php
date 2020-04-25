<?php
require 'my-exception.php';
require 'file.php';
require 'scrap-recursive.php';
require 'my-wp-json.php';
// $status['extracted_links']	les liens a telecharger
// $status['files']				les noms de fichiers telecharges
// $status['status']			message du resultat
// download wordpress for offline viewing
function startSingleFileWordpress($link){
	$parse=parse_url($link);
	$status=array();
	$status['files']=array();
	$status['extracted_links']=array();
	$status['status']='FAIL';
	if(!isValideLink($link,$status)){
		return $status;
	}
	$folder=linkToFolder($parse['host']);
	$direction='../../my-single-file-website/';
	$directionAndFolder=$direction.''.$folder;
	checkFolderOrCreate($direction);
	$directionAndFolder=checkFolderOrCreate($direction.''.$folder);
	$link=$parse['scheme'].'://'.$parse['host'].'/';
	$status['extracted_links']=getLinksFromWordpress($link,$parse,$parse['host'],$directionAndFolder);
	$j=0;
	$linksSize=sizeof($status['extracted_links']);
	$iBackGround=22;
	$sleep=60;
	for($j;$j<$linksSize; $j++){
		if(!isset($status['extracted_links'][$j])){
			array_splice($status['extracted_links'],$j,1);
			if($j==($linksSize-1)){
				break;
			}
		}
		$linkLeft=$status['extracted_links'][$j];
		$nameFile=nameFile($linkLeft);
		$file=$directionAndFolder.''.$nameFile;
		$commande=commandeSingleFile($file,$linkLeft);
		$status['files'][$j]=$file;
		if($j%$iBackGround==0 && $j!=0){
			sleep($sleep);
		}
		execInBackground($commande);
	}
	$filesSize=sizeof($status['files']);
	$iLastDownloaded=0;
	$timeNotDownloaded=0;
	ifAllLinksDownloaded($timeNotDownloaded,$status['files'],$status['extracted_links'],$iLastDownloaded,$filesSize);
	$regex=str_replace('.','\.',$parse['host']);
	foreach ( $status['files'] as $file ) {
		if (file_exists($file)){
			updateLinkToLocalLink(getHtml($file), $regex, $file);
		} else {
			writeError(new Exception('Link not downloaded: '.$file));
		}
	}
	$status['status']='SUCCESS';
	return $status;
}
// get whole internal links from wordpress
function getLinksFromWordpress($link,$parse,$file,$folder){
	$links=array();
	$linksFromWordpressPagination=getLinksFromWordpressPagination($link);
	$linksFromSitemap=getLinksFromSitemap($link,$parse);
	$linkFromWpJson=allWpJsonLinks($link);
	$links=array_unique(array_merge($linksFromWordpressPagination,$linksFromSitemap,$linkFromWpJson));
	exportJsonEncode($links,$folder.$file.'.json');
	return $links;
}
// get links from sitemap and sub xml in sitemap
function getLinksFromSitemap($link,$parse){
	$links=array();
	$sitemapIndexXmlLink=$parse['scheme'].'://'.$parse['host'].'/sitemap_index.xml';
	$sitemapIndexXml=null;
	if (!is404($sitemapIndexXmlLink)) {
		$sitemapIndexXml=simplexml_load_file($sitemapIndexXmlLink);
		$sitemapIndex=$sitemapIndexXml->sitemap;
		foreach ($sitemapIndex as $siteMap) {
			$sitemapXml=simplexml_load_file($siteMap->loc);
			if (!is404(($siteMap->loc))) {
				$sitemapXml=simplexml_load_file($siteMap->loc);
				foreach ($sitemapXml->url as $url) {
					array_push($links, strval($url->loc[0]));
				}
			} else {
				writeError(new Exception($sitemapXml.' not found'));
				return $links;
			}
		}
	} else {
		writeError(new Exception($sitemapIndexXmlLink.' not found'));
		return $links;
	}
	return $links;
}
// get links pagination from wordpress
function getLinksFromWordpressPagination($link){
	$links=array();
	$linkPagination=$link.'/page/';
	$iPage=0;
	$iError=0;
	try {
		while($iError<2){
			$linkPage=$linkPagination.$iPage.'/';
			if(!is404($linkPage)) {
				array_push($links, $linkPage);
				$iPage++;
				$iError=0;
			}else{
				writeError(new Exception($linkPage.' not found'));
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
	date_default_timezone_set('Africa/Nairobi');
	date_default_timezone_get();
	$start=time();
	if(isset($_POST['name'])){
		$link=$_POST['name'];
	}else{
		writeError(new Exception('Form name empty'));
	}
	$status=startSingleFileWordpress($link);
	$sizeFile=sizeof($status['files']);
	$sizeLink=sizeof($status['extracted_links']);
	$finnish=time();
	$interval=($finnish - $start)/60;
	$host=getHost($link);
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
		<a href='<?php echo $zipFile; ?>'><?php echo $host; ?>.zip</a>
	</li>
	<li>
		<a href='bug.txt'>view bugs</a>
	</li>
</body>
</html>