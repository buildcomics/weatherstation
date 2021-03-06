<?php
function db_connect() {
	//$mysqli = new mysqli("localhost", "admevnt", "tFNvrU1r", "admevnt");
	$mysqli = new mysqli("localhost", "root", "mysqlp4ss", "ee");
	if ($mysqli->connect_error) {
		error_call('Connect Error (' . $mysqli->connect_errno . ') '
				. $mysqli->connect_error);
	}
	return $mysqli;
}
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}
function refValues($arr){ //function to combat reference problem, stolen from fabio at http://php.net/manual/en/mysqli-stmt.bind-param.php 
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}
function error_call($error) {
	trigger_error("User triggered error: ".$error);
	die("ERROR: ".$error);
}
function excelColumnRange($lower, $upper) {
    ++$upper;
    for ($i = $lower; $i !== $upper; ++$i) {
        yield $i;
    }
}
function excelArrayRange($lower, $upper) {
	foreach (excelColumnRange($lower,$upper) as $value) {
		$rangearray[] = $value;
	}
	return $rangearray;
}
?>
