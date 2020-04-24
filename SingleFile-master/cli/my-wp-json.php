<?php
function wpJsonToLinks($link,$page)
{
	$apiLink = '/wp-json/wp/v2/posts?per_page=100&_fields=link&page=';
	$link = $link.$apiLink.$page;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, $link);
	$result = curl_exec($curl);
	curl_close($curl);
	$objs = json_decode($result);
	$links = array();
	if(is_array($objs)){
		foreach ($objs as $id => $obj) {
			array_push($links,$obj->link);
		}
	}else{
		// throw new Exception($objs->message." Code: ".$objs->code." ".$objs->data->status, 1);
		return $links;
	}
	return $links;
}
function wpJsonsToLinks($link)
{
	$links = array();
	$page = 1;
	while (1) {
		$linksPage = wpJsonToLinks($link,$page);
		if(empty($linksPage)){
			return $links;
		}
		$links = array_unique(array_merge($links,$linksPage));
		$page++;
	}
	return $links;
}
$link = 'https://www.blogueurssansfrontieres.org/';
// $page = 1;
// $links = wpJsonToLinks($link, $page);
// var_dump($links);
// $links = wpJsonsToLinks($link);
// var_dump($links);
?>