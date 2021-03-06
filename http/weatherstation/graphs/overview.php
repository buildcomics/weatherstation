<?php
	$mysqli = new mysqli($GLOBALS["mysql_server"],$GLOBALS["mysql_user"],$GLOBALS["mysql_pass"],$GLOBALS["mysql_db"]) or die("mysql connect failure: ".$this->mysqli->connect_error);
	$meters_query = "SELECT `id`,`name`,`unit`,`symbol`,`last_update` FROM `meters` WHERE `stored`=1";
	$stmt = $mysqli->prepare($meters_query) or die("prepare meters_query query error: ".$mysqli->error); //prepare statement
	$stmt->execute() or die("execute meters_query query error: ".$mysqli->error);
	$stmt->bind_result($id, $name,$unit,$symbol,$last_update);
?>	
<div class="container">
	<div class="page-header">
		<h1>Overview</h1>
	</div>
		<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<td>Name</td>
				<td>Unit</td>
				<td>last_update</td>
			</tr>
		</thead>
		<tbody>
			<?php
				while ($stmt->fetch()) {
					$last_update_html = date("d-m-Y H:i:s",$last_update);
			?>
			<tr>
				<td><?php echo $name; ?></td>
				<td><?php echo $unit."(".$symbol.")"; ?></td>
				<td><?php echo $last_update_html; ?></td>
				<td><a href="index.php?page=graph&id=<?php echo $id."&start=".($last_update-60*60*24*7)."&end=".$last_update; ?>">Graph!</a></td>
			</tr>
			<?php
					}
				$stmt->close();
				$mysqli->close();
				$title_html = htmlspecialchars($name);
			?>	
	</tbody>	
	</table>
</div>

