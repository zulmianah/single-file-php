<?php
function extractLinks($link,$parse,$host)
{
	$str = file_get_contents('https://api.hackertarget.com/pagelinks/?q='.$link);
	$arr = explode("\n", $str);
	$uniques = array_unique($arr);
	array_pop($uniques);
	$links = array();
	foreach($uniques as $unique){
		$uniqueHost = parse_url($unique);
		if($host == $uniqueHost['host']){
			array_push($links, $unique);
		}
	}
	return $links;
}
function extractWebsiteLinks($link,$parse,$host,$extractLinks,$i)
{
	$extractLinks1 = extractLinks($link,$parse,$host);
	$extractLinks = array_merge($extractLinks, $extractLinks1);
	$extractLinks = array_unique($extractLinks);
	$i++;
	$extractLinksSize = sizeof($extractLinks);
	for ($i; $i < $extractLinks; $i++) { 
		$extractLinks = extractLinks($link,$parse,$host,$extractLinks,$i);
	}
	return $extractLinks;
}
$t1 = time();
$link = 'https://www.alacase.fr/';
$parse = parse_url($link);
$host = $parse['host'];
$extractLinks = array();
$i = 0;
$extractLinks = extractWebsiteLinks($link,$parse,$host,$extractLinks,$i);
var_dump($extractLinks);
$t2 = time();
$t = ($t2-$t1)/60;
var_dump($t);
?>