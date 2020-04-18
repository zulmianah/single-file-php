<?php
function getHtml($filename) {
	libxml_use_internal_errors(true);
	$file = file_get_contents($filename);
	$html = html_entity_decode($file);
	libxml_use_internal_errors(false);
	return $html;
}
function extracteLinks($url, $host, $html, $extractedLinks)
{
	$doc = new DOMDocument;
	@$doc->loadHTML($html);
	$links = $doc->getElementsByTagName('a');
	foreach($links as $link){
		$linkHref = $link->getAttribute('href');
		$linkHost = getHost($linkHref);
		if($host == $linkHost){
			if (!in_array($linkHref, $extractedLinks)) {
				if(!ifFragment($linkHref)) array_push($extractedLinks, ($linkHref));
			}
		}
	}
	return $extractedLinks;
}
function getHost($link){
	if (isset(parse_url($link)['host'])){
		return $host = parse_url($link)['host'];
	} else return null;
}
function ifFragment($link){
	return isset(parse_url($link)['fragment']);
}
function getDocLinks($html){
	$doc = new DOMDocument;
	@$doc->loadHTML($html);
	$links = $doc->getElementsByTagName('a');
	$docAndLinks['doc'] = $doc;
	$docAndLinks['links'] = $links;
}
function statusExist($link)
{
	return getStatusLink($link)!=404;
}
function getStatusLink($link)
{
	return substr(get_headers($link, 1)[0], 9, 3);
}
?>