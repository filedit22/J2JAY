<?php
    error_reporting ( -1 );
    ini_set ( 'display_errors', true ); 
	
	require_once("config.php");
	
	function checkstatus($hostadress, $port = 80, $timeout = 2){
		if($socket =@ fsockopen($hostadress, $port, $errno, $errstr, $timeout)) {
			fclose($socket);
			return true;
		} else {
			return false;
		}
	}
	
	for($i = 0; $i <= count($servers)-1; $i++):
		if(file_exists($absolute_cache_path."server".$servers[$i]['id'].".txt")){
			$filename = $absolute_cache_path."server".$servers[$i]['id'].".txt";
			$myfile = fopen($filename, "r");
			$fileread = fread($myfile, filesize($filename));
			fclose($myfile);
			$status = $fileread;
		} else
			$status = "offline"
		
		if(checkstatus($servers[$i]['serveradress'], $servers[$i]['port'], $servers[$i]['timeout'])){
			if($status == "offline"){
				$filename = $absolute_cache_path."servertime".$servers[$i]['id'].".txt";
				$myfile = fopen($filename, "w");
				$newtime = new DateTime('now');
				fwrite($myfile, $newtime->format('Y-m-d H:i:s'));
				fclose($myfile);
				
				$filename = $absolute_cache_path."server".$servers[$i]['id'].".txt";
				$myfile = fopen($filename, "w");
				fwrite($myfile, "online");
				fclose($myfile);
			}
		} else {
			if($status == "online"){
				$filename = $absolute_cache_path."servertime".$servers[$i]['id'].".txt";
				$myfile = fopen($filename, "w");
				$newtime = new DateTime('now');
				fwrite($myfile, $newtime->format('Y-m-d H:i:s'));
				fclose($myfile);
				
				$filename = $absolute_cache_path."server".$servers[$i]['id'].".txt";
				$myfile = fopen($filename, "w");
				fwrite($myfile, "offline");
				fclose($myfile);
			}
		}
	endfor;
	
	$filename = $absolute_cache_path."lastcheck.txt";
	$myfile = fopen($filename, "w");
	$newtime = new DateTime('now');
	fwrite($myfile, $newtime->format('Y-m-d H:i:s'));
	fclose($myfile);
?>