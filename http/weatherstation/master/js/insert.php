<?php include("../functions.php"); ?>
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad(str+" ", max) : str;
}		
function padspace(str, max) {
	return pad(str,max).replace(/ /g, "&nbsp");
}
function capFirst(string) { //capitalize first letter of string
   return string.charAt(0).toUpperCase() + string.slice(1);
}
function newdiv(file,prefix) { //create new div with prefix_file.index as id
	var object = jQuery("<div/>", { //overall div
		id: prefix+"_"+file.index,
		class: "panel panel-info",
		html: "</div>"
	});
	return object;
}
function statusdiv(file,prefix) { //status div, default: loading
	if (prefix == "file") { //file status
		var content = "<strong>"+file.name+": </strong><img src=\"img/loading.gif\"> Processing file...</div>";
	}
	else { 
		var content = "<strong>"+capFirst(prefix)+"</strong> <span class=\"glyphicon glyphicon-ok pull-right\" aria-hidden=\"true\"></span>";
	}

	var object = jQuery("<div/>", {
		id: prefix+"_status_"+file.index,
		class: "panel-heading",
		html: content
	});
	return object;
}
function bodydiv(file,prefix) {
	var object = jQuery("<div/>", { //overall div
		id: prefix+"_body_"+file.index,
		class: "panel panel-body",
		html: "</div>"
	});
	return object;
}
function errordiv(file, error,prefix) {
	var object = jQuery("<div/>", {
		id: prefix+"_error_"+file.index,
		class: "alert alert-danger",
		html: "<strong>Error:</strong> \""+error+"\".</div>"
	});
	return object;
}
function warningdiv(file, error,prefix) {
	var object = jQuery("<div/>", {
		id: prefix+"_warning_"+file.index,
		class: "alert alert-warning",
		html: "<strong>Warning:</strong> \""+error+"\".</div>"
	});
	return object;
}
function set_status(file, status,prefix) {
	var statusdiv = $("#"+prefix+"_status_"+file.index);
	var outerdiv = $("#"+prefix+"_"+file.index);
	if (status == "error") {
		if (prefix == "file") {
			statusdiv.html("<strong>"+file.name+": </strong>Error(s)");
		}
		else {
			statusdiv.html("<strong>"+capFirst(prefix)+"</strong>  <span class=\"glyphicon glyphicon-remove pull-right\" aria-hidden=\"true\"></span>");
		}
		outerdiv.removeClass("panel-info panel-warning panel-success").addClass("panel-danger"); //Change status to error(s)
	}
	else if (status == "ready") {
		if (prefix == "file") {
			statusdiv.html("<strong>"+file.name+": </strong> Ready for submit");
		}
		else {
			statusdiv.html("<strong>"+capFirst(prefix)+"</strong>  <span class=\"glyphicon glyphicon-ok pull-right\" aria-hidden=\"true\"></span>");
		}
		outerdiv.removeClass("panel-info panel-warning panel-danger").addClass("panel-success"); //Change status to ready for submit
	}
	else if(status == "warning") {
		if (prefix == "file") { 
			statusdiv.html("<strong>"+file.name+": </strong> <span class=\"glyphicon glyphicon-warning-sign pull-right\" aria-hidden=\"true\"></span>");
		}
		else {
			statusdiv.html("<strong>"+capFirst(prefix)+":</strong> Warning(s)<span class=\"glyphicon glyphicon-warning-sign pull-right\" aria-hidden=\"true\"></span>");
		}
		outerdiv.removeClass("panel-info panel-danger panel-success").addClass("panel-warning"); //Change status to ready for submit
	}
	else if (status == "done") {
		if (prefix == "file") {
			statusdiv.html("<strong>"+file.name+": </strong> Measurements in database<span class=\"glyphicon glyphicon-ok pull-right\" aria-hidden=\"true\"></span>");
		}
		else {
			statusdiv.html("<strong>"+capFirst(prefix)+"</strong>  <span class=\"glyphicon glyphicon-ok pull-right\" aria-hidden=\"true\"></span>");
		}
		outerdiv.removeClass("panel-info panel-warning panel-danger").addClass("panel-success"); //Change status to done 
	}

}
function update_status(file,response) {
	meterBodyDiv = $("#meter_body_"+file.index); //Select body div of meter div
	timeBodyDiv = $("#time_body_"+file.index); //Select body of time div
	meterBodyDiv.empty(); //Clear all elements from the meterdiv
	timeBodyDiv.empty(); //Clear all elements from the timediv
	set_status(file,"ready","meter"); //reset meter status
	set_status(file,"ready", "time"); //reset time status
	if ('error' in response) {
		set_status(file,"error","file");
		if(response.error.meter) { //if there is a meter error
			set_status(file,"error","meter"); //set meter div status to error
			meterBodyDiv.append(errordiv(file,response.error.meter,"meter")); //add error to meter div
		}
	}
	else if('warning' in response) {
		set_status(file, "warning","file");
		if(response.warning.time) { //if there is a time warning
			set_status(file,"warning","time"); //set time div status to warning
			timeBodyDiv.append(warningdiv(file,response.warning.time,"time"));
		}
	}
	else {
		set_status(file,"ready","file");
	}
	
	//add list of all meters
	listhtml = "<select class=\"form-control meter-select\" id=\"meter_select_"+file.index+"\">";
	var meterSelected = false;
	if ('meters' in response) {
		$.each(response.meters, function(index, value) { //for each meter in the database
			listhtml = listhtml + "<option value=\""+index+"\"";
			if (index == response.meter_id) {
				listhtml = listhtml + " selected=\"selected\" style=\"background-color: #DFF0D8;\"";
				meterSelected = true;
			}
			listhtml = listhtml + ">"+padspace(index,20)+": "+padspace(value.desc,100)+" -- serial number:"+value.serialnr+"</option>";
		});
	}
	listhtml = listhtml + "<option value=\"new\" id=\"new_meter_option_"+file.index+"\">Insert New Meter</option></select>";
	meterBodyDiv.append(listhtml);
	
	//add form to insert new meter
	formhtml = "<form class=\"form-inline\" id=\"new_meter_"+file.index+"\" style=\"display: none;\">"+
					"<div class=\"form-group\">"+
						"<label for=\"new_id_"+file.index+"\">ID</label>"+
						"<input type=\"text\" class=\"form-control\" id=\"new_id_"+file.index+"\" maxlength=\"20\">"+
					"</div>"+
					"<div class=\"form-group\">"+
						"<label for=\"new_desc_"+file.index+"\" class=\"form-control\">Description</label>"+
						"<input type=\"text\" class=\"form-control\" id=\"new_desc_"+file.index+"\" maxlength=\"100\">"+
					"</div>"+
					"<div class=\"form-group\">"+
						"<label for=\"new_sr_"+file.index+"\" class=\"form-control\">Serial nr</label>"+
						"<input type=\"text\" class=\"form-contrl\" id=\"new_sr_"+file.index+"\" maxlength=\"50\" value=\""+response.meter_sr+"\">"+
					"</div>"+
					"<button type=\"button\" id=\"insert_meter_"+file.index+"\" class=\"btn btn-danger\" disabled>Insert</button>"+
				"</form>";
	meterBodyDiv.append(formhtml);
	//show form if new/insert meter is selected
	$("#meter_select_"+file.index).on("change", function() {
		if (this.value == "new") { //new meter insert selected
			$("#insert_values_"+file.index).prop("disabled", true); //disable button
			$("#new_meter_"+file.index).show(); //show insert new meter form
		}
		else { //something else selected (different meter)
			$("#new_meter_"+file.index).hide(); //hide insert new meter form
			change_date(file,response.session); //update div with these parameters
		}
	});

	if(!meterSelected) { //no meter selected on forehand
		$("#new_meter_option_"+file.index).attr("selected", "selected");
		$("#new_meter_"+file.index).show();
	}
	
	$("#new_id_"+file.index+", #new_desc_"+file.index+",#new_sr_"+file.index).keyup(function(){ //change status of insert button according to length of fields
		if($("#new_id_"+file.index).val().length > 2 && $("#new_desc_"+file.index).val().length > 2 && $("#new_sr_"+file.index).val().length > 2) {
			$("#insert_meter_"+file.index).prop("disabled", false).addClass("btn-success").removeClass("btn-danger");
		}
		else {
			$("#insert_meter_"+file.index).prop("disabled", true).addClass("btn-danger").addClass("btn-success");
		}
	});
	
	$("#insert_meter_"+file.index).click(function() { //if insert meter is clicked
		$.post("update_status.php", { //ajax call
			new_id : $("#new_id_"+file.index).val(),
			new_desc : $("#new_desc_"+file.index).val(),
			new_sr : $("#new_sr_"+file.index).val(),
			session: response.session	
		},
		function(data, status) { //on return
			if (status != "success") { //something not ok happened
				alert("Data: " + data + "\nStatus: "+status);
			}
			else { //ajax requst was allright
				if(!data.success) { //return also no error
					alert("Error: "+data.error);
				}
				else { //time to change everything
					//set_status(file,"ready","meter");
					update_status(file,data);
				}
			}
		});
	});

	//setup Timediv:
	var start = new Date(response.start*1000);
	var end = new Date(response.end*1000);

	//add form to change time
	timeForm = "<form class=\"form-inline\">"+
					"<div class=\"form-group\">"+
						"<label for=\"new_time_"+file.index+"\" class=\"col-md-3 control-label\">Start time: </label>"+
						"<div class=\"col-md-5\"><input type=\"text\" class=\"form-control\" id=\"new_time_"+file.index+"\"></div>"+
						"<div class=\"input-group col-md-4\" id=\"clockpicker_"+file.index+"\">"+
							"<input type=\"text\" class=\"form-control\" id=\"new_hour_"+file.index+"\" value=\""+start.toTimeString().substr(0,5)+"\">"+
							"<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-time\"></span></span>"+
						"</div>"+
					"</div>"+
					"<div class=\"form-group\">"+
						"<label for=\"end_time_"+file.index+"\" class=\"col-md-3 control-label\">End time: </label>"+
						"<div class=\"col-md-5\"><input type=\"text\" class=\"form-control hasDatePicker\" id=\"end_time_"+file.index+"\" value=\""+end.getDate()+"-"+(end.getMonth()+1)+"-"+end.getFullYear()+"\" disabled></div>"+
						"<div class=\"input-group col-md-4\">"+
							"<input type=\"text\" class=\"form-control\" value=\""+end.toTimeString().substr(0,5)+"\" disabled>"+
							"<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-time\"></span></span>"+
						"</div>"+
					"</div>"+
				"</form>";
	timeBodyDiv.append(timeForm); //add html of time form
	$("#new_time_"+file.index).datepicker({ // add datepicker widget
		defaultDate: start,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'dd-mm-yy',
		onSelect: function(dateText, inst) {
			change_date(file, response.session); //change date, update div
		}
	});
	$("#new_time_"+file.index).datepicker("setDate", start); //set start date as default

	$("#clockpicker_"+file.index).clockpicker({ //add clockpicker widget
		placement: 'top',
		align: 'left',
		donetext: 'Done',
		autoclose: true,
		default: start.getHours()+':'+start.getMinutes(),
		afterDone: function() {
			change_date(file, response.session); //change date, update div
		}
	});
	
	if (response.double_count > 0) {
		set_status(file,"warning", "time"); //set status to warning
		//add some html to decide what to do with it
		doubleAction = "<br><select class=\"form-control\" id=\"double_action_"+file.index+"\">"+
				"<option value=\"null\" selected=\"selected\">"+response.double_count+" values detected already in database, select what to do with them</option>"+
				"<option value=\"overwrite\">Overwrite values in database with these new values</option>"+
				"<option value=\"keep\">Keep values in database and ignore newer values</option>"+
			"</select>";
		timeBodyDiv.append(doubleAction);
		$("#double_action_"+file.index).change(function() {
			if(this.value == "null") {
				$("#insert_values_"+file.index).prop("disabled", true); //disable button
			}
			else {
				$("#insert_values_"+file.index).prop("disabled", false); //enable button
			}
		});
	}

	timeBodyDiv.append("<br><button type=\"button\" id=\"insert_values_"+file.index+"\" class=\"btn btn-success\">Insert measurements in database</button>"+
			"<button type=\"button\" id=\"reset_"+file.index+"\" class=\"btn btn-default pull-right\">Reset</button>");
	if(response.double_count > 0 || !meterSelected) {
		$("#insert_values_"+file.index).prop("disabled", true); //disable button
	}
	$("#insert_values_"+file.index).click(function(){
		$("#insert_values_"+file.index).prop("disabled", true);
		$("#file_status_"+file.index).append("<img src=\"img/loading.gif\"> Inserting measurements");
		change_date(file,response.session,'insert'); //insert this session into database
	});
	$("#reset_"+file.index).click(function(){
		change_date(file,response.session,'reset'); //insert this session into database
	});
}
function change_date(file,session,ins) {
	do_ac = $("#double_action_"+file.index).val() 
	do_ac = typeof do_ac !== 'undefined' ? do_ac: 'keep'; //default = keep, otherwise something else
	ins = typeof ins !== 'undefined' ? ins : 'no'; //default = false, otherwise insert new meter
	var hour = $("#new_hour_"+file.index).val(); //hours in hh:mm format
	var day = $("#new_time_"+file.index).datepicker("getDate"); //date object from calendar picker
	var minutes = hour.substr(3,4); //get minutes
	var hours = hour.substr(0,2); //get hours
	day.setHours(parseInt(hours));
	day.setMinutes(parseInt(minutes));
	$.post("update_status.php", { //ajax call
		other_meter: $("#meter_select_"+file.index).val(),
		session: session,
		new_start: day.getTime()/1000,
		insert: ins,
		double_action: do_ac
	},
	function(data, status) { //on return
		if (status != "success") { //something not ok happened
			alert("Data: " + data + "\nStatus: "+status);
		}
		else { //ajax requst was allright
			if(!data.success) { //return also no error
				alert("Error: "+data.error);
			}
			else { //time to change everything
				//set_status(file,"ready","meter");
				if(ins != "insert") {
					update_status(file,data);
				}
				else {//successfull insert
					//$("#file_body_"+file.index).empty();//clear everything from file body
					$("#meter_"+file.index).remove(); //remove meterdiv
					$("#time_"+file.index).remove(); //remove timediv
					set_status(file,"done","file");
					start = new Date(data.start*1000);
					finishedhtml = "<br><span>Inserted all measurements in database, create new Project or run:</span><br>"+
						"<form action=\"index.php?page=projruns\" method=\"post\" target=\"_blank\" class=\"form-inline row\" id=\"projform_"+file.index+"\">"+
							"<div class=\"form-group col-xs-3\">"+
								"<label for=\"projname_"+file.index+"\" class=\"sr-only\">Project Name:&nbsp;&nbsp;</label>"+
								"<input type=\"text\" class=\"form-control\" id=\"projname_"+file.index+"\" placeholder=\"Project Name\" name=\"name\" value=\""+file.name+"\">"+
							"</div>"+
							"<div class=\"form-group col-xs-3\">"+
								"<label for=\"new_runtime_"+file.index+"\" class=\"sr-only\">&nbsp;&nbsp;Start time: &nbsp;&nbsp;</label>"+
								"<input type=\"text\" name=\"date\" class=\"form-control\" id=\"new_runtime_"+file.index+"\" placeholder=\"Project Date\"></div>"+
							"</div>"+
							"<div class=\"form-group col-xs-3\">"+
								"<label for=\"projdesc_"+file.index+"\" class=\"sr-only\">&nbsp;&nbsp;Description: &nbsp;&nbsp;</label>"+
								"<input type=\"text\" name=\"desc\" class=\"form-control\" id=\"projdesc_"+file.index+"\" placeholder=\"Project Description\" value=\"Project Description\"></div>"+
							"</div>"+
							"<input type=\"hidden\" value=\"project\" name=\"new\">"+
							"<div class=\"form-group col-xs-2\">"+
								"<button type=\"submit\" name=\"new\" class=\"btn btn-success\" value=\"project\">New Project</button>"+
							"</div>"+
						"</form><br>"+
						"<form action=\"index.php?page=projruns\" method=\"post\" target=\"_blank\" class=\"form-inline row\" id=\"runform_"+file.index+"\">"+
							"<div class=\"form-group col-xs-3\">"+
								"<label for=\"runname_"+file.index+"\" class=\"sr-only\">Run Name:&nbsp;&nbsp;</label>"+
								"<input type=\"text\" class=\"form-control\" id=\"runname_"+file.index+"\" name=\"name\" value=\""+file.name+"\">"+
							"</div>"+
							"<div class=\"form-group col-xs-3\">"+
								"<label for=\"runproj_"+file.index+"\" class=\"sr-only\">&nbsp;&nbsp;Project:&nbsp;&nbsp;</label>"+
								"<input type=\"text\" class=\"form-control\" id=\"runproj_"+file.index+"\" name=\"name\" placeholder=\"Type to select project\">"+
							"</div>"+
							"<div class=\"form-group col-xs-3\">"+
								"<label for=\"rundesc_"+file.index+"\" class=\"sr-only\">&nbsp;&nbsp;Project:&nbsp;&nbsp;</label>"+
								"<input type=\"text\" class=\"form-control\" id=\"rundesc_"+file.index+"\" name=\"desc\" placeholder=\"Description of run\">"+
							"</div>"+
							"<input type=\"hidden\" value=\"run\" name=\"new\">"+
							"<input type=\"hidden\" value=\"new_id\" name=\"item\" id=\"runitem_"+file.index+"\">"+
							"<input type=\"hidden\" value=\""+data.start+"\" name=\"new_start\" id=\"runstart_"+file.index+"\">"+
							"<input type=\"hidden\" value=\""+data.end+"\" name=\"new_end\" id=\"runend_"+file.index+"\">"+
							"<input type=\"hidden\" value=\""+data.meter+"\" name=\"newmeter_\" id=\"runmeter_"+file.index+"\">"+
							"<input type=\"hidden\" value=\"0\" name=\"name_\" id=\"runname_"+file.index+"\">"+
							"<div class=\"form-group col-xs-2\">"+
								"<button type=\"submit\" name=\"new\" class=\"btn btn-success\" id=\"submit_run_"+file.index+"\" value=\"run\" disabled>New Run</button>"+
							"</div>"+
						"</form>";
					$("#file_body_"+file.index).append(finishedhtml);
					$("#new_runtime_"+file.index).datepicker({ // add datepicker widget
						defaultDate: start,
						dateFormat: 'dd-mm-yy',
					});
					$("#new_runtime_"+file.index).datepicker("setDate", start); //set start date as default
					$("#projform_"+file.index).submit(function(e) {//if submitted, disable form
						e.preventDefault();
						$.ajax({
							   type: "POST",
							   url: "index.php?page=projruns",
							   data: $("#projform_"+file.index).serialize(), // serializes the form's elements.
							   success: function(data) {
								 	$("#projform_"+file.index).remove();
							   }
						});
					});
					$("#runform_"+file.index).submit(function(e) {//if submitted, disable form
						e.preventDefault(e);
						$.ajax({
							   type: "POST",
							   url: "index.php?page=projruns",
							   data: $("#runform_"+file.index).serialize(), // serializes the form's elements.
							   success: function(data) {
								 $("#projform_"+file.index).remove();
								 $("#runform_"+file.index).remove();
  							 }
						});
					});
					$("#runproj_"+file.index).autocomplete({
						source: "projects.php",
						select: function(event, ui) {
							$("#submit_run_"+file.index).attr("disabled", false);
							$("#runitem_"+file.index).val("run_"+ui.item.id); //set id as value
							$("#runname_"+file.index).attr("name","name_"+ui.item.id); //set name as name_id value for post processing in new.php
							$("#rundesc_"+file.index).attr("name","desc_"+ui.item.id); //set name as desc_id value for post processing in new.php
							$("#runmeter_"+file.index).attr("name","newmeter_"+ui.item.id); //set newmeter as newmeter_id value for same reason as above
						}	
					});
				}
			}
		}
	});
}

