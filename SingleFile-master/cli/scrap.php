<?php
function extracteLinks($url, $extractedLinks)
{
	$i=0;
	$host = parse_url($url)['host'];
	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);  
	$html=file_get_contents($url);
	$htmlDom = new DOMDocument;
	@$htmlDom->loadHTML($html);
	$links = $htmlDom->getElementsByTagName('a');
	foreach($links as $link){
		$linkHref = $link->getAttribute('href');
		if(strlen(trim($linkHref)) == 0){
			continue;
		}
		if($linkHref[0] == '#'){
			continue;
		}
		$headers = @get_headers($url);
		// if the url doesn't exist 
		if(!($headers && strpos( $headers[0], '200'))) { 
			continue;
		} 
		$linkHost = parse_url($linkHref)['host'];
		// if the url is from the host 
		if(strcmp($host, $linkHost) != 0){
			continue;
		}
		// if the url is already in the list
		if (in_array($linkHref, $extractedLinks)) { 
			continue;
		}
		array_push($extractedLinks, $linkHref);
	}
	return ($extractedLinks);
}
?>