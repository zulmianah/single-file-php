<?php
function singleFile($url,$mySingleFileDirection){
	$fileName = nameFile($url);
	$command = 'single-file '.$url.' '.$mySingleFileDirection.$fileName;
	exec($command, $output, $return_var);
	if($return_var !== 0){
		riteError(new Exception($command." doesn't work properly"));
	}
	else{
		return $fileName;
	}
}
function commandeSingleFile($file,$link){
	return $command = 'single-file '.$link.' '.$file;
}
function listSingleFile($file,$link){
	return $command = $link.' '.$file;
}
function singleFiles($url,$mySingleFileDirection){
	$fileName = nameFile($url);
	$command = 'single-file --urls-file='.$mySingleFileDirection.$fileName;
	execInBackground($command);
}
function singleFileLinks($urls){
	foreach ($urls as $url) {
		singleFile($url);
	}
}
function nameFile($url){
	$parse = parse_url($url);
	$path = $parse['path'];
	$fileName = '';
	if(strcmp($path,'/')==0){
		$fileName = 'index';
	}else{
		$fileName = str_replace('/', '-', $path);
	}
	if(strcmp($fileName[0], '-')==0){
		$fileName = ltrim($fileName,'-');
	}
	return $fileName.'.html';
}
function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
}
function createCommande($link,$host,$nameFile){
	$nameFile = nameFile($link);
	return $host.'/'.$nameFile;
}
function updateLinkToLocalLink($html, $regex, $file){
	libxml_use_internal_errors(true);
	$pattern = '/https?:\/\/'.$regex.'\//';
	$newLink = preg_replace($pattern,'', $html);
	$doc = new DOMDocument;
	$doc->loadHTML($newLink);
	$links = $doc->getElementsByTagName('a');
	foreach ($links as $newLink1) {
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
?>