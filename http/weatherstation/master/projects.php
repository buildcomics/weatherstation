<?php
/* projects.php to get suggestions for projects
 * Written by Tom van den Berg for Event-Engineers
 */
require_once("functions.php"); //include stuff like mysql functions
header('Content-Type: application/json'); //Encode as json for javascript to easily format
$mysqli = db_connect();
if(strlen($_GET["term"]) > 1) { //project given, insert project
	$like = $_GET["term"]."%";
	$projects_query = "SELECT `id`,`name` FROM `projects` WHERE name LIKE ?";
	$stmt = $mysqli->prepare($projects_query) or error_call("prepare projects query error: ".$mysqli->error);
	$stmt->bind_param("s",$like);
	$stmt->execute() or error_call("execute projects query errror: ".$mysqli->error);
	$stmt->bind_result($id,$name);
	$count = 0;
	while ($stmt->fetch()) {
		$data[$count]["id"] = $id;
		$data[$count]["label"] = $name;
		$count++;
	}
	$stmt->close();
	echo json_encode($data);
}
?>
