<?php
function get_sunsetrise() {
date_default_timezone_set("Europe/Amsterdam");
    $lat = $GLOBALS["lat"];
    $lon = $GLOBALS["long"];
    $time = time();
    $sunrise = date_sunrise($time, SUNFUNCS_RET_TIMESTAMP, $lat, $lon);
    $minutes_to_sunrise = round(($sunrise-$time)/60);
    $sunset = date_sunset($time, SUNFUNCS_RET_TIMESTAMP, $lat, $lon);
    $minutes_to_sunset = round(($sunset-$time)/60);
    $sunrise = date_sunrise($time+24*60*60, SUNFUNCS_RET_TIMESTAMP, $lat, $lon);
    $sunrise_tomorrow_minutes = round(($sunrise-$time)/60);
    //create value between -30 and 1500(24h+) minutes
    if ($minutes_to_sunrise >= -30 && $minutes_to_sunrise <= 0) { //just past sunrise
    	$value = $minutes_to_sunrise;
    }
    elseif ($minutes_to_sunset >= -30 && $minutes_to_sunset <= 0) {// just past sunset
    	$value = $minutes_to_sunset;
    }
    elseif ($minutes_to_sunrise < -30 && $minutes_to_sunset < -30) { //tomorrow's sunrise is next up
    	$value = $sunrise_tomorrow_minutes;
    }
    elseif ($minutes_to_sunrise > 0 && $minutes_to_sunrise < $minutes_to_sunset) { //next up, sunrise!
    	$value = $minutes_to_sunrise;
    }
    else {//must be sunset?
    	$value = $minutes_to_sunset;
    }
    return $value;
}
?>
