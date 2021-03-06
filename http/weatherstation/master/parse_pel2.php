<?php
/* parse_pel2.php for parsing abstracts from PEL103 devices by Chauvin Arnoux
 *  Written by Tom van den Berg for Event-Engineers
 */
function parse_pel($file, $mysqli) {
	/** Include PHPExcel_IOFactory */
	require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';
	$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
	\PHPExcel_Settings::setCacheStorageMethod($cacheMethod);	
	$objReader = new PHPExcel_Reader_Excel5();
	$objReader->setReadDataOnly(true);
	class readFilter implements PHPExcel_Reader_IReadFilter { //Readfilter to read by chunks 
		private $_startRow = 0; 
		private $_endRow   = 0; 
		private $_mode = "config";
		public function setRows($startRow, $chunkSize) { 
			$this->_startRow = $startRow; 
			$this->_endRow   = $startRow + $chunkSize; 
		} 
		public function setMode($mode) {
			$this->_mode = $mode;
		}
		public function readCell($column, $row, $worksheetName = '') { 
			if($this->_mode == "data") {
				if(in_array($worksheetName, array("Samenvatting","Summary"))) { //only for these worksheets
					if ($row >= $this->_startRow && $row < $this->_endRow) {
					   if (in_array($column,excelArrayRange("A", "EA"))) { //Only first 2 columns
						  return true;
					   }
					}
				}
			}
			else {
				if(in_array($worksheetName, array("Configuratie","Configuration"))) { //only read these worksheets
					if ($row >= 16 && $row <= 18) {//only row 1-20 
					   if (in_array($column,range('A','B'))) { //Only first 2 columns
						  return true;
					   }
					}
				}
			}
			return false; 
		}
	}

	//get serial number
	$readFilter = new readFilter(); //make new read filter
	$objReader->setReadFilter($readFilter); //attach to objReader
	$readFilter->setMode("config"); //set mode to config
	$objPHPExcel = $objReader->load($file); //load file with config reader
	$objWorksheet = $objPHPExcel->getSheet(0); //select first sheet
	$serialNumber = $objWorksheet->getCellByColumnAndRow(1,17)->getValue(); //get serial number
	$objPHPExcel->disconnectWorksheets(); //free up some memory
	unset($objPHPExcel); //free up some more memory
	unset($objWorksheet);//free up even more memory
	
	//get column name match:
	$readFilter->setMode("data"); //set mode to data
	$readFilter->setRows(1,2); //read first row only
	$objPHPExcel = $objReader->load($file);	
	$objWorksheet = $objPHPExcel->getSheet(3); //select third sheet
	
	for($cell = 0; $cell <= 138; $cell++) { //for every column (or field in this row)
		foreach ($GLOBALS["parameters"] as $key => $parameter) { //for every parameter
			if (in_array($objWorksheet->getCellByColumnAndRow($cell,1)->getValue(), $parameter["names"])) { //see if one of the names matches this field in the csv file
				$GLOBALS["parameters"][$key]["column"] = $cell; //if it does, set column number in parameter array
				$jsondata["parameterlist"][$key] = $parameter["desc"]["en"]; //add to parsed parameter list
			}
		}
	}
	$objPHPExcel->disconnectWorksheets(); //free up some memory
	unset($objPHPExcel); //free up some more memory
	unset($objWorksheet);//free up even more memory
	
	//Prepare insert query
	$data_query = "INSERT INTO `tmp_data`(`session`,`meter`,`insert_time`,`".implode(array_keys(array_slice($GLOBALS["parameters"],1)),"`,`")."`) VALUES(?
			".str_repeat(",?",count($GLOBALS["parameters"])+1).")";
	$stmt = $mysqli->prepare($data_query) or error_call("data query:".$mysqli->error);
	$types = "ssi".str_repeat("d",count($GLOBALS["parameters"])-1);

	//get data:
	$session = uniqid(true); //create uniq session
	$jsondata["session"] = $session; //add to json data
	$jsondata["count"] = 0; //start count at zero	
	$chunksize = 10000; //size of chunk to be read (10000 equals about 600MB memory usage)
	for($startRow = 3; true; $startRow += $chunksize) {
		$readFilter->setRows($startRow,$chunksize); //set start row and length to read
		$objPHPExcel = $objReader->load($file);	//load file
		$objWorksheet = $objPHPExcel->getSheet(3); //select third sheet
		for ($row = $startRow; $row < $startRow+$chunksize; $row++) { //go through all cells
			if (strlen($objWorksheet->getCellByColumnAndRow(1,$row)->getValue()) < 2) {
				break 2;
			}
			else { //skip second row
				$jsondata["count"]++;
				$date = $objWorksheet->getCellByColumnAndRow($GLOBALS["parameters"]["date"]["column"],$row)->getValue();
				$time = $objWorksheet->getCellByColumnAndRow($GLOBALS["parameters"]["time"]["column"],$row)->getValue();
				$timestamp = strtotime($date." ".$time);
				if ($time === FALSE) {
					$jsondata["parserror"] = "Could not parse time at row ".$row." : \"".$date." ".$time."\"";
					break 2;
				}
				//Get rest of parameters for in database:  //add the amount of parameters +3(session,meter,insert_time)-2(excess date parameter and start)
				unset($arguments); //empty array
				$arguments[] = $types; //first argument is types
				$arguments[] = $session; //Unique session id
				$arguments[] = $serialNumber; //Meter serial number
				$arguments[] = time(); //time of insertion
				$arguments[] = $timestamp; //time of measurement
				foreach ($GLOBALS["parameters"] as $key => $parameter) { //for every parameter
					if (!($key == "time" OR $key == "date") AND isset($parameter["column"])) {// parameter is available
						$arguments[] = $objWorksheet->getCellByColumnAndRow($parameter["column"],$row)->getValue();
					}
					elseif(!($key == "time" OR $key == "date")) {
						$arguments[] = NULL;
					}
				}
				call_user_func_array(array($stmt, "bind_param"), refValues($arguments)) or error_call ("bind param tmp_data error: ".$mysqli->error);
				$stmt->execute() or error_call("execute tmp_data query: ".$mysqli->error);
			}
		}
		$objPHPExcel->disconnectWorksheets(); //free up some memory
		unset($objPHPExcel); //free up some more memory
		unset($objWorksheet);//free up even more memory
	}
	return $jsondata;
}
?>
