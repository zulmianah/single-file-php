<?php 
require 'file.php';
require 'scrap-recursive.php';
$link = 'https://mbasic.facebook.com/';
function essai($link)
{
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
	$directionAndFolder=checkFolderOrCreate($direction.''.$folder);
	$link=$parse['scheme'].'://'.$parse['host'];
	// $status['extractedLinks'] = getLinksFromWordpress($link,$parse);
	// execution en background 8 link pour
	$status['extractedLinks'] = [$link,$link,$link,$link,$link,$link,$link,$link];
	$filesSize = sizeof($status['files']);
	$filesLink = sizeof($status['extractedLinks']);
	stream_context_set_default( [
		'ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false,
		],
	]);
	for($j=$filesSize;$j<$filesLink; $j++){
		$linkLeft = $status['extractedLinks'][$j];
		// if(statusExist($linkLeft)){
			// $file = $directionAndFolder.''.nameFile($linkLeft);
			$file = $directionAndFolder.'bobo.html';
			$commande = commandeSingleFile($file,$linkLeft);
			var_dump($commande);
			array_push($status['files'], $file);
			execInBackground($commande);
		// }else{
		// 	writeError(new Exception($linkLeft." return an error 404"));
		// }
	}
}
essai($link);
?>