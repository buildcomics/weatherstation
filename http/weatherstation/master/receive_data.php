<?php
/* receive_data.php to receive data from powergateway
 * Written by: Tom van den Berg for Event-Engineers
 * September 2015
 */
header('Content-Type: application/json'); //Encode as json for javascript to easily format
$jsondata["success"] = false;
if(@$_POST["time"][0] > 1) {
	require_once("functions.php"); //include stuff like mysql functions
	require_once("parameters.php"); //include parameters
	$mysqli = db_connect();
	$fieldnames = array();
	$values = array();
	foreach($_POST as $col=>$valuearray) { //for every column (or field in this row)
		$GLOBALS["parameters"]["meter"] = array(); //add meter parameter
		if (in_array($col, array_keys($GLOBALS["parameters"]))) { //see if one of the names matches this field in the csv file
			$fieldnames[] = $col;
			foreach($valuearray as $row=>$value) {
				$values[$row][] = $value;
			}
		}
	}
	if(count($values) >= 1) {
		//Prepare insert query
		$data_query = "REPLACE `data`(`".implode($fieldnames,"`,`")."`) VALUES(?".str_repeat(",?",count($fieldnames)-1).")";
		$stmt = $mysqli->prepare($data_query) or die("data query:".$mysqli->error);
		$types = "";
		foreach($fieldnames as $field) {
			if ($field == "time") {
				$types .= "i";
			}
			elseif($field == "meter") {
				$types .= "s";
			}
			else {
				$types .= "d";
			}
		}
		foreach($values as $valuerow) {
			$arguments = array_merge(array($types), $valuerow);
			call_user_func_array(array($stmt, "bind_param"), refValues($arguments)) or error_call ("bind tmp_data error: ".$mysqli->error);
			$stmt->execute() or error_call("execute tmp_data query: ".$mysqli->error);
		}
		$jsondata["success"] = true;
	}
	echo json_encode($jsondata);
}
?>
