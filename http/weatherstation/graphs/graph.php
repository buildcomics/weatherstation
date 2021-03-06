<?php
if(strlen(@$_GET["id"]) > 2) { //if run id given, retrieve data:
	$mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$this->mysqli->connect_error);
	$data_query = "SELECT `name`,`unit`,`symbol`,`last_update`  FROM `meters` WHERE `id`=?";
	$stmt = $mysqli->prepare($data_query) or die("prepare data query error: ".$mysqli->error); //prepare statement
	$stmt->bind_param("s",$_GET["id"]);
	$stmt->execute() or die("execute data query error: ".$mysqli->error);
	$stmt->bind_result($name,$unit,$symbol,$last_update);
	$stmt->fetch();
	$last_update_html = date("d-m-Y H:i:s",$last_update);
	$stmt->close();
	$mysqli->close();
	$title_html = htmlspecialchars($name);
?>
		<div class="container" style="width: 1200px;">
			<div class="page-header">
				<h1>Graph of <?php echo htmlspecialchars($name); ?><br>
				<small>Last update: <?php echo $last_update_html; ?></small></h1>
			</div>
			<div id="graph" style="width: 1100px; height: 400px;"></div>
			<br>
			<div class="row">
				<div class="col-md-4">
					<a href="csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>" id="csv_button" class="pull-right btn btn-success" role="button">Download Data</a>
				</div>
				<div class="col-md-4">
					<a href="index.php?page=graph&id=<?php echo $_GET["id"]."&start=".(@$last_update-60*60*24*7)."&end=".$last_update; ?>" class="pull-right btn btn-success" role="button">Last Week</a>
				</div>
				<div class="col-md-4">
					<a href="index.php?page=graph&id=<?php echo $_GET["id"]."&start=".(@$last_update-60*60*24*30)."&end=".$last_update; ?>" class="pull-right btn btn-success" role="button">Last Month</a>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3">
					<h4>Graph options:</h4>
					<div class="checkbox">
						<label>
							<input type="checkbox" id="points_checkbox">Points</input>
						</label>
					</div><div class="checkbox">
						<label>
							<input type="checkbox" id="filled_checkbox">Filled</input>
						</label>
					</div><div class="checkbox">
						<label>
							<input type="checkbox" id="yzero_checkbox">Start Y at zero</input>
						</label>
					</div>
					<div class="form-inline">
					  <div class="checkbox">
						<label>
						  <input type="checkbox" id="maxy_checkbox"> Set Max Y:</input> 
						</label>
					  </div>
						<div class="form-group">
							<input type="number" id="maxy_input">
					  </div>
					</div>
					<br>
			</div>
			<div class="col-md-4">
				<h4>Abstract:</h4>
				<table class="table table-striped table-hover table-condensed">
					<thead>
						<tr>
							<td>Parameter</td>
							<td>Min</td>
							<td>Avg</td>
							<td>Max</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $unit."(".$symbol.")"; ?></td>
							<td id="min">x</td>
							<td id="avg">x</td>
							<td id="max">x</td>
						</tr>
				</tbody>	
				</table>
			</div>
			<div class="col-md-4">
				<h4>Custom Range:</h4>
				<form class="form-inline" method="post" id="form">
					<table>
						<tr>
							<td class="input-group" style="width: 10em;">
								<input type="text" class="datepicker form-control" required id="start" name="start" placeholder="Start" value="<?php echo date("d-m-y", $_GET["start"]); ?>">
							</td>
							<td class="input-group" style="width: 10em;">
								<input type="text" class="datepicker form-control" required id="end" name="end" placeholder="End" value="<?php echo date("d-m-y", $_GET["end"]); ?>">
							</td>
							<td class="input-group" style="width: 10em;">
								<button type="button" class="btn btn-success" id="submit_button" name="submit" value="submit">Show!</button>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<script src="js/dygraph-combined.js"></script><!-- dygraph main core -->
		<script src="js/dygraph-extra.js"></script><!-- some extra stuff, for responsive system  -->
		<script><?php include("js/graph.php"); ?></script>
		<script>
			function get_time(dayid) { //function to get full date object from date and clockpicker
				var day = $(dayid).datepicker("getDate"); //date object from calendar picker
				/*var minutes = 0; //get minutes
				var hours = 0; //get hours
				day.setHours(parseInt(hours));
				day.setMinutes(parseInt(minutes));*/
				return day.getTime()/1000;
			}
			$(".datepicker").datepicker({ //enable datepicker
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				changeYear: true,
			});
			$("#submit_button").click(function() {
				var start = get_time("#start");
				var end = get_time("#end");
				if (start > 0 && end > 0) {
					console.log(start);
					console.log(end);
					window.location.href = 'index.php?page=graph&id=<?php echo $_GET["id"]; ?>&start=' + start + '&end=' + end;
				}
				else {
					console.log("shit");
				}
			});
			var start = new Date();
   			start.setDate(parseInt(<?php echo date("d",$_GET["start"]);?>));
   			start.setMonth(parseInt(<?php echo date("n",$_GET["start"])-1;?>));
   			start.setYear(parseInt(<?php echo date("Y",$_GET["start"]);?>));
   		 	$('#start').datepicker('setDate', start);
   			var end = new Date();
			end.setDate(parseInt(<?php echo date("d",$_GET["end"]);?>));
   			end.setMonth(parseInt(<?php echo date("n",$_GET["end"])-1;?>));
   			end.setFullYear(parseInt(<?php echo date("Y",$_GET["end"]);?>));
   		 	$('#end').datepicker('setDate', end);

		</script>
<?php
}
else {
	echo "ohoh!";
}
?>
