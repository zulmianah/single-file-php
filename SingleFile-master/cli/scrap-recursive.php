<?php
require 'scrap.php';
require 'single-file.php';
function scrapLinksWebsite($url,$extractedLinks)
{
	if(is_null($url)){
		echo "url empty";
		return;
	}
	if (is_null($extractedLinks)) 
	{
		$extractedLinks = array();
		array_push($extractedLinks, $url);
	}
	// get links from pages
	$extractedLinks = extracteLinks($url,$extractedLinks);
	foreach ($extractedLinks as $extractedLink) {
		$extractedLinks = scrapLinksWebsite($extractedLink,$extractedLinks);
	}
	return $extractedLinks;
}
function getbody($filename) {
	libxml_use_internal_errors(true);
	$file = file_get_contents($filename);
	libxml_use_internal_errors(false);
	return html_entity_decode($file);
}
function updateLinkToLocalLink($html,$url,$file){
	libxml_use_internal_errors(true);
	$parse = parse_url($url);
	$regex = str_replace('.','\.',$parse['host']);
	$pattern = '/https?:\/\/'.$regex.'\//';
	$newLink = preg_replace($pattern,'', $html);
	$doc = new DOMDocument;
	$doc->loadHTML($newLink);
	foreach ($doc->getElementsByTagName('a') as $newLink1) {
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
$extractedLinks = null;
$extractedLinks = scrapLinksWebsite('https://inmadagaskar.com/',$extractedLinks);
var_dump($extractedLinks);
		// download single file
	// $file = './'.singleFile($url);
		// edit links file to local file
	// $bodycontent = getbody($file);
	// updateLinkToLocalLink($bodycontent,$url,$file);
?>