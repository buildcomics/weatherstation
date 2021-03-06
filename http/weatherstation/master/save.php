<?php
 /* save.php to store changes to project and run names, descriptions, dates, starts and ends
 * Written by Tom van den Berg for Event-Engineers
 */
header('Content-Type: application/json'); //Encode as json for javascript to easily format
if(strstr(@$_POST["item"],"_")===FALSE) {
	$data["error"] = "No or invalid item given!";
}
elseif(strlen(@$_POST["data"]) < 3) {
	$data["error"] = "Too less (or no) data";
}
else {
	require_once("functions.php"); //include stuff like mysql functions
	require_once("get_doubles.php"); //function to get data and calculate doubles
	$mysqli = db_connect();
	list($item,$id) = explode("_",$_POST["item"]); //Split item_id into $item and $id
	if($item === "projname") { //update project name in projects table
		$table = "projects";
		$field = "name";
	}elseif ($item == "projdesc") { //update project description in projects table
		$table = "projects";
		$field = "desc";
	}elseif ($item == "projdate") {
		$table = "projects";
		$field = "date";
	}elseif ($item == "runname") {
		$table = "runs";
		$field = "name";
	}elseif ($item == "runmeter") {
		$table = "runs";
		$field = "meter";
	}elseif ($item == "runstart") {
		$table = "runs";
		$field = "start";
	}elseif ($item == "rundesc") {
		$table = "runs";
		$field = "desc";
	}elseif ($item == "meterdesc") {
		$table = "meters";
		$field = "desc";
	}elseif ($item == "meterserialnr") {
		$table = "meters";
		$field = "serialnr";
	}elseif ($item == "meterid") {
		$table = "meters";
		$field = "id";
	}elseif ($item == "metertype") {
		$table = "meters";
		$field = "type";
	}elseif ($item == "meterip") {
		$table = "meters";
		$field = "ip";
	}elseif ($item == "metermac") {
		$table = "meters";
		$field = "mac";
	}elseif ($item == "runend") {
		$table = "runs";
		$field = "end";
	}else {
		$data["error"] = "unknown item!";
	}
	
	if (!@$data["error"]) {
		$update_query = "UPDATE `".$table."` SET `".$field."`=? WHERE id=? LIMIT 1";
		$stmt = $mysqli->prepare($update_query) or error_call("prepare update query error: ".$mysqli->error);
		$stmt->bind_param("ss", $_POST["data"], $id);
		$stmt->execute() or error_call("execute prepare update query error:".$mysqli->error);
		$stmt->close(); //close statement
		$mysqli->close(); 
		$data["success"] = TRUE;
	}
}
echo json_encode($data);
?>
