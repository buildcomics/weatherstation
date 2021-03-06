<?php
/* projruns.php to display all projects and attached runs, and possibility to add new runs over AJAX 
 * Written by Tom van den Berg for Event-Engineers
 */
if (strlen(@$_POST["new"]) > 2) { //new project given
	require_once("new.php"); //insert into database
}
$mysqli = db_connect();
//Get all meters
$meter_query = "SELECT `id`, `desc` FROM `meters` ORDER BY `id` ASC";
$stmt = $mysqli->prepare($meter_query) or error_call("meter query: ".$mysqli->error);
$stmt->execute() or error_call("Meter Query Execute: ".$mysqli->error);
$stmt->bind_result($id, $desc);
while ($stmt->fetch()) {
	$meters[$id] = $desc;
}
?>
<div class="container" style="width: 1300px;">
	<div class="page-header">
		<h1>Projects and runs</h1>
	</div>
		<form class="form-inline" method="post" id="form">
		<table class="table table-condensed" id="projrunstable">
			<thead>
				<tr>
					<th class="col-sm-1"><!--Empty--></th>
					<th class="col-sm-1"><a href="index.php?page=projruns">Name</a></th>
					<th class="col-sm-1"><a href="index.php?page=projruns&sort=date">Date</a></th>
					<th class="col-sm-1"><!--Empty--></th>
					<th class="col-sm-1"><!--Empty--></th>
					<th class="col-sm-1"><!--Empty--></th>
					<th class="col-sm-1">Description</th>
					<th class="col-sm-1"><!--Empty--></th>
					<th class="col-sm-1"><!--Empty--></th>
					<th class="col-sm-1"><!--Empty--></th>
				</tr>
				<tr class="info">
					<th><!--Empty--></th>
					<th class="h4 small">Name</th>
					<th class="h4 small">Start Date</th>
					<th class="h4 small">Start Time</th>
					<th class="h4 small">End Date</th>
					<th class="h4 small">End Time</th>
					<th class="h4 small">Description</th>
					<th class="h4 small">Meter</th>
					<th><!--Empty--></th>
					<th class="h4 small">Datapoints</th>
				</tr>
			</thead>
			<tbody>
				<?php
			//time to get all parents and childs using something like 
			$projruns_query = "SELECT `projects`.`id`,`projects`.`name`,`date`,`projects`.`desc`,`runs`.`id` AS 'runid',`runs`.`name` AS 'runname',`runs`.`desc` AS 'rundesc', `runs`.`meter`, `runs`.`start`, `runs`.`end`,  
				(SELECT COUNT(*) FROM `data` WHERE `data`.`time` > `runs`.`start` AND `data`.`time` < `runs`.`end`) 'runcount'	
				FROM `projects` LEFT JOIN `runs` ON `projects`.`id` = `runs`.`project` ORDER BY ";
			if(@$_GET["sort"]=="date") {
				$projruns_query .= "`projects`.`date` DESC, `runs`.`name` ASC";
			}
			else { //otherwise sort by name
				$projruns_query .= "`projects`.`name`,`runs`.`name` ASC"; //Query to get all projects and attached runs
			}
			$stmt = $mysqli->prepare($projruns_query) or error_call ("prepare projruns query error: ".$mysqli->error); //prepare statement
			$stmt->execute() or error_call ("execute projruns query error: ".$mysqli->error);
			$stmt->bind_result($projid, $projname, $projdate, $projdesc, $runid, $runname, $rundesc, $runmeter, $runstart, $runend,$runcount);
			$previous_projid = NULL;
			$previous_parent = NULL;
			$count = 0;
			while ($stmt->fetch()) { //for every row received from the database
				if ($count !== 0 && ($previous_projid !== $projid)) { //if there are projects, add this one to last  
						$count++;
						echo "<tr data-node=\"treetable-".$count."\" data-pnode=\"treetable-parent-".$previous_parent."\" class=\"success\">
						<td><!--Empty--></td>
						<td>
							<input type=\"text\" name=\"name_".$previous_projid."\" id=\"newname_".$previous_projid."\" placeholder=\"Name of New Run\">
						</td>
						<td id=\"new_runstart\">
								<input type=\"hidden\" id=\"newstartdate_".$previous_projid."\" class=\"datepicker\" value=\"".date("d-m-Y",$projdate)."\">
								<span class=\"date glyphicon glyphicon-calendar\" aria-hidden=\"true\">".date("d-m-Y",$projdate)."</span>
						</td>
						<td class=\"input-group clockpicker\" style=\"width: 10em;\">
							<input type=\"text\" class=\"form-control\" id=\"newstarttime_".$previous_projid."\" value=\"01:00\">
							<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-time\"></span></span>
						</td>
						<td id=\"new_runend\">
								<input type=\"hidden\" id=\"newenddate_".$previous_projid."\" class=\"datepicker\" value=\"".date("d-m-Y",$projdate)."\">
								<span class=\"date glyphicon glyphicon-calendar\" aria-hidden=\"true\">".date("d-m-Y",$projdate)."</span>
						</td>
						<td class=\"input-group clockpicker\" style=\"width: 10em;\">
							<input type=\"text\" class=\"form-control\" id=\"newendtime_".$previous_projid."\" value=\"23:59\">
							<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-time\"></span></span>
						</td>
						<td>
							<input type=\"text\" name=\"desc_".$previous_projid."\" id=\"newdesc_".$previous_projid."\" placeholder=\"Description of New Run\">
						</td>
						<td>
							<select id=\"newmeter_".$previous_projid."\" name=\"newmeter_".$previous_projid."\">\n";
							foreach($meters as $meter=>$desc) {
								echo "-\t\t\t\t\t\t\t\t<option value=\"".$meter."\">".htmlspecialchars($meter).": ".htmlspecialchars($desc)."</option>\n";

							}
						echo "\t\t\t\t\t\t\t</select>
						</td>
						<td>
							<button class=\"btn btn-sm btn-success savebutton\" type=\"button\" name=\"new\" value=\"run_".$previous_projid."\" id=\"newrun_".$previous_projid."\">Save</button>
						</td>
						<td><!--empty--></td>
					</tr>";
					}
				if ($projid !== $previous_projid) { //new project (e.g. not another row from matched run)
					$count++;
					echo "<tr data-node=\"treetable-".$count."\" class=\"projrunrow active\">
						<td><!--Empty--></td>
						<td><span contenteditable=\"true\" id=\"projname_".$projid."\">".htmlspecialchars($projname)."</span></td>
						<td id=\"projdate_".$projid."\">
							<input type=\"hidden\" class=\"datepicker\" value=\"".date("d-m-Y",$projdate)."\">
							<span class=\"date glyphicon glyphicon-calendar\" aria-hidden=\"true\">".date("d-m-Y",$projdate)."</span>
						</td>
						<td><!--empty--></td>
						<td><!--empty--></td>
						<td><!--empty--></td>
						<td contenteditable=\"true\" id=\"projdesc_".$projid."\">
							".htmlspecialchars($projdesc)."
						</td>
						<td><!--empty--></td>
						<td><!--empty--></td>
						<td>
							<button class=\"deletebutton btn btn-xs pull-right btn-danger\" type=\"button\" name=\"new\" value=\"project_".$projid."\">
								<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>
							</button>
						</td>
				</tr>";
					$previous_projid = $projid;
					$previous_parent = $count;
				}
				if ($runid !== NULL) {//attached run, display
					$count++;
					echo "<tr data-node=\"treetable-".$count."\" data-pnode=\"treetable-parent-".$previous_parent."\" class=\"projrunrow\">
						<td><!--Empty--></td>
						<td contenteditable=\"true\" id=\"runname_".$runid."\">".htmlspecialchars($runname)."</td>
						<td id=\"runstart_".$runid."\">
							<input type=\"hidden\" id=\"runstartdate_".$runid."\" class=\"datepicker\" value=\"".date("d-m-Y",$runstart)."\">
							<span class=\"date glyphicon glyphicon-calendar\" aria-hidden=\"true\">".date("d-m-Y",$runstart)."</span>
						</td>
						<td class=\"input-group clockpicker\" style=\"width: 10em;\">
							<input type=\"text\" class=\"form-control\" id=\"runstarttime_".$runid."\" value=\"".date("H:i",$runstart)."\">
							<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-time\"></span></span>
						</td>
						<td id=\"runend_".$runid."\">
								<input type=\"hidden\" id=\"runenddate_".$runid."\" class=\"datepicker\" value=\"".date("d-m-Y",$runend)."\">
								<span class=\"date glyphicon glyphicon-calendar\" aria-hidden=\"true\">".date("d-m-Y",$runend)."</span>
						</td>
						<td class=\"input-group clockpicker\" style=\"width: 10em;\">
								<input type=\"text\" class=\"form-control\" id=\"runendtime_".$runid."\" value=\"".date("H:i",$runend)."\">
								<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-time\"></span></span>
						</td>
						<td id=\"rundesc_".$runid."\" contenteditable=\"true\">".htmlspecialchars($rundesc)."</td>
						<td>
							<select id=\"runmeter_".$runid."\" class=\"meterselect\">\n";
							foreach($meters as $meter=>$desc) {
								echo "\t\t\t\t\t\t\t\t<option value=\"".$meter."\"";
								if ($runmeter == $meter) {
									echo " selected=\"selected\"";
								}
								echo ">".htmlspecialchars($meter).": ".htmlspecialchars($desc)."</option>\n";
							}
						echo "\t\t\t\t\t\t\t</select>
						</td>
						<td>
							<a class=\"btn btn-info\" href=\"index.php?page=graph&run=".$runid."\" role=\"button\">Graph!</a>
						</td>
						<td>".$runcount."
							<button class=\"deletebutton btn btn-xs pull-right btn-danger\" type=\"button\" name=\"new\" value=\"run_".$runid."\">
								<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>
							</button>
						</td>
				</tr>";
				}
			}
			if($count > 0) {
				$count++;
				echo "<tr data-node=\"treetable-".$count."\" data-pnode=\"treetable-parent-".$previous_parent."\" class=\"success\">
						<td><!--Empty--></td>
						<td>
							<input type=\"text\" name=\"name_".$previous_projid."\" id=\"newname_".$previous_projid."\" placeholder=\"New Run Name\">
						</td>
						<td id=\"new_runstart\">
								<input type=\"hidden\" id=\"newstartdate_".$previous_projid."\" class=\"datepicker\" value=\"".date("d-m-Y",$projdate)."\">
								<span class=\"date glyphicon glyphicon-calendar\" aria-hidden=\"true\">".date("d-m-Y",$projdate)."</span>
						</td>
						<td class=\"input-group clockpicker\" style=\"width: 10em;\">
							<input type=\"text\" class=\"form-control\" id=\"newstarttime_".$previous_projid."\" value=\"01:00\">
							<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-time\"></span></span>
						</td>
						<td id=\"new_runend\">
								<input type=\"hidden\" id=\"newenddate_".$previous_projid."\" class=\"datepicker\" value=\"".date("d-m-Y",$projdate)."\">
								<span class=\"date glyphicon glyphicon-calendar\" aria-hidden=\"true\">".date("d-m-Y",$projdate)."</span>
						</td>

						<td class=\"input-group clockpicker\" style=\"width: 10em;\">
							<input type=\"text\" class=\"form-control\" id=\"newendtime_".$previous_projid."\" value=\"23:59\">
							<span class=\"input-group-addon\">
								<span class=\"glyphicon glyphicon-time\"></span>
							</span>
						</td>
						<td>
							<input type=\"text\" name=\"desc_".$previous_projid."\" id=\"newdesc_".$previous_projid."\" placeholder=\"Description of New Run\">
						</td>
						<td>
							<select id=\"newmeter_".$previous_projid."\" name=\"newmeter_".$previous_projid."\">\n";
							foreach($meters as $meter=>$desc) {
								echo "\t\t\t\t\t\t\t\t<option value=\"".$meter."\">".htmlspecialchars($meter).": ".htmlspecialchars($desc)."</option>\n";

							}
						echo "\t\t\t\t\t\t\t</select>
						</td>
						<td>
							<button class=\"btn btn-sm btn-success savebutton\" type=\"button\" name=\"new\" value=\"run_".$previous_projid."\" id=\"newrun_".$previous_projid."\">Save</button>
						</td>
						<td><!--empty--></td>
					</tr>";
			}
			$stmt->close(); //close prepared statement
			$count++; //for last row
			?> 
				<tr data-node="treetable-<?php echo $count; ?>" class="warning">
					<td><!--Empty--></td>
					<td><input type="text" class="form-control" pattern=".{3,200}" required title="Minimum of 3 characters" name="name" placeholder="Name of New Project"></td>
					<td class="input-group" style="width: 10em;"><input type="text" class="datepicker form-control" required id="new_projdate" name="date" placeholder="Date"></td>
					<td><!--emtpy--></td>
					<td><!--emtpy--></td>
					<td><!--emtpy--></td>
					<td><input type="text" class="form-control" pattern=".{3,255}" required title="Minimum of 3 characters" name="desc" placeholder="Description of project"></td>
					<td><button type="submit" class="btn btn-success" id="submit_button" name="new" value="project">Save</button></td>
					<td><!--emtpy--></td>
					<td><!--emtpy--></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="item" id="hidden_input" value="empty">
		</form>
	</div>
	<script src="js/bootstrap-treefy.min.js"></script><!-- Bootstrap Treefy Tables JS-->
	<script src="js/save.js"></script><!-- Javascript for saving contenteditable -->
	<script src="js/projruns.js"></script><!-- Javascript for project runs -->
