/* save.js to save contenteditable items over ajax
 * written by Tom van den Berg for Event-Engineers
 */
function hidden_element(name, value) { //function to add new hidden element to form
	return "<input type=\"hidden\" name=\""+name+"\" value=\""+value+"\">";
}
function save(item,data) { //save stuff over ajax
	$.post("save.php", { //ajax call
		item: item,
		data: data
	},
	function(data, status) { //on return
		if (status != "success") { //something not ok happened
			alert("Data: " + data + "\nStatus: "+status);
		}
		else { //ajax requst was allright
			if(!data.success) { //return also no error
				alert("Error: "+data.error);
			}
			else { //time to revert background
				$("#"+item).removeClass("danger"); //back to normal background colour
			}
		}
	}).fail(function(x,y,error) {
		alert("error:"+error);
	});
}
$(document).ready(function() {//When everything is ready loading
	$('[contenteditable=true]').focus(function() { // When you click on item, 
		$(this).data("initialText", $(this).html()); //record into data("initialText") content of this item.
		$(this).addClass("danger"); //change background colour to "unstored"
	}).blur(function() {	// When you leave an item...
		if ($(this).data("initialText") !== $(this).html()) {// ...if content is different...
			save($(this).attr("id"), $(this).text());
		}
	});
	$(".deletebutton").click(function() { //when a delete button is clicked
		$("#submit_button").val("delete"); //set submit button value to delete
		$("#hidden_input").val(this.value); //set hidden value named "item"
		$("#form").append(hidden_element("new", "delete")).submit(); //submit form
	});

});

