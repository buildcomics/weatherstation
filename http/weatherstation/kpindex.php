<?php
function get_kp() {
    $text = file_get_contents($GLOBALS["kp_url"]);
    if ($text !== FALSE) {
        $lines = explode("\n", $text);
        $start = FALSE;
        $data = array();
        foreach ($lines as $key => $line) {
        	if(strpos($line, "NOAA Kp index forecast") === 0) {
        		$start = "header";
        	}
        	elseif( $start === "header") {
        		$header = preg_split("/[\s]{2,}/", trim($line));
        		$start = "data";
        	}
        	elseif ($start === "data") {
        		$data[] = preg_split("/[\s]+/", trim($line));
        	}
        }
        //find current date in header to get data indices:
        $curday = date("M d");
        $dayind = array_search($curday, $header);
        if ($dayind === FALSE) {
            return false; //couldn't find current day
        }
        $hourind = floor(date("G")/3);
        return $data[$hourind][$dayind+1];
    }
    else {
        return false;
    }
}
?>
