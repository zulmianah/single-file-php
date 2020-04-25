<?php
require 'scrap.php';
require 'single-file.php';
function ifAllLinksDownloaded($timeNotDownloaded,$files,$extractedLinks,$i,$filesSize){
	for ($i;$i<$filesSize;$i++){
		if(!isset($extractedLinks[$i])){
			$i++;
		}
		if (!file_exists($files[$i])){
			sleep(15);
			$timeNotDownloaded++;
			if($timeNotDownloaded==2){
				$timeNotDownloaded=0;
				$i++;
			}
			return ifAllLinksDownloaded($timeNotDownloaded,$files,$extractedLinks,$i,$filesSize);
		}
	}
	return true;
}
?>