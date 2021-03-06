<?php
/* get_aggregates.php to get aggregates for run (with selection)
 * Written by Tom van den Berg for Event-Engineers
 */
header('Content-Type: application/json'); //Encode as json for javascript to easily format
if (strlen(@$_POST["meter"]) > 2 AND @$_POST["start"] > 2 AND @$_POST["end"] > 2) { //New meter inserted
	require_once("functions.php"); //include stuff like mysql functions
	$mysqli = db_connect();
	$aggregates_query = "SELECT MIN(`I1`), AVG(`I1`), IFNULL(MAX(`I1max`), MAX(`I1`)),MIN(`I2`), AVG(`I2`),IFNULL(MAX(`I2max`), MAX(`I2`)),MIN(`I3`), AVG(`I3`),IFNULL(MAX(`I3max`), MAX(`I3`)),
		MIN(`I1`+`I2`+`I3`), AVG(`I1`+`I2`+`I3`), IFNULL(MAX(`I1max`+`I2max`+`I3max`),MAX(`I1`+`I2`+`I3`)),
		IFNULL(MIN(`V1min`),MIN(`V1`)), AVG(`V1`),IFNULL(MAX(`V1max`),MAX(`V1`)),
		IFNULL(MIN(`V2min`),MIN(`V2`)), AVG(`V2`),IFNULL(MAX(`V2max`),MAX(`V2`)),
		IFNULL(MIN(`V3min`),MIN(`V3`)), AVG(`V3`),IFNULL(MAX(`V3max`),MAX(`V3`)),
		MIN(`PT`)/1000, AVG(`PT`)/1000, MAX(`PT`)/1000,MIN(`ST`)/1000, AVG(`ST`)/1000, MAX(`ST`)/1000,
		(MAX(`Wh_imp`)-MIN(`Wh_imp`))/1000, (MAX(`Wh_exp`)-MIN(`Wh_exp`))/1000 FROM `data` WHERE `meter`=? AND `time`>= ? AND `time`<=?";
	$stmt = $mysqli->prepare($aggregates_query) or error_call("Aggregates query prepare errror:".$mysqli->error);
	$stmt->bind_param("sii", $_POST["meter"], $_POST["start"], $_POST["end"]);
	$stmt->execute() or error_call("Aggregates execute error: ".$mysqli->error);
	$stmt->bind_result($jsondata["I1min"],$jsondata["I1avg"],$jsondata["I1max"],$jsondata["I2min"],$jsondata["I2avg"],$jsondata["I2max"],$jsondata["I3min"],$jsondata["I3avg"],$jsondata["I3max"],
			$jsondata["Itmin"],$jsondata["Itavg"],$jsondata["Itmax"],
			$jsondata["V1min"],$jsondata["V1avg"],$jsondata["V1max"],$jsondata["V2min"],$jsondata["V2avg"],$jsondata["V2max"],$jsondata["V3min"],$jsondata["V3avg"],$jsondata["V3max"],
			$jsondata["Pmin"],$jsondata["Pavg"],$jsondata["Pmax"],$jsondata["Smin"],$jsondata["Savg"],$jsondata["Smax"],
			$jsondata["e_imp"],$jsondata["e_exp"]);
	$stmt->fetch();
	$jsondata["success"] = TRUE; 
}
else {
	$jsondata["success"] = FALSE;
	$jsondata["error"] = "No valid meter, start or end";
}
echo json_encode($jsondata);
?>
