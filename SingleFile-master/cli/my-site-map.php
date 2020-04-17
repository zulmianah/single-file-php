<?php
require 'file.php';
require 'my-exception.php';
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
function getLinksFromWordpress($link)
{
	$parse=parse_url($link);
	$link=$parse['scheme'].'://'.$parse['host'];
	$linksFromSitemap = getLinksFromSitemap($link,$parse);
	$linksFromWordpressPagination = getLinksFromWordpressPagination($link);
	return $links = array_merge($linksFromSitemap,$linksFromWordpressPagination);
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
	$link='https://www.alacase.fr/sitemap_index.xml';
	$links = getLinksFromWordpress($link);
	var_dump($links);
} catch (Exception $e) {
	writeError($e);
}
?>