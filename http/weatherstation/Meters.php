<?php
class Meter {
	public $id;
	public $name;
	public $unit;
	public $symbol;
	private $calibration;
	private $pwmval;
	private $file;
	private $line;
	public $value;
	public $last_update;
	public $gauge;
	public $stored;
	private $mysqli;

	public function __construct($id) {
		//connect to mysql database:
		$this->mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$this->mysqli->connect_error);
		//search for this id in database:
		$meter_query = "SELECT `name`, `unit`, `symbol`, `calibration`,`pwmval`, `file`, `line`,`value`, `last_update`, `gauge`, `stored` FROM `meters` WHERE `id`=?";
		$stmt = $this->mysqli->prepare($meter_query) or die ("couldn't prepare meter_query: ".$this->mysqli->error);
		$stmt->bind_param("s", $id) or die ("couldn't bind meter_query: ".$this->mysqli->error);
		$stmt->execute() or die ("couldn't execute meter_query: ".$this->mysqli->error);
		$stmt->bind_result($this->name, $this->unit, $this->symbol, $calibration,$this->pwmval, $this->file, $this->line, $this->value, $this->last_update, $this->gauge, $this->stored) or die ("couldn't bind result meter_query: ".$this->mysqli->error);
		$success = $stmt->fetch();
		if ($success === NULL) {
			$stmt->close();
			$this->mysqli->close();
			die ("meter does not exist!");
		}
		else if ($success === FALSE) {
			$stmt->close();
			$this->mysqli->close();
			die ("couldn't fetch meter_query: ".$this->mysqli->error);
		}
		else {
			$stmt->close();
			$this->id = $id;
			$this->calibration = unserialize($calibration);
		}
	}

	public function __destruct() {
		$this->mysqli->close();
	}

	public function set($value, $last_update, $set_gauge = TRUE) { //write current values to database
			if ($set_gauge === TRUE AND $this->gauge === 1) {
				$pwmval = $this->get_pwmval($value);
				$update_query = "UPDATE `meters` SET `value`=?, `last_update`=?, `pwmval`=? WHERE id=?";
				$stmt = $this->mysqli->prepare($update_query) or die ("couldn't prepare update_query: ".$this->mysqli->error);
				$stmt->bind_param("diis", $value, $last_update,$pwmval,$this->id) or die ("couldn't bind update_query: ".$this->mysqli->error);
			}
			else {
				$update_query = "UPDATE `meters` SET `value`=?, `last_update`=? WHERE id=?";
				$stmt = $this->mysqli->prepare($update_query) or die ("couldn't prepare nopwm update_query: ".$this->mysqli->error);
				$stmt->bind_param("dis", $value, $last_update,$this->id) or die ("couldn't bind nopwm update_query: ".$this->mysqli->error);
			}
			$stmt->execute() or die ("couldn't execute update_query: ".$this->mysqli->error);
			$stmt->close();
			$this->value = $value;
			$this->last_update = $last_update;
			if ($set_gauge === TRUE AND $this->gauge === 1) {
				$this->pwmval = $pwmval;
			}
	}
	
	public function insert($time, $value, $update = TRUE, $set_gauge = TRUE) { // insert measurement in database, possibly update current values as well
        if ($this->stored === 1) { 
            $insert_query = "INSERT INTO `".$this->id."` (`time`, `value`) VALUES (?,?) ON DUPLICATE KEY UPDATE value = ?";
	    	$stmt = $this->mysqli->prepare($insert_query) or die ("couldn't prepare insert_query: ".$this->mysqli->error);
	    	$stmt->bind_param("idd", $time, $value, $value) or die ("couldn't bind insert_query: ".$this->mysqli->error);
	    	$stmt->execute() or die ("couldn't execute insert_query: ".$this->mysqli->error);
	    	$stmt->close();

	    	if ($update === TRUE) {
	    		$this->set($value, $time,$set_gauge);
	    	}
	    	return true;
	    }
	    else {
	    	die ("meter doesn't store data!");
	    }
	}
	
	private function get_pwmval($testval) {
		$vals = $this->calibration;
		asort($vals);
		$closest_lowest_pwm = 0;
		$closest_lowest_output = $vals[min(array_keys($vals))];
		$closest_highest_pwm = max(array_keys($vals));
		$closest_highest_output = $vals[$closest_highest_pwm];
		if ($testval < $closest_lowest_output) {
			return 0;
		}
		else {
			foreach ($vals as $pwm=>$output) {
				if ($output <= $testval) { 
					$closest_lowest_pwm = $pwm;
					$closest_lowest_output = $output;
				}
				if ($output >= $testval) {
					$closest_highest_pwm = $pwm;
					$closest_highest_output = $output;
					break;
				}
			}
			if ($closest_lowest_pwm == $closest_highest_pwm) {
				return $closest_lowest_pwm;
			}
			else {
				
				$d = ($testval - $closest_lowest_output) / ($closest_highest_output - $closest_lowest_output);
				$y = $closest_lowest_pwm * (1-$d) + $closest_highest_pwm * $d;
				return round($y);
			}
		}
		
	} 

}
?>
