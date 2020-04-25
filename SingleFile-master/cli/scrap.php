<?php
stream_context_set_default([
	'ssl' => [
		'verify_peer' => false,
		'verify_peer_name' => false,
	],
]);
function getHtml($filename) {
	libxml_use_internal_errors(true);
	$file = file_get_contents($filename);
	$html = html_entity_decode($file);
	libxml_use_internal_errors(false);
	return $html;
}
function getHost($link){
	if (isset(parse_url($link)['host'])){
		return $host = parse_url($link)['host'];
	} else return null;
}
function is404($url) {
	$is404 = true;
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
	return $is404;
}
function inputIsALink($parse){
	return sizeof($parse)>=2;
}
function isValideLink($link,$status){
	if(!inputIsALink($parse)){
		writeError(new Exception('Error link input is not parseable: '.$link));
		return false;
	}
	if(is404($link)){
		writeError(new Exception('Error 404 link not found: '.$link));
		return false;
	}
	return true;
}
?>