/* projruns.js javascript file for projruns.php to handle all events
 * Written by Tom van den Berg for Event-Engineers
 */
function get_time(hourid, dayid) { //function to get full date object from date and clockpicker
	var hour = $(hourid).val(); //hours in hh:mm format
	var day = $(dayid).datepicker("getDate"); //date object from calendar picker
	var minutes = hour.substr(3,4); //get minutes
	var hours = hour.substr(0,2); //get hours
	day.setHours(parseInt(hours));
	day.setMinutes(parseInt(minutes));
	return day;
}
function change_date(item) { //When date is changed, this function is used to store it via ajax
	ar = item.split("_");
	item = ar[0];
	id = ar[1];
	if(item.indexOf("new") != 0) { //when not a new run
		 if (item == "runstarttime" || item == "runstartdate") {
			hourid = "#runstarttime_"+id;
			dayid = "#runstartdate_"+id;
			item = "runstart_"+id;
		}
		else {
			hourid = "#runendtime_"+id;
			dayid = "#runenddate_"+id;
			item = "runend_"+id;
		}
		day = get_time(hourid,dayid);
		save(item,day.getTime()/1000);
	}
}
$(document).ready(function() {//When everything is ready loading
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	  });
  $("#projrunstable").treeFy({ //enable expandable table
		expanderExpandedClass: 'glyphicon glyphicon-chevron-up',
		expanderCollapsedClass: 'glyphicon glyphicon-chevron-down',
		expanderTemplate: "<span class=\"treetable-expander\" aria-hidden=\"true\"></span>",
		indentTemplate: '',
		//treeColumn: 0,
		initState: 'collapsed'
	});
	$(".datepicker").datepicker({ //enable datepicker
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		onClose: function(dateText, ins) { //when datepicker is closed
			$(this).parent().find(".date").html(dateText).blur(); //copy date into .date class element
			$(this).parent().find(".date").addClass("danger");
			if($(this).parent().attr("id").indexOf("projdate") == 0) { //Only if it is not in the new project form
				save($(this).parent().attr("id"),$(this).datepicker("getDate").getTime()/1000);
			}
			else {
				change_date($(this).attr("id"));
			}
		}
	});
	$(".clockpicker").clockpicker({ //enable clockpicker
		autoclose: true,
		placement: "top",
		afterDone: function(picker) {
			picker.input.addClass("danger");
			change_date(picker.input.attr("id")); //call change_date
		}
	});
	$(".date").click(function(){ //when .date element is clicked
		$(this).parent().find('.datepicker').datepicker("show"); //show datepicker
		$(this).parent().addClass("danger"); //add red background
	});
	$(".meterselect").change(function() { //when a meter select is changed:
		save($(this).attr("id"),this.value);
	});
	$(".savebutton").click(function() { //shen save button is clicked to insert new run
		ar = $(this).attr("id").split("_"); //get item and id (item is not used)
		id = ar[1];
		//var [item, id] = $(this).attr("id").split("_"); //get item and id (item is not used)
		start = get_time("#newstarttime_"+id,"#newstartdate_"+id);
		end = get_time("#newendtime_"+id,"#newenddate_"+id);
		$("#form").append(hidden_element("new_start", start.getTime()/1000)); //add new_start
		$("#form").append(hidden_element("new_end", end.getTime()/1000)); //add new_end value
		$("#form").append(hidden_element("new", "run")); //add delete to form
		$("#hidden_input").val(this.value);
		$("#form").submit(); 
	});
});
