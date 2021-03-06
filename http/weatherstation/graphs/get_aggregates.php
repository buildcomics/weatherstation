<?php
header('Content-Type: application/json'); //Encode as json for javascript to easily format
if (strlen(@$_POST["meter"]) > 2 AND @$_POST["start"] > 2 AND @$_POST["end"] > 2) { //New meter inserted
	include("config.php");
	$mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$this->mysqli->connect_error);
	$aggregates_query = "SELECT MIN(`value`), AVG(`value`), MAX(`value`) FROM `".$_POST["meter"]."` WHERE `time`>= ? AND `time` <= ?";
	$stmt = $mysqli->prepare($aggregates_query) or die("Aggregates query prepare errror:".$mysqli->error);
	$stmt->bind_param("ii", $_POST["start"], $_POST["end"]);
	$stmt->execute() or error_call("Aggregates execute error: ".$mysqli->error);
	$stmt->bind_result($jsondata["min"],$jsondata["avg"],$jsondata["max"]);
	$stmt->fetch();
	$jsondata["success"] = TRUE; 
}
else {
	$jsondata["success"] = FALSE;
	$jsondata["error"] = "No valid id, start or end";
}
echo json_encode($jsondata);
?>
