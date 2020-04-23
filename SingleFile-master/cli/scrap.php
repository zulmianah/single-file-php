<?php
function getHtml($filename) {
	libxml_use_internal_errors(true);
	$file = file_get_contents($filename);
	$html = html_entity_decode($file);
	libxml_use_internal_errors(false);
	return $html;
}
function extracteLinks($url, $host, $html, $extractedLinks){
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
function getStatusLink($link)
{
	$isValidHttpCode = false;
	$headers = get_headers($link, 1);
	if ($headers[0] == 'HTTP/1.1 200 OK') {
		$isValidHttpCode = true;
	}
	return $isValidHttpCode;
}
function is_404($url) {
	$is_404 = true;
	$handle = curl_init($url);
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
	$response = curl_exec($handle);
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	curl_close($handle);
	if ($httpCode >= 200 && $httpCode < 300) {
		$is_404 = false;
	} else {
		$is_404 = true;
	}
	return $is_404;
}
function inputIsALink($parse){
	return sizeof($parse)>=2;
}
?>