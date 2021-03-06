var p1 = "blue";

/*
* 
* Checks if destination is a valid move for red player
*
*/
function isPlaceable(destID){
    if (destID.substring(0,2) == 'bS'){
	return true;
    }
    var num = parseInt(destID.substring(1));
    if (num >= 61 && num <= 100){
	return true;
    }
    else {
	return false;
    }
} 

function isReady(){
    for (i = 0; i < 40; i++){
	if ($.trim($("#bS" + i).html()) != ''){
	    return false;
	}
    }
    return true;
}

function addReds(){
   for (i = 1; i < 41; i++) {
       $('#M' + i).html("<img src='../img/R.png' id='R' class='square'>");
       $('#F' + i).val("R");
    }
}

function setFormLake(){
    var lakeA = [43,44,47,48,53,54,57,58];
    for (i = 0; i < lakeA.length; i++){
        $('#F' + lakeA[i]).val("X");
    }
}


$(document).ready(function(){
    addReds();
    setFormLake();
    var i = 1;//keeps track of source / destination click
    var source = "";
    var destination = "";
    var sourceID = "";
    var sourceValue = "";
    var sourceImageID = "";
    var sourceParent = "";
    document.addEventListener('click', function(e) {
	var color = e.target.id;
	color = color.substring(0,1);
	color = color.toLowerCase();

	//Checks if clickable item and is red or empty space on board
	if ($(e.target).hasClass('clickable') && color == "b" || color == "m"){
	    //Case 1: Source is empty / not a game piece 
	    if (i == 1 && $(e.target).get(0).tagName != "IMG"){
		$('#sMsg').html("Select a game piece"); 
	    }
	    //Case 2: Source is not empty / is a game piece
	    else if (i == 1){
		temp = e.target.id.split("_");
		sourceID = e.target.id;
		sourceValue = temp[0];
		sourceParent = $(e.target).parent();//
		$('#sMsg').html("Click a destination"); 
		$('#sImg').html("<img src='" + $(e.target).attr("src") + "'>"); 
		i++;
	    }
	    //Case 3: Destination is empty 
	    else if (i == 2 && $(e.target).get(0).tagName != "IMG") {
		if (isPlaceable(e.target.id)){
		    destination = e.target.id;
		    //Remove source
		    var elem = document.getElementById(sourceID);
		    elem.parentNode.removeChild(elem);
		    //Place source
		    //$('#' + destination).html(sourceImage); 
		    $('#' + destination).html("<img src='../img/"+ sourceValue +".png' id='"+ sourceValue +"_"+ destination  +"' class='clickable square'>");

		    //Change value of form element for post
		    $('#F' + destination.substring(1)).val(sourceValue);

		    //Remove source selection image / msg
		    $('#sImg').html("");
		    $("#sMsg").html("");

		    //Restart to get new source
		    i = 1;

		    //Empty source and destination
		    source = "";
		    destination = "";
		    sourceImage = "";

		    //Show ready button if all pieces are placed
		    if (isReady()){
			$('#readyButton').html("<input type='submit' value='Ready!'>"); 
		    } else {
			$('#readyButton').html("");
		    }		    
		} else {
		    $('#sMsg').html("Invalid destination"); 
		}
	    }
	}
    }, false);
});

