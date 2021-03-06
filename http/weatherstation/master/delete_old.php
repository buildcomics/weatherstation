<?php 
/* delete_old.php to delete old data from tmp_data table in database
 * Written by Tom van den Berg for Event-Engineers
 */
include("functions.php"); //functions for mysql
$mysqli = db_connect(); //connect to decibel
$delete_query = "DELETE FROM `tmp_data` WHERE `insert_time` < ?";
$stmt = $mysqli->prepare($delete_query) or error_call("Prepare delete query error: ".$mysqli->error);
$old_time = time() - 60*60; //set time limit to 1 hour ago
$stmt->bind_param("i",$old_time);
$stmt->execute() or error_call("Execute delete query error: ".$mysqli->error);
$stmt->close();
$mysqli->close();
?>
