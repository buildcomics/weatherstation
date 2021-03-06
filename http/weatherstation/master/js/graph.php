var g;
$(document).ready(function() {//When everything is ready loading
	g = new Dygraph(document.getElementById("graph"),"csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]."&timeavg=".$_GET["timeavg"]; ?>",{
		legend: 'always',
		title: '<?php echo $title_html; ?>',
		ylabel: '<?php echo $xlabel; ?>',
		xlabel: 'Time',
		drawAxisAtZero: true,
		includeZero: true,
		colors: ["#1CE6FF", "#FF34FF", "#FF4A46", "#008941", "#006FA6", "#A30059",
        "#FFDBE5", "#7A4900", "#0000A6", "#63FFAC", "#B79762", "#004D43", "#8FB0FF", "#997D87",
        "#5A0007", "#809693", "#FEFFE6", "#1B4400", "#4FC601", "#3B5DFF", "#4A3B53", "#FF2F80",
        "#61615A", "#BA0900", "#6B7900", "#00C2A0", "#FFAA92", "#FF90C9", "#B903AA", "#D16100",
        "#DDEFFF", "#000035", "#7B4F4B", "#A1C299", "#300018", "#0AA6D8", "#013349", "#00846F",
        "#372101", "#FFB500", "#C2FFED", "#A079BF", "#CC0744", "#C0B9B2", "#C2FF99", "#001E09",
        "#00489C", "#6F0062", "#0CBD66", "#EEC3FF", "#456D75", "#B77B68", "#7A87A1", "#788D66",
        "#885578", "#FAD09F", "#FF8A9A", "#D157A0", "#BEC459", "#456648", "#0086ED", "#886F4C",
        "#34362D", "#B4A8BD", "#00A6AA", "#452C2C", "#636375", "#A3C8C9", "#FF913F", "#938A81",
        "#575329", "#00FECF", "#B05B6F", "#8CD0FF", "#3B9700", "#04F757", "#C8A1A1", "#1E6E00",
        "#7900D7", "#A77500", "#6367A9", "#A05837", "#6B002C", "#772600", "#D790FF", "#9B9700",
        "#549E79", "#FFF69F", "#201625", "#72418F", "#BC23FF", "#99ADC0", "#3A2465", "#922329",
        "#5B4534", "#FDE8DC", "#404E55", "#0089A3", "#CB7E98", "#A4E804", "#324E72", "#6A3A4C",
        "#83AB58", "#001C1E", "#D1F7CE", "#004B28", "#C8D0F6", "#A3A489", "#806C66", "#222800",
        "#BF5650", "#E83000", "#66796D", "#DA007C", "#FF1A59", "#8ADBB4", "#1E0200", "#5B4E51",
        "#C895C5", "#320033", "#FF6832", "#66E1D3", "#CFCDAC", "#D0AC94", "#7ED379", "#012C58"],
		dateWindow: [Date.parse("<?php echo date("r",$start); ?>"), Date.parse("<?php echo date("r",$end); ?>")],
		zoomCallback: function(minX,maxX,yRanges) { //when graph is zoomed
			updateAggregates(Math.round(minX/1000),Math.round(maxX/1000));
			updateDownload();
		}

	});
	g.ready(function() { //when graph is ready
		updateDownload();
		xrange = g.xAxisRange();
		updateAggregates(Math.round(xrange[0]/1000), Math.round(xrange[1]/1000));
	});
	$("#submit").click( function() { //when submit button is clicked
		var params = $("#params").val().join(",");
		window.location.href="index.php?page=graph&run=<?php echo @$_GET["run"];?>&params="+params+"&timeavg="+$("#timeavg_input").val();
	});
	$("#points_checkbox").change(function() {//when points checkbox is changed
		if($(this).is(":checked")) {//if it is checked
			g.updateOptions({ //set options of graph
				drawPoints: true
			});
		}
		else {
			g.updateOptions({ //set options of graph
				drawPoints: false
			});
		}
		updateDownload();
	});
	$("#minmax_checkbox").change(function() {//when minmax chexbox is changed
		if($(this).is(":checked")) {//if it is checked
			g.updateOptions({ //change options of graph
				customBars: true,
				showRoller: true,
				rollPeriod: 10,
				file: "csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>&timeavg="+$("#timeavg_input").val() //change source of graph
			});
			$("#csv_button").attr("href", "csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>&timeavg="+$("#timeavg_input").val()); //change url of download data button
		}
		else {
			g.updateOptions({ //change graph options
				customBars: false,
				showRoller: false,
				file: "csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>&error=false&timeavg="+$("#timeavg_input").val() //change source of graph to csv without errors
			});
			$("#csv_button").attr("href", "csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>&error=false&timeavg="+$("#timeavg_input").val()); //change href of download csv button
		}
		setTimeout(function(){updateDownload();},500);
	});
	$("#filled_checkbox").change(function() { //if filled checkbox is changed
		if($(this).is(":checked")) { //change graph options
			g.updateOptions({
				fillGraph: true
			});
		}
		else {
			g.updateOptions({ //change graph options
				fillGraph: false
			});
		}
		updateDownload();
	});
	$("#timeavg_input").change(function() {//when minmax chexbox is changed
		g.updateOptions({ //change options of graph
			file: "csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>&timeavg="+$(this).val() //change source of graph
		});
		$("#csv_button").attr("href", "csv.php?run=<?php echo $_GET["run"]."&params=".@$_GET["params"]; ?>&error="+$("#minmax_checkbox").is(":checked")+"&timeavg="+$(this).val()); //change href of download csv button
		setTimeout(function(){updateDownload();},500);
	});
	$("#timeavg_input").val("<?php echo $_GET["timeavg"]; ?>"); //set default value for average
	$("#maxy_checkbox").change(function() { //if filled checkbox is changed
		if($(this).is(":checked")) { //change graph options
			g.updateOptions({
				valueRange: [null, $("#maxy_input").val()]
			});
		}
		else {
			g.updateOptions({ //change graph options
				valueRange: [null,null]
			});
		}
		updateDownload();
	});
	$("#maxy_input").change(function() { //if filled checkbox is changed
		if($("#maxy_checkbox").is(":checked")) { //change graph options
			g.updateOptions({
				valueRange: [null, $("#maxy_input").val()]
			});
		}
		updateDownload();
	});
	$("#yzero_checkbox").change(function() { //if filled checkbox is changed
		if($(this).is(":checked")) { //change graph options
			g.updateOptions({
				includeZero: true
			});
		}
		else {
			g.updateOptions({ //change graph options
				includeZero: false
			});
		}
		updateDownload();
	});
	$("#yzero_checkbox").prop("checked", true); //check start y at zero
});
function updateDownload() {
	var img = document.getElementById('demoimg');
	Dygraph.Export.asPNG(g,img);
	$("#download_button").attr("href", $("#demoimg").attr("src"));
	$("#download_button").attr("download", "<?php echo $projname."_".$name; ?>.png");
}
function updateAggregates(start, end) {
	$.post("get_aggregates.php", { //ajax call
		meter: '<?php echo htmlspecialchars($meter); ?>',
		start: start,
		end: end
	},
	function(data, status) { //on return
		if (status != "success") { //something not ok happened
			alert("Data: " + data + "\nStatus: "+status);
		}
		else { //ajax requst was allright
			if(!data.success) { //return also no error
				alert("Error: "+data.error);
			}
			else { //time to put data in table
				delete data.success; //This data is not going into the table
				$.each(data, function(key, value) {
					if(value != null) {
						$("#"+key).html(Math.round(value));
					}
					else {
						$("#"+key).html("xxx");
					}
				});
			}
		}
	}).fail(function(x,y,error) {
		alert("Update Aggregates Error:"+error);
	});

}
