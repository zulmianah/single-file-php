<?php
require 'single-file.php';
date_default_timezone_set("Africa/Nairobi");
date_default_timezone_get();
$start = time();
$cmd = 'single-file https://www.alacase.fr/les-avantages-du-service-traiteur-pour-une-reception/ ../../my-single-file-website/www.alacase.fr/les-avantages-du-service-traiteur-pour-une-reception.html';
execInBackground($cmd);
	sleep(30);
$finnish = time();
$interval = ($finnish - $start)/60;
var_dump($interval);
?>