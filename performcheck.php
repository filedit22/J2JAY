<?php
    //we can leave this on as this will give us some infos when we might debug it! the cron-job wont see this xD
    error_reporting ( -1 );
    ini_set ( 'display_errors', true );

    include("config.php");
    include("time_difference.php");

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
            } else {
                $null = null;
                $write = $socket;
                socket_select($null, $write, $null, 1);
                foreach ($write as $port => $socket) {
                    $desc = "$port/tcp";
                    $errno = socket_get_option($socket, SOL_SOCKET, SO_ERROR);

                    if ($errno == 0) {
                        return true;
                    } elseif ($errno == SOCKET_ECONNREFUSED) {
                        if($checkmode == 1 || $checkmode == 3)
                            return true;
                        else
                            return false;
                        //                            return timeout_check($hostaddress);
                    } elseif ($errno == SOCKET_ETIMEDOUT) {
                        if($checkmode == 2 || $checkmode == 3)
                            return true;
                        else
                            return false;
                        //                            return timeout_check($hostaddress);
                    } else {
                        $errmsg = socket_strerror($errno);
                        echo "$desc error $errmsg\n";
                    }
                }
                return false;
                //                return timeout_check($hostaddress);
            }
        } else
            return false;
        //            return timeout_check($hostaddress);
    }

    function timeout_check($hostaddress)
    {
        include("config.php");

        for ($i = 0; $i <= count($servers) - 1; $i++):
            if ($servers[$i]['serveraddress'] == $hostaddress) {
                $filename = $absolute_cache_path . "servertime" . $servers[$i]['id'] . ".txt";
                $myfile = fopen($filename, "r");
                $fileread = fread($myfile, filesize($filename));
                fclose($myfile);
                $lastseenonline = date_create_from_format('Y-m-d H:i:s', $fileread);
            }
        endfor;
        $since_on_diff = get_difference($lastseenonline, $timeout_format);
        if(!isset($since_on_diff)){
            echo "not set";
            return true;
        }
        var_dump($since_on_diff);
        var_dump($timeout_amount);
        var_dump(get_slot_state("f"));
        if (intval($since_on_diff) >= $timeout_amount && get_slot_state("f") == "same") {
            echo "off";
            return false;
        }else {
            echo "on";
            return true;
        }
    }


    //fetch the user online count from the website
    function get_slot_state($mode = "none")
    {
        include("config.php");

        $address = "http://pokemon-revolution-online.net/ServerStatus.php";
        $html = file_get_contents($address);
        echo $html;

        $pattern = '/\<font color="33FF66"\>Users Online\: (.*)\/(.*)\<\/font\>/';
        preg_match($pattern, $html, $result);

        $pattern = '/\<font color="(.*)">Offline\<\/font\>/';
        preg_match($pattern, $html, $result2);


        $filename = $absolute_cache_path . "userstatus.txt";
        $myfile = fopen($filename, "r");
        $fileread = fread($myfile, filesize($filename));
        fclose($myfile);
        $old_onlinecount = $fileread;

        if (count($result) != 0) {
            if ($old_onlinecount == $result[1] && $mode == "f")
                return "same";
            return '</br>Users Online: ' . $result[1] . '/' . $result[2];
        } else if (count($result2) != 0) {
            return '</br><span style="color:red">Server Offline!</span>';
            //return '</br><span style="color:orange">Server might be down</span>!';
        } else
            return '</br><span style="color:orange">Playercount unavailable because the website might be down</span>!';
    }

    //is the statustext online
    function isOnline($status){
        if($status == "online" || $status == "online (full)" || $status == "online (timeout)")
            return true;
        else
            return false;
    }

    //update file function
    function updateFile($new_state, $id, $new_time = false) {
        if($new_time) {
            $filename = $absolute_cache_path . "servertime" . $id . ".txt";
            $myfile = fopen($filename, "w");
            $newtime = new DateTime('now');
            fwrite($myfile, $newtime->format('Y-m-d H:i:s'));
            fclose($myfile);
        }

        $filename = $absolute_cache_path . "server" . $id . ".txt";
        $myfile = fopen($filename, "w");
        fwrite($myfile, "$new_state");
        fclose($myfile);
    }


    //update the user count
    if ($onlinecounter) {
        $filename = $absolute_cache_path . "userstatus.txt";
        $myfile = fopen($filename, "w");
        fwrite($myfile, get_slot_state("none"));
        fclose($myfile);
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

        if($i == 0){
            $filename = $absolute_cache_path . "userstatus.txt";
            $myfile = fopen($filename, "r");
            $fileread = fread($myfile, filesize($filename));
            fclose($myfile);
            $count_temp = $fileread;

            $pattern = "/Users Online\: (.*)\/(.*)/";
            preg_match($pattern, $count_temp, $result);

            if(count($result) != 0)
                $count = $result[1];
            else
                $count = -1;
        } else
            $count = -2;

        //some switcherino so it just updates the stats if the status changed
        if (checkstatus2($servers[$i]['serveraddress'], $servers[$i]['port'], $servers[$i]['timeout'], $servers[$i]['codecheck'], $servers[$i]['keyphrase'], $servers[$i]['keyphrase2'])) {
            if(isOnline($status) && $count != -1 && $count >= 0 && $count <= 1550){
                updateFile("online", $servers[$i]['id'], false);
            } elseif(isOnline($status) && $count >= 1550){
                updateFile("online (full)", $servers[$i]['id'], false);
            } elseif(isOnline($status) && $count == -1){
                updateFile("online", $servers[$i]['id'], false);
            }  elseif($status == "offline" && $count != -1 && $count >= 0){
                updateFile("online", $servers[$i]['id'], true);
            } elseif($status == "offline" && $count > 1550){
                updateFile("online (full)", $servers[$i]['id'], true);
            } elseif($status == "offline" && $count == -1){
                updateFile("offline", $servers[$i]['id'], false);
            }  elseif($status == "offline"){
                updateFile("online", $servers[$i]['id'], true);
            } elseif(isOnline($status)) {
                updateFile("online", $servers[$i]['id'], false);
            } else {
                updateFile("online", $servers[$i]['id'], true);
            }
        } else {
            if(isOnline($status) && $count != -1 && $count > 1550){
                updateFile("online (full)", $servers[$i]['id'], false);
            } elseif(isOnline($status) && $count != -1 && $count >= 0){
                updateFile("online (timeout)", $servers[$i]['id'], false);
            } elseif(isOnline($status) && $count == -1){
                updateFile("offline", $servers[$i]['id'], true);
            }  elseif($status == "offline" && $count != -1 && $count >= 0){
                updateFile("online (timeout)", $servers[$i]['id'], true);
            } elseif($status == "offline" && $count != -1 && $count > 1550){
                updateFile("online (full)", $servers[$i]['id'], true);
            } elseif($status == "offline" && $count == -1){
                updateFile("offline", $servers[$i]['id'], false);
            }  elseif(isOnline($status)){
                updateFile("offline", $servers[$i]['id'], true);
            } elseif($status == "offline") {
                updateFile("offline", $servers[$i]['id'], false);
            } else {
                updateFile("offline", $servers[$i]['id'], true);
            }
        }
    endfor;

    //update the last check time
    $filename = $absolute_cache_path."lastcheck.txt";
    $myfile = fopen($filename, "w");
    $newtime = new DateTime('now');
    fwrite($myfile, $newtime->format('Y-m-d H:i:s'));
    fclose($myfile);

    echo "Done!";
?>