<?php
/* Function to parse TXT file from EMS96 current meters
 * Written by: Tom van den Berg for Event-Engineers
 * September 2015
 */
function parse_ems96($file, $mysqli) {
	ini_set('auto_detect_line_endings',TRUE);//important for annoying mac users	
	if(($f = fopen($file, "r")) !== FALSE) { //If file could be opened
		$row = 1; //start at row 1
		$jsondata["count"] = 0; //start count at zero	
		$session = uniqid(true);
		$jsondata["session"] = $session;
		while (($line = fgets($f, 800)) !== FALSE) { //as long as there is valid csv data
			if($row === 3) { //third row contains serial number
				$metersr = trim(substr($line,5));
			}
			elseif ($row === 11) { //11th row, match parameters to column numbers
				$data = str_getcsv($line, "	");
				foreach($data as $col=>$value) { //for every column (or field in this row)
					$value = trim($value);
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
			$stmt = $mysqli->prepare($data_query) or error_func("data query:".$mysqli->error);
		
			//use user_func_array to call bind_param with all parameters (use references?)
			$types = "ssi".str_repeat("d",count($GLOBALS["parameters"])-1);
			}			
			elseif($row > 12) {// after line 12 (starting at 13) real data starts 
				$data = preg_split("/[\s]+/", $line);
				if(strlen($data[$GLOBALS["parameters"]["date"]["column"]]) < 2) { //empty row, we are finished
					break;
				}
				$jsondata["count"]++;
				//$time = strtotime($data[$GLOBALS["parameters"]["date"]["column"]]." ".$data[$GLOBALS["parameters"]["time"]["column"]]);
				$time = date_create_from_format("d/m/Y H:i:s",$data[$GLOBALS["parameters"]["date"]["column"]]." ".$data[$GLOBALS["parameters"]["time"]["column"]]);
				if ($time === FALSE) {
					$jsondata["parserror"] = "Could not parse time '".$data[$GLOBALS["parameters"]["date"]["column"]]." ".$data[$GLOBALS["parameters"]["time"]["column"]]."' at row ".$row.".";
					break;
				}
				//Get rest of parameters for in database:  //add the amount of parameters +3(session,meter,insert_time)-2(excess date parameter and start)
				unset($arguments); //empty array
				$arguments[] = $types; //first argument is types
				$arguments[] = $session; //Unique session id
				$arguments[] = $metersr; //Meter serial number
				$arguments[] = time(); //time of insertion
				$arguments[] = $time->getTimestamp(); //time of measurement
				foreach ($GLOBALS["parameters"] as $key => $parameter) { //for every parameter
					if (!($key == "time" OR $key == "date") AND isset($parameter["column"])) {// parameter is available
						$arguments[] = str_replace(",",".",$data[$parameter["column"]]);
					}
					elseif(!($key == "time" OR $key == "date")) {
						$arguments[] = NULL;
					}
				}
				call_user_func_array(array($stmt, "bind_param"), refValues($arguments)) or error_func ("bind param tmp_data error: ".$mysqli->error);

				$stmt->execute() or error_func("execute tmp_data query on line ".$row.": ".$mysqli->error);

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
