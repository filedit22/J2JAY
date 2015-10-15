<?php
    // error_reporting ( -1 );
    // ini_set ( 'display_errors', true );

    include("config.php");
    include("time_difference.php");

    if ($onlinecounter) {
        if (file_exists($absolute_cache_path . "userstatus.txt")) {
            $filename = $absolute_cache_path . "userstatus.txt";
            $myfile = fopen($filename, "r");
            $fileread = fread($myfile, filesize($filename));
            fclose($myfile);
            $userstatus = $fileread;
        } else
            die("Missing cache pls run performcheck.php with your key!");
    }

    if(file_exists($absolute_cache_path."lastcheck.txt")){
        $filename = $absolute_cache_path."lastcheck.txt";
        $myfile = fopen($filename, "r");
        $fileread = fread($myfile, filesize($filename));
        fclose($myfile);
        $lastcheck = date_create_from_format('Y-m-d H:i:s', $fileread);
    } else
        die("Missing cache pls run performcheck.php with your key!");

    $now2 = new DateTime('now');

    echo '<html><head>
        <title>Pokémon Online Revolution Status Checker by BigBrainAFK</title>
        <meta charset="UTF-8">
        <meta content="Server Statuschecker for Pokémon Revolution Online" name="description">
        <link media="all" href="style.css" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        </head>

        <body link="#FFFFFF" alink="#FFFFFF" vlink="#FFFFFF" text="#FFFFFF">

        <img id="hintergrund" src="images/statuschecker/bg'; echo mt_rand(1, 7); echo '.jpg"></img>

        <div id="checkerlogo">
        </div>
        </br>
        </br>
        <div class="greybg">';

    for($i = 0; $i <= count($servers)-1; $i++):
        if(file_exists($absolute_cache_path."server".$servers[$i]['id'].".txt")){
            $filename = $absolute_cache_path."server".$servers[$i]['id'].".txt";
            $myfile = fopen($filename, "r");
            $fileread = fread($myfile, filesize($filename));
            fclose($myfile);
            $status = $fileread;
        } else
            die("Missing cache pls run performcheck.php!");

        if(file_exists($absolute_cache_path."servertime".$servers[$i]['id'].".txt")){
            $filename = $absolute_cache_path."servertime".$servers[$i]['id'].".txt";
            $myfile = fopen($filename, "r");
            $fileread = fread($myfile, filesize($filename));
            fclose($myfile);
            $lastonline = date_create_from_format('Y-m-d H:i:s', $fileread);
        } else
            die("Missing cache pls run performcheck.php with your key!");

        if($i == 0)
            echo '</br><span style="display:none">1</span>';

        echo '
                The '.$servers[$i]["displayname"].' is ';

        if($status == "online")
            echo '<span style="color:green">online</span>!';
        elseif($status == "online (full)")
            echo '<span style="color:orange">online (full)</span>!';
        elseif($status == "online (timeout)")
            echo '<span style="color:darkorange">online (timeout)</span>!';
        else
            echo '<span style="color:red">offline</span>!';

        if ($i == 0 && $onlinecounter)
            echo $userstatus;

        echo '</br></br> It\'s been ' . $status . ' for: ';
        get_difference($lastonline);
        echo '</br> Last checked: ';
        get_difference($lastcheck);
        echo '</br> These numbers are up to date as of ';
        echo $now2->format('Y-m-d H:i:s');
        echo ' in GMT+2 ';
        if (date('I')) echo 'DST';
        echo '</br>
                </br>';
    endfor;

    echo '
            <span style="display:none">10</span>
        </div>
        </br>
        </br>
        </br>
        <div id="footer">
            <div class="greybg">
                </br>
                This site is written by BigBrainAFK aka. 『 』 aka. DrVilla aka. MrArrigato aka. DocHosevoll aka. Fristermister.
                </br>PRO-Logo and idea for the site is the shadowfied status checker.
                </br>Copyright for the Pictures used go to their respective owners as I not created them!
                </br> Check me out on <a href="https://github.com/BigBrainAFK/Statuschecker">GitHub</a>!
                </br></br>
            </div>
        </div>
        </body></html>';
?>