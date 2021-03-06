<?php 
/*
 * createtables.php to set up the initial database
 * written by Tom van den Berg for Event-engineers
 * september 2015
 */
include ("parameters.php");
include ("functions.php");

$mysqli = db_connect();
$query = "SET FOREIGN_KEY_CHECKS=0;";  //disable first
//temporary data table
$query .= "DROP TABLE IF EXISTS `tmp_data`;
	CREATE TABLE `tmp_data` ( 
	`session` VARCHAR(50) NOT NULL COMMENT 'Session used to upload the temporary data and later reference', 
	`insert_time` BIGINT NOT NULL COMMENT 'timestamp of entering data used for deletion',
	`time` BIGINT NOT NULL COMMENT 'timestamp of measurement', 
	`meter` VARCHAR(50) NOT NULL COMMENT 'serial number of meter'";

foreach($GLOBALS["parameters"] as $key=>$value) {
	if($key != "date" AND $key != "time") {
		$query .= ", `".$key."` DECIMAL(20,5) null COMMENT '".$value["desc"]["en"]." (".$value["unit"].")'";
	}
}
$query .= ") COMMENT = 'Temporary storage for data before entering in main table';";


//data table:
$query .= "DROP TABLE IF EXISTS `data`;
	CREATE TABLE `data` (
	`time` BIGINT NOT NULL COMMENT 'timestamp of measurement', 
	`meter` VARCHAR(20) NOT NULL COMMENT 'id of meter from meters table'";

foreach($GLOBALS["parameters"] as $key=>$value) {
	if($key != "date" AND $key != "time") {
		$query .= ", `".$key."` DECIMAL(20,5) null COMMENT '".$value["desc"]["en"]." (".$value["unit"].")'";
	}
}
$query .= ", PRIMARY KEY(`time`, `meter`),
		FOREIGN KEY (meter) REFERENCES meters(id) ON UPDATE CASCADE
	) COMMENT = 'Main data storage table unique by time and meter';";

//runs table:
$query .= "DROP TABLE IF EXISTS `runs`;
	CREATE TABLE `runs` (
	`id` INT NOT NULL AUTO_INCREMENT COMMENT 'id of run',
	`project` INT NOT NULL COMMENT 'ID of parent project',
	`name` VARCHAR(200) NOT NULL COMMENT 'Name of run',
	`desc` VARCHAR(255) NOT NULL COMMENT 'Description of run',
	`meter` VARCHAR(20) NOT NULL COMMENT 'id of meter of this run',
	`start` BIGINT NOT NULL COMMENT 'start timestamp of this run',
	`end` BIGINT NOT NULL COMMENT 'end timestamp of this run',
	PRIMARY KEY (`id`),
	INDEX `projectindex` (`project`),
	FOREIGN KEY (meter) REFERENCES meters(id) ON UPDATE CASCADE
	) COMMENT = 'Runs to split up all data and meters';";

//projects table:
$query .= "DROP TABLE IF EXISTS `projects`;
	CREATE TABLE `projects` (
	`id` INT NOT NULL AUTO_INCREMENT COMMENT 'unique id for project',
	`name` VARCHAR(200) NOT NULL COMMENT 'Name of project',
	`date` BIGINT NOT NULL COMMENT 'Start date of project',
	`desc` VARCHAR(255) NULL COMMENT 'Description of project',
	PRIMARY KEY (`id`)
	) COMMENT = 'Table of projects that can have different measurement runs';";

//meters table:
$query .= "DROP TABLE IF EXISTS `meters`;
	CREATE TABLE `meters` (
	`id` VARCHAR(20) NOT NULL COMMENT 'Event-Engineers ID of meter',
	`serialnr` VARCHAR(50) NOT NULL COMMENT 'Serial of meter used to auto-detect meter',
	`desc` VARCHAR(100) NULL COMMENT 'Description of meter',
	`type` VARCHAR(20) NULL COMMENT 'Type of meter',
	`ip` VARCHAR(20) NULL COMMENT 'IP address of meter',
	`mac` VARCHAR(20) NULL COMMENT 'MAC address of meter',
	PRIMARY KEY(`id`),
	UNIQUE `serial` (`serialnr`)
	) COMMENT = 'All meters available with serial numbers and description';";

$query = "SET FOREIGN_KEY_CHECKS=1;";  //enable again first
$mysqli->multi_query($query) or die("Create tables: ".$mysqli->error);
?>

