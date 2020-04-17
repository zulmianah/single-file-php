<?php
	$xml=simplexml_load_string($myXMLData) or die("Error: Cannot create object");
	print_r($xml);
?>