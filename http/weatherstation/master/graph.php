<?php
/* graph.php to show graphs of runs 
 * Written by Tom van den Berg for Event-Engineers
 */
if(is_numeric(@$_GET["run"]) && @$_GET["run"] > 0) { //if run id given, retrieve data:
	//$run_query = "SELECT `name`, `meter`,`start`,`end` FROM `runs` WHERE `runs`.`id`=?";
	$run_query = "SELECT `runs`.`name` AS runname,`projects`.`name` AS projname, `meter`,`start`,`end` FROM `runs` INNER JOIN `projects` ON `runs`.`project`=`projects`.`id` WHERE `runs`.`id`=?";
	$mysqli = db_connect(); //connect to mysql database
	$stmt = $mysqli->prepare($run_query) or die("prepare run query error: ".$mysqli->error); //prepare statement
	$stmt->bind_param("i",$_GET["run"]);
	$stmt->execute() or die("execute reuns query error: ".$mysqli->error);
	$stmt->bind_result($name,$projname,$meter,$start,$end);
	$start_html = date("d-m-Y H:i:s",$start);
	$end_html = date("d-m-Y H:i:s",$end);
	$stmt->fetch();
	$stmt->close();
	$mysqli->close();
	if (strlen(@$_GET["params"]) < 1) {//if zero parameters
		$_GET["params"] = "I1,I2,I3";
	}
	if(!(is_numeric(@$_GET["timeavg"]) && @$_GET["timeavg"] > 0)) { //if no time average given
		$_GET["timeavg"] = 0;
	}

	$title_html = htmlspecialchars(@$_GET["params"]." of \"".$name."\"");
	require_once("parameters.php"); //for parameter list

	$params = explode(",",@$_GET["params"]); //split all parameters into array
	$xlabels = array(); //avoid notice
	foreach($params as $parameter) {
		if(array_key_exists($parameter,$GLOBALS["parameters"]) AND !in_array($GLOBALS["parameters"][$parameter]["unit"], $xlabels)) {
			$xlabels[] = $GLOBALS["parameters"][$parameter]["unit"];
		}
	}
	$xlabel = implode(",",$xlabels);

?>
		<div class="container" style="width: 1200px;">
			<div class="page-header">
				<h1>Graph for "<?php echo htmlspecialchars($projname); ?>"<br>
				<small>Run: "<?php echo $name; ?>"</small></h1>
			</div>
			<div id="graph" style="width: 1100px; height: 400px;"></div>
			<br>
			<div class="row">
				<div class="col-md-4">
					<button type="button" class="btn btn-success" id="submit">New Graph!</button>
				</div>
				<div class="col-md-4">
					<a href="" id="download_button" class="btn btn-success" role="button">Download Graph</a>
				</div>
				<div class="col-md-4">
					<a href="csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>" id="csv_button" class="pull-right btn btn-success" role="button">Download Data</a>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-5">	
					<h4>Select parameters:</h4>
					<select multiple class="form-control" name="params" id="params" size="<?php echo count($GLOBALS["parameters"])-2; ?>">
						<?php
						foreach ($GLOBALS["parameters"] as $key=>$value) {
							if($key != "time" AND $key !== "date") {
								echo "\t\t\t\t<option value=\"".$key."\"";
								if (in_array($key, $params)) {
									echo " selected=\"selected\"";
								}
								echo ">".htmlspecialchars($key.":".$value["desc"]["en"]."(".$value["unit"].")")."</option>\n";
							}
						}
						?>
					</select>
				</div>
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
							<input type="checkbox" id="yzero_checkbox" checked>Start Y at zero</input>
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
					  <div class="checkbox">
						<label>
						  <input type="checkbox" id="minmax_checkbox"> Use Min,Max and Average</input> 
						</label>
					  </div>
						<div class="form-group">
							<label for="timeavg_input">Average Time in Minutes (0=No average):<br></label>
							<input type="number" id="timeavg_input" min="0" value="0">
					  </div>
					<br><strong>Image:</strong><br>
					<img id="demoimg" style="width: 100%;">
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
							<td>Exported Energy (KWh):</td>
							<td class="text-center" colspan="3" id="e_exp">x</td>
						</tr>
						<tr>
							<td>Imported Energy (KWh):</td>
							<td class="text-center" colspan="3" id="e_imp">x</td>
						</tr>
						<tr>
							<td>Current Phase 1 (A)</td>
							<td id="I1min">x</td>
							<td id="I1avg">x</td>
							<td id="I1max">x</td>
						</tr>
						<tr>
							<td>Current Phase 2 (A)</td>
							<td id="I2min">x</td>
							<td id="I2avg">x</td>
							<td id="I2max">x</td>
						</tr>
						<tr>
							<td>Current Phase 3 (A)</td>
							<td id="I3min">x</td>
							<td id="I3avg">x</td>
							<td id="I3max">x</td>
						</tr>
						<tr>
							<td>Current Total (A)</td>
							<td id="Itmin">x</td>
							<td id="Itavg">x</td>
							<td id="Itmax">x</td>
						</tr>
						<tr>
							<td>Total Power (kW)</td>
							<td id="Pmin">x</td>
							<td id="Pavg">x</td>
							<td id="Pmax">x</td>
						</tr>
						<tr>
							<td>Total Power (kVA)</td>
							<td id="Smin">x</td>
							<td id="Savg">x</td>
							<td id="Smax">x</td>
						</tr>
						<tr>
							<td>Voltage Phase 1 (V)</td>
							<td id="V1min">x</td>
							<td id="V1avg">x</td>
							<td id="V1max">x</td>
						</tr><tr>
							<td>Voltage Phase 2 (V)</td>
							<td id="V2min">x</td>
							<td id="V2avg">x</td>
							<td id="V2max">x</td>
						</tr><tr>
							<td>Voltage Phase 3 (V)</td>
							<td id="V3min">x</td>
							<td id="V3avg">x</td>
							<td id="V3max">x</td>
						</tr>
					</tbody>	
				</table>
			</div>
		</div>
		<script src="js/dygraph-combined.js"></script><!-- dygraph main core -->
		<script src="js/dygraph-extra.js"></script><!-- some extra stuff, for responsive system  -->
		<script><?php include("js/graph.php"); ?></script>
<?php
}
else {
	echo "Please select a run first! Try <a href=\"projruns.php\">projruns.php</a>";
}
?>
