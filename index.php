<?php
    // error_reporting ( -1 );
    // ini_set ( 'display_errors', true ); 

    include("config.php");
	
	class MyDateInterval extends DateInterval {
		public
			$pluralCheck = '()',
				// Must be exactly 2 characters long
				// The first character is the opening brace, the second the closing brace
				// Text between these braces will be used if > 1, or replaced with $this->singularReplacement if = 1
			$singularReplacement = '',
				// Replaces $this->pluralCheck if = 1
				// hour(s) -> hour
			$separator = ', ',
				// Delimiter between units
				// 3 hours, 2 minutes
			$finalSeparator = ', and ',
				// Delimeter between next-to-last unit and last unit
				// 3 hours, 2 minutes, and 1 second
			$finalSeparator2 = ' and ';
				// Delimeter between units if there are only 2 units
				// 3 hours and 2 minutes

        public static function createFromDateInterval (DateInterval $interval) {
            $obj = new self('PT0S');
            foreach ($interval as $property => $value) {
                $obj->$property = $value;
            }
            return $obj;
        }

		public function formatWithoutZeroes () {
			// Each argument may have only one % parameter
			// Result does not handle %R or %r -- but you can retrieve that information using $this->format('%R') and using your own logic
			$parts = array ();
			foreach (func_get_args() as $arg) {
				$pre = mb_substr($arg, 0, mb_strpos($arg, '%'));
				$param = mb_substr($arg, mb_strpos($arg, '%'), 2);
				$post = mb_substr($arg, mb_strpos($arg, $param)+mb_strlen($param));
				$num = intval(parent::format($param));

				$open = preg_quote($this->pluralCheck[0], '/');
				$close = preg_quote($this->pluralCheck[1], '/');
				$pattern = "/$open(.*)$close/";
				list ($pre, $post) = preg_replace($pattern, $num == 1 ? $this->singularReplacement : '$1', array ($pre, $post));

				if ($num != 0) {
					$parts[] = $pre.$num.$post;
				}
			}

			$output = '';
			$l = count($parts);
			foreach ($parts as $i => $part) {
				$output .= $part.($i < $l-2 ? $this->separator : ($l == 2 ? $this->finalSeparator2 : ($i == $l-2 ? $this->finalSeparator : '')));
			}
			return $output;
		}
	}

	function get_difference($time){
		$now = new DateTime('now');
		$diff = MyDateInterval::createFromDateInterval($now->diff($time, true));
		$time = array($diff->formatWithoutZeroes('%y'), $diff->formatWithoutZeroes('%m'), $diff->formatWithoutZeroes('%d'), $diff->formatWithoutZeroes('%h'), $diff->formatWithoutZeroes('%i'), $diff->formatWithoutZeroes('%s'));
		$returnstring = "";
		$name = array("years", "months", "days", "hours", "minutes", "seconds");
		$names = array("year", "month", "day", "hour", "minute", "second");
		
		if($time[5] == 0 && $time[4] == 0 && $time[3] == 0 && $time[2] == 0 && $time[1] == 0 && $time[0] == 0)
			echo "just now";
		else{
			if($time[5] != 0)
				$needtogo = 5;
			if($time[4] != 0)
				$needtogo = 4;
			if($time[3] != 0)
				$needtogo = 3;
			if($time[2] != 0)
				$needtogo = 2;
			if($time[1] != 0)
				$needtogo = 1;
			if($time[0] != 0)
				$needtogo = 0;

			for ($count = 5; $count >= $needtogo; $count--):
				if($count == 5)
					if($time[$count] == 1)
						$returnstring = $time[$count] . " " . $names[$count] . " ago.";
					else if($time[$count] == 0)
						$returnstring = "0 " . $name[$count] . " ago.";
					else
						$returnstring = $time[$count] . " " . $name[$count] . " ago.";
				else
					if($time[$count] == 1)
						$returnstring = $time[$count]." ".$names[$count].", ".$returnstring;
					else if($time[$count] == 0)
						$returnstring = "0 ".$name[$count].", ".$returnstring;
					else
						$returnstring = $time[$count]." ".$name[$count].", ".$returnstring;
			endfor;
			
			echo $returnstring;
		}
	}

    if ($onlinecounter) {
        if (file_exists($absolute_cache_path . "userstatus.txt")) {
            $filename = $absolute_cache_path . "userstatus.txt";
            $myfile = fopen($filename, "r");
            $fileread = fread($myfile, filesize($filename));
            fclose($myfile);
            $userstatus = $fileread;
        } else
            die("Missing cache pls run performcheck.php!");
    }
	
	if(file_exists($absolute_cache_path."lastcheck.txt")){
		$filename = $absolute_cache_path."lastcheck.txt";
		$myfile = fopen($filename, "r");
		$fileread = fread($myfile, filesize($filename));
		fclose($myfile);
		$lastcheck = date_create_from_format('Y-m-d H:i:s', $fileread);
	} else
		die("Missing cache pls run performcheck.php!");
	
	$now2 = new DateTime('now');
	
	echo '<html><head>
	<title>Pokémon Online Revolution Status Checker by BigBrainAFK</title>
	<meta charset="UTF-8">
	<meta content="a233bd814848cf0bc3ea" name="wot-verification"> 
	<meta content="Server Status checker for Pokémon Revolution Online" name="description">
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
			die("Missing cache pls run performcheck.php!");
		
		if($i == 0)
			echo '</br><span style="display:none">1</span>';
		
		echo '
			The '.$servers[$i]["displayname"].' is ';
			
			if($status == "online")
				echo '<span style="color:green">online</span>!'; 
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