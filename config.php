<?php
	//You can enter it manually of course
	//example: $absolute_cache_path = "/httpdocs/statuschecker/";
	//pls include a / at the end of the path if you type it manually
    //$absolute_cache_path = $_SERVER['DOCUMENT_ROOT']."/yoursubfolder";
	$absolute_cache_path = $_SERVER['DOCUMENT_ROOT']."/statuschecker/";
	
	//just keep the arrays like that!
	//do not include http:// and no / at the end!
	$servers = array(
		array("id" => 1, "serveraddress" => "46.28.204.197", "port" => 800, "timeout" => 30, "displayname" => "PRO Server", "codecheck" => false, "keyphrase" => "<html>", "keyphrase2" => "</html>"),
		array("id" => 2, "serveraddress" => "pokemon-revolution-online.net", "port" => 80, "timeout" => 30, "displayname" => "PRO Website", "codecheck" => true, "keyphrase" => "2015 ", "keyphrase2" => "/Creatures")
	);
	
	//set a key so only your cronjob can run this file!
	$key = "insertyourkeyhere";

    //enable/disable the "User Online" counter
    $onlinecounter = true;

    //set the checkmode to: 0 = server is online on successfully connection, 1 = server is online on connection refuse, 2 = server is online on timeout, 3 = server is online on both
    $checkmode = 1;

    //lets add some timeout things so even if the server drops new incoming connections we can make a more or less accurate status
    //standart php time format and just 1 format type so no hours AND minutes just minutes OR hours
    $timeout_format = "i";
    //and the amoutn of the selected format until it is considered offline
    $timeout_amount = 30;
	
	//PRO specifiy warning that the server might be full
	$fullwarning = true;
?>