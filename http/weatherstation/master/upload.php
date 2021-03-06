<?php
/* File to upload and parse data file 
 * Written by Tom van den Berg for Event-Engineers
 * September 2015
 */
date_default_timezone_set("Europe/Amsterdam"); //Set timezone Europe/Amsterdam
header('Content-Type: application/json');
set_time_limit(1200); //set time limit to 20 minutes
if ($_FILES["file"]["error"] === UPLOAD_ERR_OK) {
	$extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
	$jsondata["extension"] = $extension;
	$accepted_types = array("txt", "csv", "xls");
	if (in_array($extension, $accepted_types)) {
		require_once("parameters.php");
		require_once("functions.php");
		$mysqli = db_connect();
		if($extension == "csv") { //csv file, handle as one
			$jsondata["status"] = "ok, csv file found, guessing 63A meter!"; //return status 
			require_once("parse_63.php");
			$jsondata = array_merge($jsondata, parse_63($_FILES["file"]["tmp_name"], $mysqli));
		}
		elseif($extension == "txt") { //txt file, handle as one
			$jsondata["status"] = "ok, TXT file found, guessing EMS96 meter!"; //return status 
			require_once("parse_ems96.php");
			$jsondata = array_merge($jsondata, parse_ems96($_FILES["file"]["tmp_name"], $mysqli));
		}
		elseif($extension == "xls") {
			$jsondata["status"] = "ok, xls file found, guessing PEL103 meter!"; //return status 
			require_once("parse_pel2.php");
			$jsondata = array_merge($jsondata, parse_pel($_FILES["file"]["tmp_name"], $mysqli));
		}
		else {
			$jsondata["error"] = "wtf!";
		}
		require_once("get_doubles.php");
		$jsondata = array_merge($jsondata, get_doubles($mysqli, $jsondata["session"]));
	}
	else {
		$jsondata["error"] = "Unknown file format \"".$_FILES["file"]["type"]."\", only know: application/vnd.ms-excel and text/csv";
	}
}
else {
	$jsondata["error"] = "Upload error: ".$_FILES["file"]["error"];	
}
echo json_encode($jsondata);
?>
