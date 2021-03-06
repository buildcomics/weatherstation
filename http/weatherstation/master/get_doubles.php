<?php
/* Function to detect meter and find amount of double measurements for inserting a new data set
 * Written by: Tom van den Berg for Event-Engineers
 * September 2015
 */
function get_doubles($mysqli, $session, $newstart = NULL, $othermeter = NULL) { //find double measurements according to optional new start and meter_id for insert session
	
	$session_query = "SELECT MAX(`time`) as `end`, MIN(`time`) as `start`,meter FROM `tmp_data` WHERE `session`=?"; //select maximum and minimum time plus meter serial nr for particular session
	$stmt = $mysqli->prepare($session_query) or error_call("session query: ".$mysqli->error); 
	$stmt->bind_param("s", $session);
	$stmt->execute() or error_call("double execute: ".$mysqli->error);
	$stmt->bind_result($end,$start,$meter_sr);
	$stmt->store_result(); //store result to be able to count
	$stmt->fetch();
	if($stmt->num_rows === 1) { //result found, session exists:
		$data["end"] = $end;
		$data["start"] = $start;
		$data["meter_sr"] = $meter_sr;
	}
	else { //session does not exist
		$data["error"]["session"] = "Session does not exist, num_rows:".$stmt->num_rows;
		return $data; //stop
	}
	$stmt->close();

	if(!is_null($newstart)) { //if new start time is given:
		$data["end"] = $newstart + ($data["end"]-$data["start"]); //calculate new end
		$data["start"] = $newstart;
	}
	if ((time() - $data["end"])/(365*24*60*60) > 1) { //if data is more than one year old
		$data["warning"]["time"] = "Data is more than one year old.";
	}

	if(!is_null($othermeter)) {//new meter id given, find desc and see if it exists
		$othermeter_query = "SELECT `desc` FROM `meters` WHERE `id` = ?"; //get meter description
		$stmt = $mysqli->prepare($othermeter_query) or error_call("othermeter query: ".$mysqli->error);
		$stmt->bind_param("s", $othermeter);
		$stmt->execute() or error_call("othemeter execute: ".$mysqli->error);
		$stmt->store_result(); //store to be able to count
		if($stmt->num_rows === 1) { //meter found
			$data["meter_id"] = $othermeter;
			$stmt->bind_result($data["meter_desc"]);
			$stmt->fetch();
		}
		else { //other meter not given
			$data["warning"]["meter"] = "User inputted meter not found, you're messing with the form, trying to match meter anyway";
			$othermeter = NULL;
		}
		$stmt->close();
	}

	if(is_null($othermeter)) { //no new meter given, match serial number (if possible)
		$meter_query = "SELECT `id`,`desc` FROM `meters` WHERE serialnr=?";
		$stmt = $mysqli->prepare($meter_query) or error_call ("meter query: ".$myslqi->error);
		$stmt->bind_param("s", $meter_sr); //bind serial number to serialnr parameter
		$stmt->execute() or error_call("meter execute: ".$mysqli->error);
		$stmt->store_result(); //store to be able to count
		if ($stmt->num_rows === 1) { //meter found!
			$stmt->bind_result($meter_id, $meter_desc);
			$stmt->fetch();
			$data["meter_id"] = $meter_id;
			$data["meter_desc"] = $meter_desc;
		}
		elseif($stmt->num_rows < 1) {
			$data["error"]["meter"] = "No meter found that matched serial number \"".$meter_sr."\".";
			//return $data; //stop
		}
		else {
			$data["error"]["meter"] = "Something weird happened, more then one meter found for serial number \"".$meter_sr."\".";
			//return $data; //stop
		}
		$stmt->close();
	}

	//Get all meters
	$meter_query = "SELECT `id`, `desc`, `serialnr` FROM `meters`";
	$stmt = $mysqli->prepare($meter_query) or error_call("meter query: ".$mysqli->error);
	$stmt->execute() or error_call("Meter Query Execute: ".$mysqli->error);
	$stmt->bind_result($id, $desc, $serialnr);
	while ($stmt->fetch()) {
		$data["meters"][$id]["desc"] = $desc;
		$data["meters"][$id]["serialnr"] = $serialnr;
	}
	$data["start"] -= $data["start"]%60; //needed for proper doubles detection since time is only minute based
	//get doubles for specific meter and start/stop time
	$double_query = "SELECT COUNT(*) FROM `data` WHERE meter = ? AND time >= ? AND time <= ?"; //select for specific meter and time
	$stmt = $mysqli->prepare($double_query) or error_call("double query:".$mysqli->error);
	$stmt->bind_param("sii",$data["meter_id"], $data["start"], $data["end"]);
	$stmt->execute() or error_call("double execute: ".$mysqli->error);
	$stmt->bind_result($data["double_count"]);
	$stmt->fetch(); //fetch result
	$stmt->close();
	return $data;
}
?>
