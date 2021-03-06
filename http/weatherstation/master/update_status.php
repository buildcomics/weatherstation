<?php
/* update_status.php to insert new meter serialnumber, description and id from insert.php forms and get new doubles with new information
 * Written by Tom van den Berg for Event-Engineers
 */
header('Content-Type: application/json'); //Encode as json for javascript to easily format
require_once("functions.php"); //include stuff like mysql functions
require_once("get_doubles.php"); //function to get data and calculate doubles
$mysqli = db_connect();

if (strlen(@$_POST["new_id"]) > 2 AND strlen(@$_POST["new_desc"]) > 2 AND strlen(@$_POST["new_sr"]) > 2) { //New meter inserted
	$insert_meter_query = "INSERT INTO `meters`(`id`, `serialnr`, `desc`,`type`, `ip`, `mac`) VALUES (?,?,?, 'unknown', 'unknown', 'unknown')";
	$stmt = $mysqli->prepare($insert_meter_query) or error_call ("meter insert query error: ".$mysqli->error);
	$stmt->bind_param("sss",$_POST["new_id"], $_POST["new_sr"], $_POST["new_desc"]);
	$stmt->execute() or error_call ("execute insert meter query error: ".$mysqli->error);
	$data = get_doubles($mysqli, $_POST["session"]);
	$data["success"] = TRUE;
	$data["session"] = $_POST["session"];
}
elseif(@$_POST["insert"] == "insert") { //time to insert into database
	$doubledata = get_doubles($mysqli, $_POST["session"]); //get original start time
	$oldstart = $doubledata["start"]; //original start
	$newend = $doubledata["end"]; //original end
	$meter = $doubledata["meter_id"]; //original meter
	$time_offset = 0;
	$newstart = $oldstart;
	if(is_numeric(@$_POST["new_start"]) && @$_POST["new_start"] > 10) { //different start given
		$time_offset = $_POST["new_start"]-$oldstart; //calculate offset in time 
		$newstart = $_POST["new_start"];
		$newend = $newend+$time_offset; //calculate new end time
	}
	$data["start"] = $newstart; //add start to json data
	$data["end"] = $newend; //add end to json data
	if (strlen(@$_POST["other_meter"]) > 2) {//Other meter given
		$meter = $_POST["other_meter"];
	}
	$data["meter"] = $meter; //add meter to json data
	if(@$_POST["double_action"] == "overwrite") { //overwrite old values with new ones:
		$insert_query = "REPLACE";	
	}
	else { //default, otherwise keep the ones in the database:
		$insert_query = "INSERT IGNORE";
	}
	require_once("parameters.php"); //get all parameters in $GLOBALS["parameters"]
	$insert_query .= "`data`(`meter`,`".implode(array_keys(array_slice($GLOBALS["parameters"],1)),"`,`")."`) 
		SELECT ?, `time`+".$time_offset.",`".implode(array_keys(array_slice($GLOBALS["parameters"],2)),"`,`")."` FROM `tmp_data` WHERE `session`=?";
	$stmt = $mysqli->prepare($insert_query) or error_call("insert query:".$mysqli->error);
	$stmt->bind_param("ss",$meter, $_POST["session"]);
	$stmt->execute() or error_call("Execute inserrt data query error: ".$mysqli->error);
	$stmt->close();

	$delete_query = "DELETE FROM `tmp_data` WHERE `session`=?";
	$stmt = $mysqli->prepare($delete_query) or error_call("delete query:".$mysqli->error);
	$stmt->bind_param("s", $_POST["session"]);
	$stmt->execute() or error_call("delete query execute error: ".$mysqli->error);
	$stmt->close();
	$data["success"] = TRUE; //return success
}
elseif(strlen(@$_POST["other_meter"]) > 2 || (is_numeric(@$_POST["new_start"]) && @$_POST["new_start"] > 10)) { //different meter or start time selected
	if(!(is_numeric(@$_POST["new_start"]) AND  @$_POST["new_start"] > 10) OR @$_POST["insert"] == "reset") {
		$_POST["new_start"] = NULL;
	}
	if (!(strlen(@$_POST["other_meter"]) > 2) OR @$_POST["insert"] == "reset") {
		$_POST["other_meter"] = NULL;
	}
	$data = get_doubles($mysqli, $_POST["session"],$_POST["new_start"], $_POST["other_meter"]);

	$data["success"] = TRUE;
	$data["session"] = $_POST["session"];
	$data["info"] = "other meter!";
}
else { //no new meter, or other data
	$data["success"] = FALSE;
	$data["error"] = "Please fill in all the fields!";
}
echo json_encode($data);
?>
