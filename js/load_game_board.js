var p1 = "blue"
var remaining = 40;

function isPlaceable(player, destID){
    if (destID.substring(0,1) == 'S'){
	return true;
    }
    var num = parseInt(destID.substring(1));
    if (player == "red"){
	if (num >= 1 && num <= 40){
	    //alert("valid red move");
	    return true;
	}
	else {
	    //alert("invalid move");
	    return false;
	}
    }
    else{
	if (num >= 61 && num <= 100){
	    //alert("valid blue move");
	    return true;
	}
	else {
	    //alert("invalid move");
	    return false;
	}
    }
} 

function isReady(){
    for (i = 0; i < 40; i++){
	console.log("#S" + i);
	if ($.trim($("#S" + i).html()) != ''){
	    //$('#mbox').html("Not all pieces have been placed");
	    return false;
	}
    }
    return true;
}

$(document).ready(function(){
    var i = 1;
    var source = "";
    var destination = "";
    var sourceImage = "";
    var sourceImageID = "";
    document.addEventListener('click', function(e) {
	if ($(e.target).hasClass('clickable')){
	    //case 1: Source is empty 
	    if (i == 1 && $(e.target).get(0).tagName != "IMG"){
		$('#mbox').html("Select a game piece"); 
	    }

	    //case 2: Source is not empty
	    else if (i == 1){
		source = e.target.id;
		sourceImage = e.target.outerHTML;
		$('#mbox').html("Click a destination"); 
		$('#selected').html("<img src='" + $(e.target).attr("src") + "'>"); 
		i++;

	    }

	    //case 3: Destination is empty
	    else if (i == 2 && $(e.target).get(0).tagName != "IMG") {
		if (isPlaceable(p1, e.target.id)){
		    destination = e.target.id;

		    //remove source
		    var elem = document.getElementById(source);
		    elem.parentNode.removeChild(elem);

		    //place source
		    $('#' + destination).html(sourceImage); 

		    //change value of form element for post
		    $('#F' + destination.substring(1)).val(source);
		    console.log(destination);

		    //remove source selection image
		    $('#selected').html("");
		    $("#mbox").html("");
		    //console.log("Destination =" + e.target.id + " id "+ source  + " sourceImage= " + sourceImage);

		    //restart to get new source
		    i = 1;

		    //empty source and destination -- necessary?
		    source = "";
		    destination = "";
		    sourceImage = "";

		    if (isReady()){
			$('#readyButton').html("<button onclick=''>Ready!</button>"); 
		    } else {
			$('#readyButton').html("");
		    }
		    
		} else {
		    $('#mbox').html("Invalid destination"); 
		}

	    }

	}
    }, false);
});

