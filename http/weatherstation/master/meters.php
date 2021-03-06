<?php
/* meters.php to display all meters and make them editable 
 * Written by Tom van den Berg for Event-Engineers
 */
$mysqli = db_connect();
if (@$_POST["new"] == "meter" OR @$_POST["new"] == "delete") { //new project given
	require_once("new.php"); //insert into / delete from database
}
?>

<div class="container">
	<div class="page-header">
		<h1>Change meters</h1>
	</div>
	<?php
	if (@$GLOBALS["error"]) { //if there is/was an error
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?php echo htmlspecialchars($GLOBALS["error"]); ?>
		</div>
	<?php
	}
	?>
	<form method="post" id="form">
	<table class="table table-striped table-hover" id="projrunstable">
		<thead>
			<tr>
				<th>ID</th>
				<th>Description</th>
				<th>Serial Number</th>
				<th>Type</th>
				<th>IP Address</th>
				<th>MAC Address</th>
				<th><!--empty--></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$meter_query = "SELECT `id`, `desc`,`serialnr`,`type`, `ip`, `mac` FROM `meters` ORDER BY `id` ASC";
		$stmt = $mysqli->prepare($meter_query) or error_func("meter query: ".$mysqli->error);
		$stmt->execute() or error_func("Meter Query Execute: ".$mysqli->error);
		$stmt->bind_result($id, $desc,$serialnr,$type,$ip,$mac);
		while ($stmt->fetch()) {
			echo "<tr class=\"projrunrow\">
					<td><span contenteditable=\"true\" id=\"meterid_".$id."\">".htmlspecialchars($id)."</span></td>
					<td><span contenteditable=\"true\" id=\"meterdesc_".$id."\">".htmlspecialchars($desc)."</span></td>
					<td><span contenteditable=\"true\" id=\"meterserialnr_".$id."\">".htmlspecialchars($serialnr)."</span></td>
					<td><span contenteditable=\"true\" id=\"metertype_".$id."\">".htmlspecialchars($type)."</span></td>
					<td><span contenteditable=\"true\" id=\"meterip_".$id."\">".htmlspecialchars($ip)."</span></td>
					<td><span contenteditable=\"true\" id=\"metermac_".$id."\">".htmlspecialchars($mac)."</span></td>
					<td>
						<button class=\"deletebutton btn btn-xs pull-right btn-danger\" type=\"button\" name=\"new\" value=\"meter_".$id."\">
							<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>
						</button>
					</td>
				</tr>";
		}
		?> 
			<tr class="success">
				<td><input type="text" class="form-control" pattern=".{3,200}" required title="Minimum of 3 characters" name="id" placeholder="ID of meter"></td>
				<td><input type="text" class="form-control" pattern=".{3,200}" required title="Minimum of 3 characters" name="desc" placeholder="Description of meter"></td>
				<td><input type="text" class="form-control" pattern=".{3,200}" required title="Minimum of 3 characters" name="serialnr" placeholder="Serial number of meter"></td>
				<td><input type="text" class="form-control" pattern=".{3,200}" required title="Minimum of 3 characters" name="type" placeholder="Type number of meter"></td>
				<td><input type="text" class="form-control" pattern=".{3,200}" required title="Minimum of 3 characters" name="ip" placeholder="IP address number of meter"></td>
				<td><input type="text" class="form-control" pattern=".{3,200}" required title="Minimum of 3 characters" name="mac" placeholder="MAC address number of meter"></td>
				<td><button type="submit" id="submit_button" class="form-control btn btn-success" name="new" value="meter">New Meter</button></td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="item" id="hidden_input" value="empty">
	</form>
</div>
<script src="js/save.js"></script><!-- Javascript for saving contenteditable -->
