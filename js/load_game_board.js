var gameArray = [];
var capture = false;

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

function addBlues(){
   for (i = 61; i < 101; i++) {
       $('#M' + i).html("<div id='B_"+i+"'><img src='../img/B.png' id='B_"+i+"' class='square clickable'></div>");
       //Change value of form element for post           
    }
}

function getName(){
    var res = $('#user_name').html();
    return res;
}

/*
*
*
*/
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

/*
* Uses gameArrayInit to fill the board with appropriate pieces 
* 
* img id convention [Color][Value][Location] -> ['R'/'B']['0'-'11']l['1'-'100']
* ie red flag @ top right corner would be 'R0l10'
*  back of a blue piece in bottom left corner would be Bl91
*/
function fillBoard(color){
    for (i = 1; i < 101; i++){
	if (gameArray[i] == 'N' || gameArray[i] == 'X'){ // N = empty, X = Lake
	    continue;
	} else if (color == 'R'){
	    if (gameArray[i].charAt(0) == "B"){
		$('#M'+i).html("<img src='../img/B.png' id='B_" + i + "' class='blue_piece clickable'>");
	    } else {
		$('#M'+i).html("<img src='../img/" + gameArray[i] + ".png' id='" +gameArray[i]+ "_" + i + "' class='blue_piece clickable'> ");
	    }
	} else if (color == 'B'){
	    if (gameArray[i].charAt(0) == "R"){
		$('#M'+i).html("<img src='../img/R.png' id='R_" + i + "' class='red_piece clickable'>");
	    } else {
		$('#M'+i).html("<img src='../img/" + gameArray[i] + ".png' id='" +gameArray[i]+ "_" + i + "' class='rede_piece clickable'> ");
	    }
	}
    }
}

/*
* Return array with either location if empty space
*  or piece value and location if occupied by piece
*  ie '45' or ['R6','12']
*/
function idParseToArray(id){
    var a = [];
    if (id.substring(0,1) == "M"){
	a.push(id.substring(1));
	return a;
    } else {
	return id.split("_");
    }
}

/*
* source/target = 
* 
*
*/
function isClickable(playerColor, pieceColor, click, target){
    //Source click
    if (click == 1){
	var splitA = target.split("_");
	var targetVal = splitA[0].substring(1);

	//Flags and bombs can't move
	if (targetVal == "0" || targetVal == "11") { 
	    return false;
	} 
	//Source can only be your own piece
	else if (playerColor == pieceColor){
	    return true;
	} else {
	    return false;
	}
    //Destination click    
    } else if (click == 2){
	//Destination cannot be your own piece
	if (playerColor == pieceColor){
	    return false;
	}
	return true;
    }
    return false;
}

//
// Source location: 
//
function isPathClear(source, dest, dir){
    // Need to include obstructions store value as X
    // in gameArray 

    var i = source;
    if (dir == "W"){
	while (i > dest){
	    i--;

	    if (i == dest){
		return true;
	    } else if (gameArray[i] != 'N') {
		return false;
	    }
	}
	return false;
    } else if (dir == "E"){
	while (i < dest){
	    i++;

	    if (i == dest){
		return true;
	    } else if (gameArray[i] != 'N') {
		return false;
	    }
	}
	return false;
    } else if (dir == "N"){
	while (i > dest){
	    i = i - 10;

	    if (i == dest){
		return true;
	    } else if (gameArray[i] != 'N') {
		return false;
	    }
	}
	return false;
    } else if (dir == "S") { // dir == 'S'
	while (i < dest){
	    i = i + 10;

	    if (i == dest){

		return true;
	    } 
	    else if (gameArray[i] != 'N') {

		return false;
	    }
	}

	return false;
    }
}

function isScoutMove(source, dest){
    // -1 because array starts at 1
    // without -> 1 / 10 = 0 | 10 / 10 = 1 
    // ie 0-9 / 10 = 0 | 90-99 / 10 = 9
    source = source - 1;
    dest = dest - 1;
    var sRow = parseInt(source / 10); 
    var dRow = parseInt(dest / 10);
    var sCol = source % 10;
    var dCol = dest % 10;
    //Horizontal
    if (sRow == dRow){
	//West
	if (dCol < sCol){
	    if(isPathClear(source+1, dest+1, "W")){
		return true;
	    } else {
		return false;
	    }
	}
	//East
	else {
	    if(isPathClear(source+1, dest+1, "E")){
		return true;
	    } else {
		return false;
	    }
	}
    }

    //Vertical 
    if (sCol == dCol){
	//North
	if (dRow < sRow){
	    if(isPathClear(source+1, dest+1, "N")){	    
		return true;
	    } else {
		return false;
	    }
	}
	//South
	else {
	    if(isPathClear(source+1, dest+1, "S")){
		return true;
	    } else {
		return false;
	    }
	}
    }
}

