<?php
require_once("../config.php");
require_once("../Meters.php");

function debug($m) {
    file_put_contents("/srv/http/weatherstation/power/input.txt", "DEBUG: ".$m.PHP_EOL , FILE_APPEND);
}
$dsmr["power_in"]["pattern"] = "/1-0:1.7.0.*/";
$dsmr["power_in"]["match"] = "/.*\((.*?)\*.*/";
$dsmr["power_in"]["factor"] = 1000;

$dsmr["energy_t1"]["pattern"] = "/1-0:1.8.1.*/";
$dsmr["energy_t1"]["match"] = "/.*\((.*?)\*.*/";
$dsmr["energy_t1"]["factor"] = 1;
$dsmr["energy_t1"]["skip"] = 60; //only update every 60 seconds

$dsmr["gas"]["pattern"] = "/0-1:24.2.1.*/";
$dsmr["gas"]["match"] = "/.*\(.*\)\((.*?)\*.*/";
$dsmr["gas"]["factor"] = 1;
$dsmr["gas"]["time_match"] = "/.*\((.*?)S.*/";;

$dsmr["energy_t2"]["pattern"] = "/1-0:1.8.2.*/";
$dsmr["energy_t2"]["match"] = "/.*\((.*?)\*.*/";
$dsmr["energy_t2"]["factor"] = 1;
$dsmr["energy_t2"]["skip"] = 60; //only update every 60 seconds

//0-1:24.2.1(200823230000S)(03296.369*m3)

echo "input: received\n";
//echo "input: ".$_POST["input"]."\n";
//file_put_contents("/srv/http/weatherstation/power/input.txt", date("c").": ".$_POST["input"]."\n", FILE_APPEND);
//Split input
$split_input = explode(PHP_EOL, $_POST["input"]);
//file_put_contents("/srv/http/weatherstation/power/input.txt", print_r($split_input, true), FILE_APPEND);

foreach ($dsmr as $key => $value) {
    //debug("checkign for :".$key);
    $matches = preg_grep($value["pattern"], $split_input);
    $result = 0;
    //debug("matches for ".$key.":".count($matches));
    foreach ($matches as $row) {
        $result = preg_match($value["match"],$row, $match);
     //   file_put_contents("/srv/http/weatherstation/power/input.txt", print_r($match,true) , FILE_APPEND);
    }
    if ($result === 1) {
        //debug("result on ".$key);
        if (array_key_exists("time_match", $value)) { //use time
            $time_result = preg_match($value["time_match"],$row, $time_match);
            if ($time_result === 1) {
                //file_put_contents("/srv/http/weatherstation/power/input.txt", "time \"".$key."\":".$time_match[1].PHP_EOL , FILE_APPEND);
                $date = DateTime::createFromFormat("ymdHis",$time_match[1]);
                
                $val= $match[1]*$value["factor"];
                
                //file_put_contents("/srv/http/weatherstation/power/input.txt", "value \"".$key."\":".$value.PHP_EOL , FILE_APPEND);
                if (is_numeric($val)) {
                    $meter = new Meter($key);
                    $meter->insert($date->getTimestamp(), $val);
                }
            }
            else {
                file_put_contents("/srv/http/weatherstation/power/input.txt", "ERROR could not get time:".$key , FILE_APPEND);
            }
        }
        else {
            $val= $match[1]*$value["factor"];
            //file_put_contents("/srv/http/weatherstation/power/input.txt", "value \"".$key."\":".$value.PHP_EOL , FILE_APPEND);
            if (is_numeric($val)) {
                $meter = new Meter($key);
                if (array_key_exists("skip", $value)) {//Check if we need to test for time skip
                    if (($meter->last_update + $value["skip"]) < time()) {//Only if some time has passed
                        $meter->insert(time(), $val);
                    }
                }
                else { 
                    $meter->insert(time(), $val);
                }
            }
        }
    }
    else {
        debug("Could not get:".$key);
    }
        
}
?>
