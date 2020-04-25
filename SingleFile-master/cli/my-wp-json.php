<?php
function allWpJsonLinks($link){
	$links = array();
	$linksPages = wpJsonPages($link);
	$linksCategories = wpJsonCategories($link);
	$linksArticles = wpJsonArticles($link);
	$links = array_unique(array_merge($linksPages,$linksCategories,$linksArticles));
	return $links;
}
function exportJsonEncode($array,$file)
{
	$json = json_encode($array);
	file_put_contents($file,'');
	file_put_contents($file,$json);
}
function wpJsonPages($link)
{
	$API_LINK = '/wp-json/wp/v2/pages?per_page=100&_fields=link';
	$linkAndApi = $link.$API_LINK;
	return wpJsonLinks($linkAndApi);
}
//categories
function wpJsonCategories($link)
{
	$API_LINK = '/wp-json/wp/v2/categories?per_page=100&_fields=link';
	$linkAndApi = $link.$API_LINK;
	return wpJsonLinks($linkAndApi);
}
function wpJsonArticlesPerPage($link,$page)
{
	$API_LINK = '/wp-json/wp/v2/posts?per_page=100&_fields=link&page=';
	$linkAndApi = $link.$API_LINK.$page;
	return wpJsonLinks($linkAndApi);
}
function wpJsonArticles($link)
{
	$links = array();
	$page = 1;
	while (1) {
		$linksPage = wpJsonArticlesPerPage($link,$page);
		if(empty($linksPage)){
			return $links;
		}
		$links = array_unique(array_merge($links,$linksPage));
		$page++;
	}
	return $links;
}
function wpJsonLinks($linkAndApi)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, $linkAndApi);
	$result = curl_exec($curl);
	curl_close($curl);
	$objs = json_decode($result);
	$links = array();
	if(is_array($objs)){
		foreach ($objs as $id => $obj) {
			array_push($links,$obj->link);
		}
	}else{
		writeError(new Exception($objs->message.' Code: '.$objs->code.' '.$objs->data->status));
		return $links;
	}
	return $links;
}
?>