//No need to factor in lakes becaues they are not clickable
function isRegMove(source, dest){
    source = source - 1;
    dest = dest - 1;
    var sRow = parseInt(source / 10);
    var dRow = parseInt(dest / 10);
    var sCol = source % 10;
    var dCol = dest % 10;
    //Horizontal
    if (dRow == sRow){
	//Left
	if (dest == source - 1){
	    return true;
	} 
	//Right
	else if (dest == source + 1){
	    return true;
	}
	//Invalid
	else {
	    return false;
	}
    } 
    //Vertical
    else if (dCol == sCol){
	//Top
	if (dest == source - 10){
	    return true;
	}
	//Down
	else if (dest == source + 10) {
	    return true;
	}
	
	//Invalid
	else {
	    return false;
	}
    }
    //Invalid
    else {
	return false;
    }
}

//
//
//
function isValidMove(sourceID, destID){
    var sArray = idParseToArray(sourceID);
    var dArray = idParseToArray(destID);
    var sourceValue =  sArray[0];
    var sourceColor = sourceValue.substring(0,1);
    var sourceRank = sourceValue.substring(1);
    var sourceLocation = sArray[1];
    var destValue = null;
    var destColor = null;
    var destRank = null;
    var destLocation = "";
    if (dArray.length > 1){
	destValue = dArray[0]
	destColor = destValue.substring(0,1);
	destRank = destValue.substring(1);
	destLocation = dArray[1];
    } else {
	destLocation = dArray[0];
    }	    
    
    //Source and destination are same
    if (sourceID == destID){
	return false;
    }

    //Destination is empty
    if (destColor != sourceColor){
	//Scout move
	if (sourceRank != 2){
	    console.log("regmove");
	    if (isRegMove(sourceLocation, destLocation)){
		console.log("isregmove : " + isRegMove(sourceLocation, destLocation));
		return true;
	    } else {
		return false;
	    }
	}
	//Other move
	else {
	    if (isScoutMove(sourceLocation, destLocation)){
		return true;
	    } else {
		return false;
	    }
	}
    } else {
	return false;
    }
}

//return winning value
//return N if tied
function fight(sVal, dVal){
    //Both are the same val
    if (sVal == dVal){
	return "N";
    } 
    var sCol = sVal.substring(0,1);
    var dCol = dVal.substring(0,1);
    var sVal = sVal.substring(1);
    var dVal = dVal.substring(1);
    //Flag - 0
    if (dVal == "0"){
	capture = true;
	return sCol + sVal;
    }
    //Spy - 1
    else if (sVal == "1" || dVal == "1"){
	if (sVal == "1" ){
	    // Source Spy vs Destination Marshall
	    if (dVal == "10"){
		return sCal + sVal;
	    } else {
		return dCol + dVal;
	    }
	} else if (dVal == "1") {
	    // Destination Spy vs Souce Marshall 
	    if (sVal == "10"){
		return dCol + dVal;
	    } else {
		return sCol + sVal;
	    }
	}
    }

    //Bomb - 11
    else if (dVal == "11"){
	if (sVal == "3"){
	    return sCol + sVal;
	} else {
	    return dCol + dVal;
	}
    }

    //Regular
    else {
	//Source is greater and wins
	if (sVal > dVal){
	    return sCol + sVal;
	//Dest is greater and wins
	} else if (dVal > sVal){
	    return dCol + dVal;
	//Tie: both lose
	} else {
	    return "N";
	}
    }
}

function confirmOrReset(){
    $('#readyButton').html("<button type='button' onclick='confirm('yes')'>Confirm Move</button><button type='button' onclick='confirm('no')'>Reset</button>"); 
    //Change mbox message
    //Change button to confirm
}

