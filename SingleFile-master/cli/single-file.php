<?php
function singleFile($url,$mySingleFileDirection){
	$fileName = nameFile($url);
	$command = 'single-file '.$url.' '.$mySingleFileDirection.$fileName;
	exec($command, $output, $return_var);
	if($return_var !== 0){
		var_dump('error');
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
?>