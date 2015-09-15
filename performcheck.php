<?php
//we can leave this on as this will give us some infos when we might debug it! the cron-job wont see this xD
    error_reporting ( -1 );
    ini_set ( 'display_errors', true );

include("config.php");

//no key no honey
	if($_GET["key"] != $key)
		die("ERROR");

//the standart check method which is pretty unreliable for example: it cant connect to port 800
function checkstatus($hostaddress, $port = 80, $timeout = 2)
{
    if ($socket = @ fsockopen($hostaddress, $port, $errno, $errstr, $timeout)) {
			fclose($socket);
			return true;
		} else {
			return false;
		}
	}

//alternative check method that gets tested so we may can fetch correct server status
function checkstatus2($hostaddress, $port = 80, $timeout = 2, $incodecheck = false, $keyphrase = "<html>", $keyphrase2 = "</html>")
{
    include("config.php");

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($socket != false) {
        if (socket_connect($socket, $hostaddress, $port)) {
            socket_close($socket);

            if ($port = 80 && $incodecheck) {
                $html = file_get_contents("http://" . $hostaddress . "/index.php");
                $html2 = file_get_contents("http://" . $hostaddress . "/index.html");

                $pattern = "/" . preg_quote($keyphrase, "/") . "(.*)" . preg_quote($keyphrase2, "/") . "/";

                if ($html)
                    preg_match($pattern, $html, $result);
                if ($html2)
                    preg_match($pattern, $html2, $result2);

                if (isset($result)) {
                    if (count($result) != 0) {
                        return true;
                    }
                } else if (isset($result2)) {
                    if (count($result2) != 0) {
                        return true;
                    }
                } else
                    return false;
            }

            return true;
        } else
            return false;
    } else
        return false;
}


//fetch the user online count from the website
function get_slot_state()
{
    $address = "http://pokemon-revolution-online.net/";
    $html = file_get_contents($address);

    $pattern = "/Users Online\: (.*)\/(.*)/";
    preg_match($pattern, $html, $result);

    $pattern = "/\<br\>Server Status\:\<br\> .*Server (.*)\<br\>/";
    preg_match($pattern, $html, $result2);


    if (count($result) != 0)
        return '</br>Benutzer online: ' . $result[1] . '/' . $result[2];
    else if (count($result2) != 0)
        return '</br><span style="color:orange">Server might be down</span>!';
    else
        return '</br><span style="color:orange">Playercount unavailable because the website might be down</span>!';
}

//loop through the server array and save the stats to a cache file
	for($i = 0; $i <= count($servers)-1; $i++):
		if(file_exists($absolute_cache_path."server".$servers[$i]['id'].".txt")){
			$filename = $absolute_cache_path."server".$servers[$i]['id'].".txt";
			$myfile = fopen($filename, "r");
			$fileread = fread($myfile, filesize($filename));
			fclose($myfile);
			$status = $fileread;
		} else
            $status = "offline";

        //some switcherino so it just updates the stats if the status changed
        if (checkstatus2($servers[$i]['serveraddress'], $servers[$i]['port'], $servers[$i]['timeout'], $servers[$i]['codecheck'], $servers[$i]['keyphrase'], $servers[$i]['keyphrase2'])) {
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

//update the last check time
	$filename = $absolute_cache_path."lastcheck.txt";
	$myfile = fopen($filename, "w");
	$newtime = new DateTime('now');
	fwrite($myfile, $newtime->format('Y-m-d H:i:s'));
	fclose($myfile);

//update the user online count
$filename = $absolute_cache_path . "userstatus.txt";
$myfile = fopen($filename, "w");
fwrite($myfile, get_slot_state());
fclose($myfile);
?>