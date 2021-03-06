<?php
require_once("../config.php");
require_once("../Meters.php");

$dsmr["power_in"]["pattern"] = "/1-0:1.7.0.*/";
$dsmr["power_in"]["match"] = "/.*\((.*?)\*.*/";
$dsmr["power_in"]["factor"] = 1000;

$dsmr["energy_t1"]["pattern"] = "/1-0:1.8.1.*/";
$dsmr["energy_t1"]["match"] = "/.*\((.*?)\*.*/";
$dsmr["energy_t1"]["factor"] = 1;

$dsmr["energy_t2"]["pattern"] = "/1-0:1.8.2.*/";
$dsmr["energy_t2"]["match"] = "/.*\((.*?)\*.*/";
$dsmr["energy_t2"]["factor"] = 1;

$dsmr["gas"]["pattern"] = "/0-1:24.2.1.*/";
$dsmr["gas"]["match"] = "/.*\(.*\)\((.*?)\*.*/";
$dsmr["gas"]["factor"] = 1;
$dsmr["gas"]["time_match"] = "/.*\((.*?)S.*/";;

echo "input: received\n";
//echo "input: ".$_POST["input"]."\n";
//file_put_contents("/srv/http/weatherstation/power/input.txt", date("c").": ".$_POST["input"]."\n", FILE_APPEND);

//Split input
$split_input = explode(PHP_EOL, $_POST["input"]);
//file_put_contents("/srv/http/weatherstation/power/input.txt", print_r($split_input, true), FILE_APPEND);

foreach ($dsmr as $key => $value) {
    $matches = preg_grep($value["pattern"], $split_input);
    foreach ($matches as $row) {
        $result = preg_match($value["match"],$row, $match);
     //   file_put_contents("/srv/http/weatherstation/power/input.txt", print_r($match,true) , FILE_APPEND);
    }
    if ($result === 1) {
        if (array_key_exists("time_pattern")) { //use time
            $time_result = preg_match($value["time_match"],$row, $time_match);
            if ($time_result === 1) {
                file_put_contents("/srv/http/weatherstation/power/input.txt", "time \"".$key."\":".$time_match[1].PHP_EOL , FILE_APPEND);
                $date = DateTime::createFromFormat("ymdHis",$time_match[1]);
                
                $value = $match[1]*$value["factor"];
                
                file_put_contents("/srv/http/weatherstation/power/input.txt", "value \"".$key."\":".$value.PHP_EOL , FILE_APPEND);
                if (is_numeric($value)) {
                    $meter = new Meter($key);
                    $meter->insert(time(), $value);
                }
            }
            else {
                file_put_contents("/srv/http/weatherstation/power/input.txt", "ERROR could not get time:".$key , FILE_APPEND);
            }
        }
        else {
            $value = $match[1]*$value["factor"];
            file_put_contents("/srv/http/weatherstation/power/input.txt", "value \"".$key."\":".$value.PHP_EOL , FILE_APPEND);
            if (is_numeric($value)) {
                $meter = new Meter($key);
                $meter->insert(time(), $value);
            }
        }
    }
    else {
        file_put_contents("/srv/http/weatherstation/power/input.txt", "ERROR could not match:".$key , FILE_APPEND);
    }
        
}
///*
////$matches = preg_grep("/1-0:1.7.0.*/", $split_input);
///*
////file_put_contents("/srv/http/weatherstation/power/input.txt", print_r($matches, true), FILE_APPEND);
//
//foreach ($matches as $val) {
//    preg_match("/.*\((.*?)\*.*/",$val, $match);
// //   file_put_contents("/srv/http/weatherstation/power/input.txt", print_r($match,true) , FILE_APPEND);
//}
//$usage = $match[1]*1000;
////file_put_contents("/srv/http/weatherstation/power/input.txt", "usage:".$usage , FILE_APPEND);
//if (is_numeric($usage)) {
//    $power_in = new Meter("power_in");
//    $power_in->insert(time(), $usage);
//}
//*/
?>
