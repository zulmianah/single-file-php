<?php
function commandeSingleFile($file,$link){
	return $command = 'single-file '.$link.' '.$file;
}
function singleFiles($url,$mySingleFileDirection){
	$fileName = nameFile($url);
	$command = 'single-file --urls-file='.$mySingleFileDirection.$fileName;
	execInBackground($command);
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
	$fileName = ltrim($fileName,'-');
	$fileName = rtrim($fileName, "-");
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
function updateLinkToLocalLink($html, $regex, $file){
	libxml_use_internal_errors(true);
	$pattern = '/https?:\/\/'.$regex.'\//';
	$newLink = preg_replace($pattern,'', $html);
	$doc = new DOMDocument;
	$doc->encoding = 'utf-8';
	$doc->loadHTML(utf8_decode($newLink));
	$links = $doc->getElementsByTagName('a');
	$symbol="=";
	foreach ($links as $newLink1) {
		$hrefLink = parse_url($newLink1->getAttribute('href'));
		if(!isset($hrefLink['host'])){
			if(isset($hrefLink['path'])){
				$str = "";
				if(strpos($hrefLink['path'], $symbol) !== false){
					$str = 'index';
				}else{
					$str = str_replace('/', "-", $hrefLink['path']);
					$str = rtrim($str,"-");
					if ($str === '') {
						$str = 'index';
					}
				}
				$newLink1->setAttribute('href',$str.'.html');
			}
		}
	}
	file_put_contents($file,'');
	$update = $doc->saveHTML();
	file_put_contents($file,$update);
	libxml_use_internal_errors(false);
}
?>