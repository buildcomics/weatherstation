<?php
require_once("config.php");
function write_pwmvalues() {
	//connect to mysql database:
	$mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$mysqli->connect_error);
	
	//search for this id in database:
	$pwmvalues_query = "SELECT `pwmval`, `line` FROM `meters` WHERE `file`='pwmvalues' ORDER BY `line` ASC";
	$stmt = $mysqli->prepare($pwmvalues_query) or die ("couldn't prepare pwmvalues_query: ".$mysqli->error);
	$stmt->execute() or die ("couldn't execute pwmvalues_query: ".$mysqli->error);
	$stmt->bind_result($pwmval,$line) or die ("couldn't bind result pwmvalues_query: ".$mysqli->error);
	$file = fopen("/srv/http/weatherstation/pwmvalues", "w") or die("unable to open file");
	while ($stmt->fetch()) {
		$pwmvalues[$line] = $pwmval;
		fwrite($file, $pwmval."\n");
	}
	fclose($file);
	$stmt->close();
	$mysqli->close();
	if (file_exists($GLOBALS["pwmvalues"])) {
		copy("/srv/http/weatherstation/pwmvalues", $GLOBALS["pwmvalues"]);
	}
	else {
		echo "no pwmvalues file \n";
	}
}

function write_servovalues() {
	//connect to mysql database:
	$mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$mysqli->connect_error);
	
	//search for this id in database:
	$servovalues_query = "SELECT `pwmval`, `line` FROM `meters` WHERE `file`='servovalues' ORDER BY `line` ASC";
	$stmt = $mysqli->prepare($servovalues_query) or die ("couldn't prepare servovalues_query: ".$mysqli->error);
	$stmt->execute() or die ("couldn't execute servovalues_query: ".$mysqli->error);
	$stmt->bind_result($pwmval,$line) or die ("couldn't bind result servovalues_query: ".$mysqli->error);
	$file = fopen("/srv/http/weatherstation/servovalues", "w") or die("unable to open file");
	while ($stmt->fetch()) {
		$servovalues[$line] = $pwmval;
		fwrite($file, $pwmval);
	}
	fclose($file);
	$stmt->close();
	$mysqli->close();
	if (file_exists($GLOBALS["servovalues"])) {
		copy("/srv/http/weatherstation/servovalues", $GLOBALS["servovalues"]);
	}
	else {
		echo "no servovalues file \n";
	}	
}

?>
