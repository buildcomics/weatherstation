<?php
/* new.php to save new projects and runs to database
 * Written by Tom van den Berg for Event-Engineers
 */
if(strlen($_POST["new"]) < 3) error_call ("Not included or no post data!");
require_once("functions.php"); //include stuff like mysql functions
$mysqli = db_connect();
if($_POST["new"] == "project") { //project given, insert project
	$insert_query = "INSERT INTO `projects` (`name`, `date`, `desc`) VALUES(?,?,?)";
	$stmt = $mysqli->prepare($insert_query) or error_call("prepare insert query error: ".$mysqli->error);
	$date = strtotime($_POST["date"]);
	$stmt->bind_param("sis",$_POST["name"], $date, $_POST["desc"]);
	$stmt->execute() or error_call("execute insert query errror: ".$mysqli->error);
	$stmt->close();
}
elseif ($_POST["new"] == "run") {//add run to project
	list($item,$id) = explode("_",$_POST["item"]); //Split item_id into $item and $id
	$newrun_query = "INSERT INTO `runs`(`project`, `name`, `desc`, `meter`, `start`, `end`) VALUES (?,?,?,?,?,?)";
	$stmt = $mysqli->prepare($newrun_query) or error_call("prepare newrun query error: ".$mysqli->error);
	$stmt->bind_param("isssii",$id, $_POST["name_".$id], $_POST["desc_".$id], $_POST["newmeter_".$id],$_POST["new_start"], $_POST["new_end"]);
	$stmt->execute() or error_call("newrun query execute error: ".$mysqli->error);
	$stmt->close();	
}
elseif ($_POST["new"] == "delete") { //delete an item
	list($item,$id) = explode("_",$_POST["item"]); //Split item_id into $item and $id
	if ($item == "project") {//delete project and all attached runs
		$delete_query = "DELETE FROM `projects` WHERE id=? LIMIT 1";
		$stmt = $mysqli->prepare($delete_query) or error_call("prepare delete query error: ".$mysqli->error);
		$stmt->bind_param("i", $id);
		$stmt->execute() or error_call("execute delete query error: ".$mysqli->error);
		$stmt->close();

		$delete_runs_query = "DELETE FROM `runs` WHERE project=?";
		$stmt = $mysqli->prepare($delete_runs_query) or error_call("prepare delete runs query error: ".$mysqli->error);
		$stmt->bind_param("i",$id);
		$stmt->execute() or error_call("execute delete runs query error: ".$mysqli->error);
		$stmt->close();
	}
	elseif($item == "run") { //delete run
		$delete_run_query = "DELETE FROM `runs` WHERE id=?";
		$stmt = $mysqli->prepare($delete_run_query) or error_call("prepare delete run query error: ".$mysqli->error);
		$stmt->bind_param("i",$id);
		$stmt->execute() or error_call("execute delete run query error: ".$mysqli->error);
		$stmt->close();
	}elseif($item == "meter") {
		$delete_run_query = "DELETE FROM `meters` WHERE id=? LIMIT 1";
		$stmt = $mysqli->prepare($delete_run_query) or error_call("prepare delete run query error: ".$mysqli->error);
		$stmt->bind_param("s",$id);
		if(!$stmt->execute() AND $mysqli->errno == 1451) {
			$GLOBALS["error"] = "Could not delete meter '".htmlspecialchars($id)."', there is a run and/or data associated with it.";
		}
		elseif(!$stmt->execute()) {
			error_call ("meter delete query exec error: ".$mysqli->error);
		}
		$stmt->close();
	}

}
elseif ($_POST["new"] == "meter") {//add new meter 
	$newmeter_query = "INSERT INTO `meters`(`id`, `desc`, `serialnr`,`type`, `ip`, `mac`) VALUES (?,?,?,?,?,?)";
	$stmt = $mysqli->prepare($newmeter_query) or error_call("prepare newmeter query error: ".$mysqli->error);
	$stmt->bind_param("ssssss",$_POST["id"], $_POST["desc"], $_POST["serialnr"],$_POST["type"], $_POST["ip"], $_POST["mac"]);
	$stmt->execute() or error_call ("newmeter query execute error: ".$mysqli->error);
	$stmt->close();	
}
?>
