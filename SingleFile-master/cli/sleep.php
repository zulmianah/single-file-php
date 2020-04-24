<?php
require 'single-file.php';
function testBrokenLink(){
	date_default_timezone_set("Africa/Nairobi");
	date_default_timezone_get();
	$start = time();
	$response = '';
	$response = exec("blc https://www.blogueurssansfrontieres.org/ --filter-level 0 -roe",$output,$value);
	var_dump("output");
	var_export( $output);
	var_dump("value");
	echo $value;
	var_dump("response");
	echo $response;
	$finnish = time();
	$interval = ($finnish - $start)/60;
	var_dump($interval);
}
function testSleep(){
	date_default_timezone_set("Africa/Nairobi");
	date_default_timezone_get();
	$start = time();
	$cmd = 'single-file https://www.alacase.fr/les-avantages-du-service-traiteur-pour-une-reception/ ../../my-single-file-website/www.alacase.fr/les-avantages-du-service-traiteur-pour-une-reception.html';
	execInBackground($cmd);
	sleep(30);
	$finnish = time();
	$interval = ($finnish - $start)/60;
	var_dump($interval);
}
testBrokenLink();
?>