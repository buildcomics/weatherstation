<?php
function get_pwmval($vals, $testval) {
	asort($vals);
	$closest_lowest_pwm = 0;
	$closest_lowest_output = $vals[min(array_keys($vals))];
	$closest_highest_pwm = max(array_keys($vals));
	$closest_highest_output = $vals[$closest_highest_pwm];
	if ($testval < $closest_lowest_output) {
		return 0;
	}
	else {
		foreach ($vals as $pwm=>$output) {
			if ($output <= $testval) { 
				$closest_lowest_pwm = $pwm;
				$closest_lowest_output = $output;
			}
			if ($output >= $testval) {
				$closest_highest_pwm = $pwm;
				$closest_highest_output = $output;
				break;
			}
		}
		if ($closest_lowest_pwm == $closest_highest_pwm) {
			return $closest_lowest_pwm;
		}
		else {
			
			$d = ($testval - $closest_lowest_output) / ($closest_highest_output - $closest_lowest_output);
			$y = $closest_lowest_pwm * (1-$d) + $closest_highest_pwm * $d;
			return round($y);
		}
	}
	
}
?>