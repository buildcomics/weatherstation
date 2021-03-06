<?php
include("config.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Basic Weatherstation Overview</title>
  </head>
  <style>
    tr:nth-child(1) {background: #000; color: white;}
    tr:nth-child(even) {background: #CCC}
  </style>
  <body>
  Current time: <?php echo date("c"); ?><br/>
  <table>
    <tr>
        <td>Name</td>
        <td>Value</td>
        <td>Last update</td>
    </tr>
   <?php
   $mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$mysqli->connect_error);
   //$values_query = "SELECT `name`, `symbol`, `value`, `last_update` FROM `meters` WHERE * ORDER BY `name` ASC";
   $values_query = "SELECT `name`, `symbol`, `value`, `last_update` FROM `meters` ORDER BY `name` ASC";
   $stmt = $mysqli->prepare($values_query) or die ("couldn't prepare values_query: ".$mysqli->error);
   $stmt->execute() or die ("couldn't execute values_query: ".$mysqli->error);
   $stmt->bind_result($name,$symbol,$value,$last_update) or die ("couldn't bind result values_query: ".$mysqli->error);
   while ($stmt->fetch()) {
        $diff = time()-$last_update;
        $minutes = $diff/60 % 60;
        $hours = round(($diff-($minutes*60))/(60*60));
       ?>
       <tr>
            <td><?php echo $name; ?></td>
            <td><?php echo $value." ".$symbol; ?></td>
            <td><?php echo $minutes." minutes and ".$hours." hours ago."; ?></td>
       </tr>
       <?php
   }
   $stmt->close();
   $mysqli->close();
   ?>
</body>
</html>
