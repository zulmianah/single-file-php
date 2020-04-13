<?php
function singleFile($url){
	$parse = parse_url($url);
	$path = $parse['path'];
	$nameFile = '';
	if(strcmp($path,'/')==0){
		$nameFile = 'index';
	}else{
		$nameFile = str_replace('/', '-', $path);
	}
	if(strcmp($nameFile[0], '-')==0){
		$nameFile = ltrim($nameFile,'-');
	}
	$file = $nameFile.'.html';
	$command = 'single-file '.$url.' ../../'.$file;
	exec($command, $output, $return_var);
	if($return_var !== 0){
		echo 'error';
	}
	else{
		return $file;
	}
}
function singleFileLinks($urls){
	foreach ($urls as $url) {
		singleFile($url);
	}
}
?>