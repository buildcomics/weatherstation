<?php
/* get_meters.php to get all meters for specific live types from database
   Written by Tom van den Berg for Event-Engineers
   */
header('Content-Type: application/json'); //Encode as json for javascript to easily format
require_once("functions.php"); //include stuff like mysql functions
require_once("get_doubles.php"); //function to get data and calculate doubles
require_once("parameters.php"); //get parameters, primarily for livemeters
$mysqli = db_connect();
$meters_query = "SELECT * FROM `meters` WHERE `type` IN ('".implode("', '", $GLOBALS["livetypes"])."');";
$result = $mysqli->query($meters_query) or error_call("meters query error: ".$mysqli->error);
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	$meters[] = $row;
}
echo json_encode($meters);
?>