$(document).ready(function() {
	$("#upload_target").upload({ //What to do when file(s) entered to upload
		action: "upload.php",
		maxSize: <?php echo return_bytes(ini_get("upload_max_filesize")); ?> //maximum size of upload size according to php 
	}).on("filecomplete",function(event,file,response){ //When a file is complete, do this
		if('error' in response && typeof(response.error) == "string") {
				$("#file_body_"+file.index).append(errordiv(file,response.error),"file");
		}
		else {
			//Add list of all matched parameters
			var phtml = "<span><strong>Parameters Found: </strong>";
			$.each(response.parameterlist, function(index, value) { //List parameters with tooltip description
				phtml = phtml + "<a href=\"#\" data-toggle=\"tooltip\" title=\""+value+"\">"+index+"</a>  ";
			});
			phtml = phtml + "<br></span><span><strong>Number of measurements: </strong>"+response.count+"</span>"; 
			$("#file_body_"+file.index).append(phtml); //Add parameter html to meter div
			$(function () {	$('[data-toggle="tooltip"]').tooltip()	}); //enable tooltips
			
			//add delete button
			$("#file_body_"+file.index).prepend("<button type=\"button\" class=\"btn btn-danger pull-right\" id=\"delete_"+file.index+"\">"+
					"<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button>");
			$("#delete_"+file.index).click(function(){
				$("#file_"+file.index).remove();
			});

			//Add meter div and status
			var meterDiv = newdiv(file,"meter"); //Get meterdiv for file
			$("#file_body_"+file.index).append(meterDiv); //append to general file div
			meterDiv.append(statusdiv(file, "meter")); //add meter status
			var meterBodyDiv = bodydiv(file, "meter");
			meterDiv.append(meterBodyDiv);

			//add time div and status
			var timeDiv = newdiv(file,"time"); //Get meterdiv for file
			$("#file_body_"+file.index).append(timeDiv); //append to general file div
			timeDiv.append(statusdiv(file, "time")); //add meter status
			var timeBodyDiv = bodydiv(file, "time");
			timeDiv.append(timeBodyDiv);


			update_status(file,response);
		}
	}).on("fileerror",function(event,file,error){ //if there is a file error:
		if($("#file_"+file.index).length == 0) {
			alert("Error: "+error);
		}
		else {
			set_status(file,"error","file");
			$("#file_"+file.index).append(errordiv(file,error)); //Add error div
		}
	}).on("filestart",function(event,file){ //When file download is started
		window.addEventListener("beforeunload", function(event) {//make reminder if leaving page
			event.returnValue = "Are you sure?";
		});
		var fileDiv = newdiv(file,"file"); //get filediv
		$("#status").append(fileDiv); //append filediv to status
		fileDiv.append(statusdiv(file, "file")); //status/heading of div
		fileDiv.append(bodydiv(file,"file")); //body of div
	});	
});

