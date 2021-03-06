<?php
header('Content-Type: text/csv'); //Encode as csv for javascript to easily format
require_once("config.php"); //include stuff like mysql functions
date_default_timezone_set("Europe/Amsterdam");
$mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$this->mysqli->connect_error);
if(strlen(@$_GET["id"]) > 2 ) { //if run id given, retrieve data:
	$data_query = "SELECT `time`,`value` FROM `".$_GET["id"]."` WHERE `time`>= ? AND `time` <= ?";
	echo "time, value\n";	
	$stmt = $mysqli->prepare($data_query) or die("prepare data_query query error: ".$mysqli->error); //prepare statement
	$stmt->bind_param("ii",$_GET["start"],$_GET["end"]);
	$stmt->execute() or die("execute data_query query error: ".$mysqli->error." query: ".$data_query);
	$stmt->bind_result($time,$value);
	while ($stmt->fetch()) {
		echo date("Y/m/d H:i:s",$time); //echo right format of date
		echo ",".$value."\n";
	}
	$stmt->close();
	$mysqli->close();
}
else {
	echo "Please select a run first! Try <a href=\"index.php?page=projruns\">Projects and Runs</a>";
}
?>