function toForm(sLoc,dLoc,sVal,dVal,rVal){
    var gArray = gameArray.toString();
    console.log(gArray);
    $("#sLoc").val(sLoc);
    $("#dLoc").val(dLoc);
    $("#sVal").val(sVal);
    $("#dVal").val(dVal);
    $("#rVal").val(rVal);
    $("#gameArray").val(gArray);
}

function confirm(){
    var s = $('#sourceLocation').html();
    var d = $('#destLocation').html();
    
    var sTemp = s;
    
    var sVal = gameArray[s];
    var dVal = gameArray[d];
    //If dest is empty
    console.log("dVal = "+dVal);
    if (dVal == "N"){
	gameArray[d] = sVal;
	gameArray[s] = "N";
	toForm(s,d,sVal,dVal,sVal);
    } else {
	var res = fight(sVal,dVal);                                                                                                                   
	gameArray[s] = "N";
	gameArray[d] = res;
	toForm(s,d,sVal,dVal,res);
    }
    console.log("confirm() s = " + s + " d " + d + " sVal " + sVal + " dVal " + dVal);    
}

$(document).ready(function(){
    var playerColor = getPlayerColor();
    var state = getState();
    var i = 1;//keeps track of source(1)/destination(2) click
    var sourceID = "";
    var sourceValue = "";
    if (state == 3){
	//Clear game initialization data
	$("#bPool").remove();
	$("#rLine").remove();
	$("#bLine").remove();
	addBlues();
    }
    gameArrayInit();
    fillBoard(playerColor);    
    document.addEventListener('click', function(e) {

	var pieceColor = e.target.id.substring(0,1);
	/* Checks if clickable item */ 
	if ($(e.target).hasClass('clickable') && isClickable(playerColor, pieceColor, i, e.target.id)){
		$('.destSelected').css({"-webkit-filter" : "brightness(1)"});
		$('.destSelected').removeClass('destSelected');
	    if (i == 1){
		//set target as source
		sourceID = e.target.id;
		var tempArray = sourceID.split("_");
		sourceValue = tempArray[0];
		sourceLoc = tempArray[1];
		$('#sMsg').html("Click a destination");
                $('#sImg').html("<img src='" + $(e.target).attr("src") + "'>");
		i++;
	    } else if (i == 2){
		i = 1;
		var destID = e.target.id;
		var destLoc = "";
		var temp = idParseToArray(destID);
		console.log(destID);
		if (temp.length > 1){
		    destLoc = temp[1];
		} else {
		    destLoc = temp[0];
		}
		if (isValidMove(sourceID, destID)){
		    console.log("s" + sourceID + "d" + destID)
		    $("#sMsg").html("Confirm move?<br><span id='sourceLocation'>"
				     + sourceLoc + "</span> -> <span id='destLocation'>"+destLoc+"</span>");
		    $("#readyForm").submit(function(){
			confirm(sourceID,destID);
		    });
		    $('#readyButton').html("<button>Confirm Move</button><button type='button' onclick='location.reload()'>Reset</button>"); 
		    $('#' + destID).addClass('destSelected');
		    $('.destSelected').css({"-webkit-filter" : "brightness(.3)"});
		} else {

		    //error
		    $('#sMsg').html("Invalid destination"); 
                    $('#sImg').html();
		    //Restart to get new source
		    //i = 1;
                    sourceID = "";
		    sourceValue = "";                                                                                                 
                    destID = "";
		    sourceImage = "";
		}
	    }
	}
    }, false);
});
/*
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
		    //source = "";
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

*/


		    /*
		    confirm = confirmMove(sourceID, destID);
		    var elem = document.getElementById(sourceID);
		    elem.parentNode.removeChild(elem);
		    //Place source
		    $('#' + destID).html("<img src='../img/"+ sourceValue +".png' id='"+ sourceValue +"l"+ destID  +"' class='clickable square'>");

		    //Change value of form element for post
		    $('#F' + destID.substring(1)).val(sourceValue);

		    //Remove source selection image / msg
		    $('#sImg').html("");
		    $("#sMsg").html("");


		    //Empty source and destination
		    sourceID = "";
		    sourceValue = "";
		    destID = "";
		    sourceImage = "";

		    //Show ready button if all pieces are placed
		    if (isReady()){
			$('#readyButton').html("<input type='submit' value='Ready!'>"); 
		    } else {
			$('#readyButton').html("");
		    }
*/
