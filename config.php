<?php
	//You can enter it manually of course
	//example: $absolute_cache_path = "/httpdocs/statuschecker/";
	//pls include a / at the end of the path if you type it manually
	$absolute_cache_path = $_SERVER['DOCUMENT_ROOT'];
	
	//just keep the arrays like that!
	//do not include http:// and no / at the end!
	$servers = array(
		array("id" => 1, "serveradress" => "46.28.204.197", "port" => 80, "timeout" => 30, "displayname" => "PRO Server"),
		array("id" => 2, "serveradress" => "pokemon-revolution.net", "port" => 80, "timeout" => 30, "displayname" => "PRO Website")
	);
	
	//set a key so only your cronjob can run this file!
	$key = "insertyourkeyhere";
?>
