<?php
/* csv.php to get data from run and param get data into a csv format 
 * Written by Tom van den Berg for Event-Engineers
 */
header('Content-Type: text/csv'); //Encode as csv for javascript to easily format
require_once("functions.php"); //include stuff like mysql functions
date_default_timezone_set("Europe/Amsterdam");
$mysqli = db_connect();
if(is_numeric(@$_GET["run"]) && @$_GET["run"] > 0) { //if run id given, retrieve data:
	$params = explode(",",@$_GET["params"]); //split all parameters into array
	if (strlen(@$_GET["params"]) < 1) {//if zero parameters
		$sql_params = ",AVG(`I1`),AVG(`I2`),AVG(`I3`)";
		$sql_error_params = ",MIN(`I1`),AVG(`I1`),MAX(`I1`),MIN(`I2`),AVG(`I2`),MAX(`I2`),MIN(`I3`),AVG(`I3`),MAX(`I3`)";
		$header_params = "Time,I1,I2,I3";
	}
	else {
		require_once("parameters.php");
		$sql_params = ""; //avoid notice
		$header_params = "Time"; //avoid notice
		$sql_error_params = ""; //avoid notice
		foreach($params as $parameter) {
			if(array_key_exists($parameter,$GLOBALS["parameters"])) {
				if(isset($GLOBALS["parameters"][$parameter]["div"])) { 
					$sql_params .= ",AVG(`".$parameter."`)/".$GLOBALS["parameters"][$parameter]["div"];
					$sql_error_params .= ",MIN(`".$parameter."`)/".$GLOBALS["parameters"][$parameter]["div"].
					",AVG(`".$parameter."`)/".$GLOBALS["parameters"][$parameter]["div"].
					",MAX(`".$parameter."`)/".$GLOBALS["parameters"][$parameter]["div"];
				}
				else {
					$sql_params .= ",AVG(`".$parameter."`)";
					$sql_error_params .= ",MIN(`".$parameter."`),AVG(`".$parameter."`),MAX(`".$parameter."`)";
				}
				$header_params .= ",".$parameter;
			}
		}
	}
	if(is_numeric(@$_GET["timeavg"]) && @$_GET["timeavg"] > 0) { //if time average given:
		$avgtime = $_GET["timeavg"]*60;
	}
	else {
		$avgtime = 5;
	}
	if(@$_GET["error"] != "false") { //if using error graph
		$data_query = "SELECT `time`".$sql_error_params." FROM `data` LEFT JOIN `runs` ON `data`.`meter`=`runs`.`meter` AND `data`.`time` >= `runs`.`start` AND `data`.`time` <= `runs`.`end` 
			WHERE `runs`.`id` ='".intval($_GET["run"])."' 
			GROUP BY `time` DIV ".$avgtime;
	}
	else { //if not using error graph
		$data_query = "SELECT `time`".$sql_params." FROM `data` LEFT JOIN `runs` ON `data`.`meter`=`runs`.`meter` AND `data`.`time` >= `runs`.`start` AND `data`.`time` <= `runs`.`end` WHERE `runs`.`id` ='".intval($_GET["run"])."'
			 GROUP BY `time` DIV ".$avgtime;
	}
	echo $header_params."\n";	
	$result = $mysqli->query($data_query) or error_call("data query prepare error: ".$mysqli->error);
	while($row = $result->fetch_row()) {
		echo date("Y/m/d H:i:s",$row[0]); //echo right format of date
		unset($row[0]); //remove first element from array
		if(@$_GET["error"] != "false") {// if using errors from database
			$values_array = array_chunk($row, 3); //split into 2 dimensional array 
			foreach($values_array as $values) { //for every parameter
				echo ",".implode($values, ";"); //Imploded list of min,middle,max value seperated by ; for dygraph
			}
		}
		else { //if not using errors
			echo ",".implode($row, ","); //echo list of seperated list followed by breake line
		}
		echo "\n"; //echo list of seperated list followed by breake line
	}
}
else {
	echo "Please select a run first! Try <a href=\"index.php?page=projruns\">Projects and Runs</a>";
}
$mysqli->close();
?>
