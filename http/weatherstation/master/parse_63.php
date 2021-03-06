<?php
/* Function to parse csv file from GMC U180C & U189A 63A current meters
 * Written by: Tom van den Berg for Event-Engineers
 * September 2015
 */
function parse_63($file, $mysqli) {
	ini_set('auto_detect_line_endings',TRUE);//important for annoying mac users	
	if(($f = fopen($file, "r")) !== FALSE) { //If file could be opened
		$row = 1; //start at row 1
		$jsondata["count"] = 0; //start count at zero	
		$session = uniqid(true);
		$jsondata["session"] = $session;
		while (($data = fgetcsv($f, 500, ";")) !== FALSE) { //as long as there is valid csv data
			if ($row === 1) { //first row, match parameters to column numbers
				foreach($data as $col=>$value) { //for every column (or field in this row)
					$GLOBALS["parameters"]; //load globals into local something
					foreach ($GLOBALS["parameters"] as $key => $parameter) { //for every parameter
						if (in_array($value, $parameter["names"])) { //see if one of the names matches this field in the csv file
							$GLOBALS["parameters"][$key]["column"] = $col; //if it does, set column number in parameter array
							$jsondata["parameterlist"][$key] = $parameter["desc"]["en"]; //add to parsed parameter list
						}
					}
				}
			//Prepare insert query
			$data_query = "INSERT INTO `tmp_data`(`session`,`meter`,`insert_time`,`".implode(array_keys(array_slice($GLOBALS["parameters"],1)),"`,`")."`) VALUES(?
					".str_repeat(",?",count($GLOBALS["parameters"])+1).")";
			$stmt = $mysqli->prepare($data_query) or error_call("data query:".$mysqli->error);
		
			//use user_func_array to call bind_param with all parameters (use references?)
			$types = "ssi".str_repeat("d",count($GLOBALS["parameters"])-1);
			}			
			else {// if not first row, format data and insert into database
				if(strlen($data[$GLOBALS["parameters"]["date"]["column"]]) < 2) { //empty row, we are finished
					break;
				}
				$jsondata["count"]++;
				$time = strtotime($data[$GLOBALS["parameters"]["date"]["column"]]." ".$data[$GLOBALS["parameters"]["time"]["column"]]);
				if ($time === FALSE) {
					$jsondata["parserror"] = "Could not parse time at row ".$row.".";
					break;
				}
				//Get rest of parameters for in database:  //add the amount of parameters +3(session,meter,insert_time)-2(excess date parameter and start)
				unset($arguments); //empty array
				$arguments[] = $types; //first argument is types
				$arguments[] = $session; //Unique session id
				$arguments[] = $data[0]; //Meter serial number
				$arguments[] = time(); //time of insertion
				$arguments[] = $time; //time of measurement
				foreach ($GLOBALS["parameters"] as $key => $parameter) { //for every parameter
					if (!($key == "time" OR $key == "date") AND isset($parameter["column"])) {// parameter is available
						$arguments[] = str_replace(",",".",$data[$parameter["column"]]);
					}
					elseif(!($key == "time" OR $key == "date")) {
						$arguments[] = NULL;
					}
				}
				call_user_func_array(array($stmt, "bind_param"), refValues($arguments)) or error_call ("bind param tmp_data error: ".$mysqli->error);

				$stmt->execute() or error_call("execute tmp_data query on line ".$row.": ".$mysqli->error);

			}
			$row++;	
		}
	}
	else {
		$jsondata["parseerror"] = "error opening file...";
	}
	return $jsondata;
}
?>
