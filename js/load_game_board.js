var gameArray = [];

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
    /* commented out for testing purposes
    for (i = 0; i < 40; i++){
	if ($.trim($("#rS" + i).html()) != ''){
	    return false;
	}
    }
    */
    return true;
}

/* Not used
function setVals() {
    for (i = 1; i < 41; i++) {
	var temp = $('#M' + i + ":first-child").attr("id");
	$("#F" + i).val(temp);
    }
}
*/

function addBlues(){
   for (i = 61; i < 101; i++) {
       $('#M' + i).html("<img src='../img/B.png' id='B' class='square'>");
       //Change value of form element for post           
       $('#F' + i).val("B");

    }
}

function getName(){
    var res = $('#user_name').html();
    return res;
}

function getState(){
    var res = $('#state_num').html();
    return res;
}

//Returns R or B
function getPlayerColor(){
    var res = $('#player_color').html();
    return res.substring(0,1);
}

function gameArrayInit(){
    gameArray.push("Error: Don't use 0");//to start index at 1
    for (i = 1; i < 101; i++){
	gameArray.push($('#T' + i).html());
	$('#T' + i).remove();	
    }
}

function fillBoard(color){
    for (i = 1; i < 101; i++){
	if (gameArray[i] == 'N'){
	    continue;
	} else if (color == 'R'){
	    if (gameArray[i].charAt(0) == "B"){
		$('#M'+i).html("<img src='../img/Bback.png' id='blue_back' class='blue_piece clickable'>");
	    } else {
		$('#M'+i).html("<img src='../img/" +gameArray[i]+ ".png' id='" +gameArray[i]+ "' class='blue_piece clickable'> ");
	    }
	} else if (color == 'B'){
	    if (gameArray[i].charAt(0) == "R"){
		$('#M'+i).html("<img src='../img/Rback.png' id='red_back' class='red_piece clickable'>");
	    } else {
		$('#M'+i).html("<img src='../img/" +gameArray[i]+ ".png' id='" +gameArray[i]+ "' class='red_piece clickable'> ");
		$('#M'+i).html("<img src=../img/" +gameArray[i]+ ".png>");
	    }

	}
    }
}

function isClickable(target, playerColor, pieceColor, click){
    if ($(target).hasClass('clickable')){
	//Case 1: source click
	if (click == 1){
	    if (playerColor == pieceColor){
		return true;
	    }
	} else {
	    

	}

	//click == 1 is the source click
	if (playerColor == pieceColor && click == 1){

	}
    }
    return false;
}

$(document).ready(function(){
    var playerColor = getPlayerColor();
    var state = getState();
    if (state == 3){
	//Clear game initialization data
	$("#bPool").remove();
	$("#rLine").remove();
	$("#bLine").remove();
	addBlues();
    }
    gameArrayInit();
    fillBoard(playerColor);    
    var i = 1;//keeps track of source / destination click
    var source = "";
    var destination = "";
    var sourceID = "";
    var sourceValue = "";
    var sourceImageID = "";
    var sourceParent = "";
    document.addEventListener('click', function(e) {
	console.log(e.target.id.substring(0,1));
	var pieceColor = e.target.id.substring(0,1);
	//isClickable(color)
	//Checks if clickable item and is red or empty space on board
	if ($(e.target).hasClass('clickable')){
	    //Case 1: Source is empty / not a game piece 
	    if (i == 1 && $(e.target).get(0).tagName != "IMG"){
		$('#sMsg').html("Select a game piece"); 
	    }
	    //Case 2: Source is not empty / is a game piece
	    else if (i == 1){
		temp = e.target.id.split("l");
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
		    $('#' + destination).html("<img src='../img/"+ sourceValue +".png' id='"+ sourceValue +"l"+ destination  +"' class='clickable square'>");

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

