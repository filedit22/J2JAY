<?php
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

    function get_difference($time, $mode = "none"){
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

            if($mode != "none"){
                switch($mode) {
                    case "s":
                        return (int)$time[5];
                    case "i":
                        return (int)$time[4];
                    case "h":
                        return (int)$time[3];
                    case "d":
                        return (int)$time[2];
                    case "m":
                        return (int)$time[1];
                    case "y":
                        return (int)$time[0];
                    default:
                        return false;
                }
                return true;
            }

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
            return true;
        }
    }
?>