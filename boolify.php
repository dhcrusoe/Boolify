<?php

$defined = '$defined';
echo <<<STRICT
<script src="http://www.google.com/jsapi?key=ABQIAAAA4JIh6t6WqZ6bu2qtSfaUHRT_MH5dYcXsMMhjPNkaLjfPMzh-axSMnb84GYT7DC4z1i5TuBUrsL4K-g" type="text/javascript"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="boolifyie6.css">
<link rel="stylesheet" type="text/css" href="gifimages.css" />
<style type="text/css">
	img, div { behavior: url(iepngfix.php) }	
</style> 
<![endif]-->
<script type="text/javascript" src="mootools-yui-1.11.js"></script>
<script type="text/javascript">
    //<![CDATA[
    google.load("search", "1", {"language" : 
STRICT;
if(isset($_GET['currentLang']) && strlen($_GET['currentLang']) == 2){
	echo '"'.$_GET['currentLang'].'"';
	$langArray = array('en'=>0, 'es'=>1, 'sv'=>2, 'pt'=>3);
	$startingLanguage = $langArray[$_GET['currentLang']];
}else{
	echo '"en"';
	$startingLanguage = 0;
}
echo <<<STRICT
});
    google.load("language", "1");
       
    //]]>
</script>
<script type="text/javascript">
STRICT;
echo '
var currentLang = '.$startingLanguage.';
';
echo <<<STRICT
var holder = 0;
var count = 0;
var type = 'web';
var typeee = 'web';
var safeSearch = 'strict'; // 'strict', 'moderate'
var notline = 0;
var board_coords = 0;
var board_holder_coords = 0;
var searchstr = "";
var has_results = false;
var replace_flag = false;	// default flag, if user wants to replace, it's true, 
var replace_id = 0;		// default id, if user dblclick, it equals to the id of the current piece/button 
var pieceType;
var curentlastspos= 0;

// Shows the overlay and starts the ESCAPE event listener
function showOverlay()
{
	alert('overlay shown');
	$('overlay').setStyle('visibility','visible');
}

// Hides the overlay and stops the ESCAPE event listener
function hideOverlay()
{
	$('overlay').setStyle('visibility','hidden');
}

function insert_text()
{
	var nv = $('newvalue').value;
	var newValueText;
	
	if (nv == '')
		return;
	if (nv.length > 75)
	{

		alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'tooManyCharsErr', false).'")';
echo <<<STRICT
);
		return;
	}
	
	if (nv.length <= 4)
	{
		var spaces = (4 - nv.length);
		var spc = '';
		for (var x = spaces+3; x > 0; x--)
		{
			spc += '&#160;';		
		}
		newValueText = nv + spc;
	}
	else
		newValueText = nv;
	
	$(count + '').innerHTML = '<span>' + newValueText + '</span>';
	
	for (var x = count-1; x>0; x--)
	{
		if ($(x+'').innerHTML == $(count +'').innerHTML)
		{
			alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'sameKeywordsErr', false).'")';
echo <<<STRICT
);
			remove_last_piece();
			update_results(0);
			break;
		}
	}
	
	boardTooLongWith($(count+''));
		
	closeDialogue(false);
}

// ======= new function added @ Jul 7, 2009 =======
// replace_text: to replace text by double-click any green buttons on board
//    p_id: input variable, the num id of the target green button (dblclick)
//
function replace_text(p_id)
{
	var nv = $('newvalue').value;
	if (nv == '')
		return;
	if (nv.length > 75)
	{
		alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'tooManyCharsErr', false).'")';
echo <<<STRICT
);
		return;
	}

	// preserve the old text on the button
	var old_nv = $(p_id + '').innerHTML;

	if (nv.length < 4)
	{
		var spaces = (4 - nv.length);
		var spc = '';
		for (var x = spaces+4; x > 0; x--)
		{
			spc += '&#160;';		
		}
		$(p_id + '').innerHTML = '<span>' + nv + spc + '</span>';
	}
	else
		$(p_id + '').innerHTML = '<span>' + nv + '</span>';

	// search out if there're the same keywords around the board
	// if no, continue
	// if yes, omit the new input one, and restore to the former text
	//
	for (var x = count; x>0; x--)
	{
		if (x == p_id)
			continue;
		else {
			if ($(x + '').innerHTML == $(p_id + '').innerHTML)
			{
				alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'sameKeywordsErr', false).'")';
echo <<<STRICT
);
 				$(p_id + '').innerHTML = old_nv;
				break;
			}
		}
	}

	// restore flag and id to default value
	replace_flag = false;
	replace_id = 0;	
	closeDialogue(false);
}

// Closes the dialogue box, resets it and hides the overlay
function closeDialogue(escaped)
{
	// Clear dialogue
	// annoying workaround because i was tired of seeing the cursor in the middle of the page
	$('newvalue').value = '';
	holder = $('newvalue').clone();
	holder.addEvent('keyup',function(event)
	{
		// user hits enter
		if (event.which == 13 || event.keyCode == 13)
		{
			if (replace_flag)		// if user wants to replace
				replace_text(replace_id);
			else				// if user wants to enter new keywords
				insert_text()
		}	
		// user hits escape
		if (event.which == 27 || event.keyCode == 27)
		{
			cancel_entry();
		}
	});	
	$('newvalue').remove();

	$('overlay').setStyle('visibility','hidden');

	// Hide dialogue
	$('dialogue').setStyle('visibility','hidden');
	
	if (escaped)
		remove_last_piece();
	else
		update_results(0);
}

function loadPopup()
{
	update_results(1);
	
	if (holder)
		holder.injectBefore('addentry');

	$('overlay').setStyle('visibility','visible');

	// Show dialogue and focus on newvalue
	$('dialogue').setStyle('visibility','visible');
	switch(pieceType) {
		case "blankWord":
			$('dialogueHeader').innerHTML = 
STRICT;
echo 'translate("'.outputLanguage($xml, 'keyword', false).'")';
echo <<<STRICT
;
			break;
		case "urlWord":
			$('dialogueHeader').innerHTML = 
STRICT;
echo 'translate("'.outputLanguage($xml, 'url', false).'")';
echo <<<STRICT
;
			break;
	}
	$('newvalue').focus();
}

function loadCustomPopup(popupID) {

	// dimic: while opening "Search Option" dialog call two methods below to initialize current selections for "type" and "safe search"
	if( 'typesBox' == popupID )
	{
		updateSearchTypeSelection( type );
		updateSearchSafeSelection( safeSearch );
	}// dimic: end
	

	$('overlay').setStyle('visibility','visible');
	$(popupID).setStyle('visibility','visible');
	get_searches();
}

function unloadCustomPopup(popupID) {
	$('overlay').setStyle('visibility','hidden');
	$(popupID).setStyle('visibility','hidden');
}



// end borrowed code

function attach_piece(item, tilesNotDefined)
{
	var newpiece = 0;

	if (item.getProperty('id') == "blankWord" || item.getProperty('id') == "urlWord")						
	{
		newpiece = new Element('div');			
		newpiece.innerHTML = '<span><pre style="margin-top: 0px">    </pre></span>';			
	}
	else
	{
		newpiece = item.clone();	
	}
	newpiece.setProperty('class',item.getProperty('id'));	
	
	newPieceClass = newpiece.getProperty('class');
	
	if($defined($((count)+'')))
		lastPieceClass = $((count)+'').getProperty('class');
	else
		lastPieceClass = false;

	if(newPieceClass == 'notWord' || newPieceClass == 'andWord' || newPieceClass == 'orWord') {
		if(!$((count) + '')) {
			$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'onlyGrnErr', false).'")';
echo <<<STRICT
);
			return;
		} else if(newPieceClass == 'notWord' && lastPieceClass != 'notAndWord' && lastPieceClass != 'blankWord' && lastPieceClass != 'urlWord') {
			$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'onlyGrnOrPrplErr', false).'")';
echo <<<STRICT
);
			return;
		}  else if(lastPieceClass != 'blankWord' && lastPieceClass != 'urlWord' && newPieceClass != 'notWord') {
			$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'onlyGrnErr', false).'")';
echo <<<STRICT
);
			return;
		} else if(lastPieceClass == 'urlWord' && newPieceClass == 'andWord') {
			$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'onlyGrnErr', false).'")';
echo <<<STRICT
);
			return;
		}
	} else if(newPieceClass == 'urlWord') {
		if(lastPieceClass == 'notWord' || lastPieceClass == 'andWord') {
			$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'noUrlToNotAndErr', false).'")';
echo <<<STRICT
);
			return;
		}
		if(lastPieceClass == 'orWord' && $defined($((count-1)+'')) && $((count-1)+'').getProperty('class') == 'blankWord') {
			$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'noUrlToOrBlankErr', false).'")';
echo <<<STRICT
);
			return;
		}
	}
	
	if (newPieceClass == "notWord") {
		if(lastPieceClass != "notAndWord") {
			attach_piece($('notAndWord'), tilesNotDefined);
			if(!boardTooLongWith($(count+''))) {
				attach_piece($('notWord'), tilesNotDefined);
				if(!boardTooLongWith($(count+'')) && tilesNotDefined) {
					attach_piece($('blankWord'), tilesNotDefined);
					loadPopup();
				}
			}
			return;
		}
	}
	
	if (lastPieceClass == 'blankWord' && newPieceClass == 'blankWord') {
		attach_piece($('andWord'), tilesNotDefined);
		return;
	}
	
	if(lastPieceClass == 'urlWord' && newPieceClass == 'urlWord') {
		attach_piece($('orWord'), tilesNotDefined);
		if(!boardTooLongWith($(count+'')) && tilesNotDefined) {
			attach_piece($('urlWord'), tilesNotDefined);
			loadPopup();
		}
		return;
	}

	if (newPieceClass == "notAndWord") {
		newpiece.setStyle('visibility','visible');
	}
	
	if (newPieceClass == 'andWord')  {
		newpiece.setProperty('id',count); // ie7 fix (don't ask)
	}
	
	if (newPieceClass == 'orWord' && notline) {
		$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'notLineErr', false).'")';
echo <<<STRICT
);
		return;
	}

	var trash = $('trash');
	
	newpiece.setProperty('id',++count);
	newpiece.inject($('board'));

	newpiece.addEvent('mousedown', function(e)
	{
		e = new Event(e).stop();

		var clone = this.clone()
			.setStyles(this.getCoordinates()) // this returns an object with left/top/bottom/right, so its perfect
			.setStyles({'opacity': 0.7, 'position': 'absolute', 'margin': '0'})
			.setProperty('id', newpiece.getProperty('id')+"trashclone")
			.addEvent('emptydrop', function()
			{
				trash.removeEvents();
				this.remove();
			}).inject(document.body);
						
			var drag = clone.makeDraggable(
			{
				droppables: [trash]
			}); // this returns the dragged element
		
		trash.addEvents({
			'drop':function() {
				trash.removeEvents();
				rebuildBoard(returnNewStringExcept(clone));
				clone.remove();
			}
		});
		
		drag.start(e); // start the event manual
	});

	// ======= new event added @ Jul 7, 2009 =======
	// the double click event
	newpiece.addEvent('dblclick', function(e)
	{
		e = new Event(e).stop();	// exception

		if (this.getProperty('class') != "blankWord")
			return;	// if the dblclick target is the operators, just return
		else
		{
			replace_flag = true;		// prepare to replace the text, flag is true
			replace_id = this.getProperty('id');	// get the button id
			loadPopup();		// pop up the dialog for user to input
			return;
		}
	});
	
	pieceType = newPieceClass;	
		
	if (!$((count-1)+'')) // skip all the rest if this is the first piece
	{
		newpiece.setStyles({marginLeft:"20px"});
		if(tilesNotDefined)
			loadPopup();
		return;
	}

	var lastpiece = $((count-1)+''); 					// obj
			
	// time to move the pieces around
	if (newPieceClass == "andWord" || lastPieceClass == "andWord") // attach below
	{
		var top = 0;
		var left = 0;
		
		notline = 0;
		top = lastpiece.getCoordinates()['top'] + lastpiece.getCoordinates()['height'] - 12;
		left = lastpiece.getCoordinates()['left'];
		newpiece.setStyle('position','absolute');
		newpiece.setStyle('left',left);				
		newpiece.setStyle('top',top);		
				
	}
	else // attach to the right side
	{
		var left = lastpiece.getCoordinates()['right'] - 10;
		var top = 0;
		
		if (lastPieceClass == 'notAndWord')
			left -= 2;
		else if(lastPieceClass == 'blankWord') {
			left += 22;
		} else if(lastPieceClass == 'urlWord') {
			left += 21;
		}
			
		if (newPieceClass == 'blankWord' || newPieceClass == 'urlWord')
			left -= 2;
		

		// grab top of last green piece
		if (lastPieceClass == 'blankWord' || lastPieceClass == 'urlWord'){
			top = lastpiece.getCoordinates()['top'];
		} else
			top = $((count-2)+'').getCoordinates()['top'];
					
		newpiece.setStyle('top',top);
		newpiece.setStyle('position','absolute');
		newpiece.setStyle('left',left);				
	}
	

	
	if (newPieceClass != 'blankWord')
		update_results(0);
	if(newPieceClass == 'blankWord' || newPieceClass == 'urlWord') {
		if(!boardTooLongWith(newpiece) && tilesNotDefined) {
			loadPopup();
		}
	} else if(newPieceClass == 'andWord' && !boardTooLongWith($(count+''))) {
		attach_piece($('blankWord'), tilesNotDefined);
		if(!boardTooLongWith($(count+'')) && tilesNotDefined) {
			loadPopup();
		}
	} else if(newPieceClass == 'orWord' && !boardTooLongWith($(count+'')) && tilesNotDefined) {
		// if/else added by dmoree on 1-14-2010
		if(lastPieceClass != 'urlWord'){
			attach_piece($('blankWord'), tilesNotDefined);
			if(!boardTooLongWith($(count+'')) && tilesNotDefined) {
				loadPopup();
			}
		}
	} else if(newPieceClass == 'urlWord') {
		boardTooLongWith(newpiece);
	}
}

function boardTooLongWith(newpiece) {
	if(newpiece.getCoordinates()['right'] > $('board').getCoordinates()['right']) {
		remove_last_piece();
		update_results(false);
		alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'tooLongHorizErr', false).'")';
echo <<<STRICT
);
		return true;
	}
	return false;
}

function switch_respage(whatpage)
{
whatpage=parseInt(whatpage);
if(whatpage==1)
{
curentlastspos=0;
update_results(0);
}
else if(whatpage==2)
{
curentlastspos=5;
update_results(0);
}
else if(whatpage==3)
{
curentlastspos=10;
update_results(0);
}
else if(whatpage==4)
{
curentlastspos=15;
update_results(0);
}
else if(whatpage==5)
{
curentlastspos=20;
update_results(0);
}
else if(whatpage==6)
{
curentlastspos=25;
update_results(0);
}
else if(whatpage==7)
{
curentlastspos=30;
update_results(0);
}
else if(whatpage==8)
{
curentlastspos=35;
update_results(0);
}
else 
{
alert('unknown page');
curentlastspos=0;
update_results(0);
}
var t=setTimeout(function(){change_pcolor(whatpage)},1000);;
}

function change_pcolor(whatpage)
{
whatpage=parseInt(whatpage);
var j;
var tempid="plk"+whatpage;
//alert('pageid '+tempid);
document.getElementById(tempid).className='paglinksA';
}

function update_results(board_only)
{
	searchstr = "";
	var curr_bottom = 0;
	var curr_right = 0;
	var curr_board_coords = $('board').getCoordinates();
	
	for (var x = 1; x <= count; x++)
	{
		var getclass = $(x + '').getProperty('class');
		var coords = $(x+'').getCoordinates();
		curr_bottom = coords['top'] + coords['height'];
		//curr_right = coords['left'] + coords['width'];
		if (getclass == "notWord")
		{
			notline = 1;
			searchstr += " -";
		}
		if (getclass == "orWord")
			searchstr += " OR ";
		if (getclass == "andWord")
		{
			notline = 0;			
			searchstr += " ";
		}
		if (getclass == "urlWord")
			searchstr += " site:";
		if (getclass == "blankWord" || getclass == "urlWord")
		{
			getcontents = $(x + '').innerHTML;
			getcontents = getcontents.substring(6,getcontents.length - 7);
			getcontents = getcontents.replace(/&nbsp;/g,"");
			if (getcontents.search(' ') != -1)
				getcontents = '\"' + getcontents + '\"';
			searchstr += getcontents;
			if(getclass == "urlWord")
				searchstr+= " ";
		}
	}
	var curr_board_bottom = curr_board_coords['top'] + curr_board_coords['height'];	
	//var curr_board_right = curr_board_coords['left'] + curr_board_coords['width'];
	
	if (curr_board_bottom < curr_bottom)
	{
		$('board').setStyle('height',(10+curr_bottom-board_coords['top']));
	}
	else if (curr_board_bottom > curr_bottom && curr_bottom < (board_coords['height'] + board_coords['top']))
	{
		$('board').setStyle('height',board_coords['height']);
	}
	
	/*if(curr_board_right < curr_right) {
		$('board').setStyle('width',(10+curr_right-board_coords['left']));
	} else if(curr_board_right > curr_right && curr_right < (board_coords['width'] + board_coords['left'])) {
		$('board').setStyle('width',board_coords['width']);
	}*/
	
	if (board_only)
		return;

	has_results = 0;
	
	var searchControl = new google.search.SearchControl();


	// dimic: below se create appropriate Google search services and setup specific parameters
	if (type == 'web')
	{
		var gsearch = new google.search.WebSearch();

		if( 'strict' == safeSearch )
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_STRICT );
		}
		else
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_MODERATE );
		}
	}
	else if (type == 'news')
	{
		var gsearch = new google.search.NewsSearch();

		if( 'strict' == safeSearch )
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_STRICT );
		}
		else
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_MODERATE );
		}
	}
	else if (type == 'book')
	{
		var gsearch = new google.search.BookSearch();

		if( 'strict' == safeSearch )
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_STRICT );
		}
		else
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_MODERATE );
		}
	}
	else if (type == 'blog')
	{
		var gsearch = new google.search.BlogSearch();
		// dimic: 'blog' doesn't have setRestriction() method
	}
	else if (type == 'video')
	{
		var gsearch = new google.search.VideoSearch();
		// dimic: 'video' doesn't have setRestriction() method
	}
	else
	{
		var gsearch = new google.search.ImageSearch();

		if( 'strict' == safeSearch )
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_STRICT );
		}
		else
		{
			gsearch.setRestriction( google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_MODERATE );
		}
	} // dimic: end
    	
	options = new GsearcherOptions(); 
	options.setExpandMode(GSearchControl.EXPAND_MODE_OPEN);	
	Search();
	searchControl.addSearcher(gsearch,options);
    
    // Tell the searcher to draw itself and tell it where to attach
    searchControl.draw(document.getElementById("searchControl"));
   
    searchControl.setSearchCompleteCallback(searchControl,grabResults);
    //searchControl.execute(searchstr);
		
	$$('.gsc-search-box').setStyle('visibility','hidden');	


	if (searchstr)
	{
		//GSearch.getBranding($('branding'));
		$('msg').setText("Boolean: " + searchstr);
      $('msg2').setText("Your Search: " + searchstr);
	}
	else
	{
		$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'startMsg', false).'")';
echo <<<STRICT
);
		$('branding').innerHTML = '';
		$('resultstr').setText('');
	}
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function grabResults(x,y)
{
	if (y.cursor && y.cursor.pages)
	{
		$('resultstr').setText(type.capitalize() + ": " + addCommas(y.cursor.estimatedResultCount) + " estimated results.");
		has_results = y.cursor.estimatedResultCount;
	}
	if (!has_results)
		$('resultstr').setText(type.capitalize() + ": "+
STRICT;
echo 'translate("'.outputLanguage($xml, 'noResults', false).'")';
echo <<<STRICT
);
}

//Returns an array with the piece id as the index and the 

function returnNewStringExcept(theClone) {
	var cloneID = theClone.getProperty('id');
	var trashnum = parseInt(cloneID);
	var searchStr = "";
	var getContents;
	var getClass;
	for(var i=0;i<=count;i++) {
		if($defined($(i+''))) {
			getClass = $(i + '').getProperty('class');
			if(i != trashnum) {
				if (getClass == "notWord") {
					searchStr += " -";
				}
				if (getClass == "orWord")
					searchStr += " OR ";
				if (getClass == "andWord") {	
					searchStr += " ";
				}
				if (getClass == "urlWord")
					searchStr += " site:";
				if (getClass == "blankWord" || getClass == "urlWord")
				{
					getContents = $(i + '').innerHTML;
					getContents = getContents.substring(6,getContents.length - 7);
					getContents = getContents.replace(/&nbsp;/g,"");
					if (getContents.search(' ') != -1)
						getContents = '\"' + getContents + '\"';
					searchStr += getContents;
					if(getClass == "urlWord")
						searchStr+= " ";
				}

				$(i + '').remove();
			} else if(getClass == 'notAndWord') {
					$(trashnum+'').remove();
					trashnum++;
			} else if(getClass == 'notWord' || getClass == 'orWord' || getClass == 'andWord') {
					$(trashnum+'').remove();
					trashnum++;
			}
		}
	}
	//alert(searchstr);
	if($defined($(trashnum+''))) {
		$(trashnum+'').remove();
	}
	$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'startMsg', false).'")';
echo <<<STRICT
);
	$('resultstr').setText('');
	count = 0;
	notline = 0;
	return searchStr;
}

function rebuildBoard(stringToBuild) {

// dimic:
//
// fix to: In some browsers (Safari, for example), removing a keyword piece from below a keyword piece (green pieces with multiple words) results in a condition in which keywords all break apart into individual pieces"
//
// the problem was that during board rebuild, we divided words by spaces and didn't take into account the quotes.
// we should take into account quotes
// so let's do this.....
// the idea:
// 1. divide words by space
// 2. find and split words between quotes

	// devide by spaces first...
	var tempArr = stringToBuild.split(" ");

	var amount = 0;
	var strArr = [];
	var bInsideTheQuotes = 0;	// 0 - no, 1 - yes

	// split some words
	var i = 0;
	var phraseInTheQuotes = "";
	for( i=0; i<tempArr.length; i++ )
	{
		//
		if( 0 == bInsideTheQuotes )
		{
			//
			if( "\"" == tempArr[i].charAt(0) )	// met "opening" quotes
			{
				bInsideTheQuotes	= 1;
				phraseInTheQuotes	= tempArr[i].substr( 1, tempArr[i].length-1 ); // add first word without quotes
			}
			else
			{
				strArr[ amount++ ]	= tempArr[i];	// just usual simple word
			}
		}
		else
		{
			//
			if( "\"" == tempArr[i].charAt(tempArr[i].length-1) )	// met "closing" quotes
			{
				bInsideTheQuotes	= 0;

				phraseInTheQuotes	+= " ";
				phraseInTheQuotes	+= tempArr[i].substr( 0, tempArr[i].length-1 ); // add last word without quotes

				//
				strArr[ amount++ ]	= phraseInTheQuotes; 	// as we here met the last word in the phrase - store whole phrase in the array
				phraseInTheQuotes	= "";
			}
			else
			{
				phraseInTheQuotes	+= " ";
				phraseInTheQuotes	+= tempArr[i];	// add word (not first, not last)
			}
		}
	}

	//var strArr = stringToBuild.split(" ");
// dimic: end

	var newPiece;
	var updateText = false;
	strArr.each(function(item) {
		switch(item) {
			case "OR":
				newPiece = new Element("div", {
					'id' : 'orWord',
					'class' : 'puzzle'
				});
				newPiece.innerHTML = 'Or';
				attach_piece(newPiece, false);
				break;
			case "":
				break;
			default:
				if(item.test("^\-")) {
					newPiece = new Element("div", {
						'id' : 'notWord',
						'class' : 'puzzle'
					});
					newPiece.innerHTML = 'Not';
					attach_piece(newPiece, false);
					item = item.substr(1);
				}
				if(item != "") {
					if(item.test("^site:")) {
						newPiece = new Element("div", {
							'id' : 'urlWord',
							'class' : 'puzzle'
						});
						item = item.substr(5);
						updateText = true;
					} else {
						newPiece = new Element("div", {
							'id' : 'blankWord',
							'class' : 'puzzle'
						});
						updateText = true;
					}
					attach_piece(newPiece, false);
					if(updateText) {
						updateText = false;
						if (item.length < 4) {
							var spaces = (4 - item.length);
							var spc = '';
							for (var x = spaces+4; x > 0; x--) {
								spc += '&#160;';		
							}
							$(count + '').innerHTML = '<span>' + item + spc + '</span>';
						} else
							$(count + '').innerHTML = '<span>' + item + '</span>';
					}
				}
				break;
		}
	});
	if(count > 0)
		update_results(0);
	else
		$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'startMsg', false).'")';
echo <<<STRICT
);
}

function remove_last_piece()
{	
	if ($defined($(count+'')))
	{
		var getclass = $(count + '').getProperty('class');
		if (getclass == 'notword')
			notline = 0;
	}
	$(count + '').remove();
	count--;	
}

function start_over() {
	while(count > 0) {
		remove_last_piece();
	}
	notline = false;
	update_results(0);
	document.getElementById('msg2').innerHTML="";
}

function cancel_entry()
{
	$('newvalue').value = '';
	closeDialogue(true);
}

var searches = new Hash.Cookie('searches', {autosave:true});	
	window.addEvent('domready', function()
	{			
		var drop = $('board');
		var dropFx = drop.effect('background-color', {wait: false}); // wait is needed so that to toggle the effect,		
		board_coords = $('board').getCoordinates();
		
						
		$('close').addEvent('click',function(e)
		{
			cancel_entry();
		});

		$('closeSearches').addEvent('click',function(e)
		{
			unloadCustomPopup('searchesBox');
		});
		
		$('addentry').addEvent('click',function(e)
		{
			if (replace_flag)
				replace_text(replace_id);
			else
				insert_text()
		});
		
		$('viewSearch').addEvent('click',function(e)
		{
			loadCustomPopup('searchesBox');		
		});
		
		$('saveSearch').addEvent('click',function(e)
		{
			if (searchstr == '')
			{
				alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'noSearchToSaveErr', false).'")';
echo <<<STRICT
);
				return;
			}			
			if ($('resultstr').getText() == '')
			{
				alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'completeSearchErr', false).'")';
echo <<<STRICT
);
				return;
			}
			if (!has_results)
			{
				alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'noResultsToSaveErr', false).'")';
echo <<<STRICT
);
				return;
			}

			if (searchstr != '')
			{
				var searchcount = searches.get('count');
				if (searches.get('count') == false)
					searchcount = 1;
				else
					searchcount++;
				searches.set('count',searchcount);
				searches.set('boolean' + searchcount,searchstr);
				searches.set('type' + searchcount,type);
				searches.set('results' + searchcount,$('resultstr').getText());
				var showcount = 0;
				if (searchcount >= 10)
					showcount = 10;
				else 
					showcount = searchcount;
				searches.save();
				alert(
STRICT;
echo 'translate("'.outputLanguage($xml, 'searchSaved', false).'")';
echo <<<STRICT
);
			}
		});		
		
		$('removeLastPiece').addEvent('click',function(e)
		{
			if (count > 0) {
				remove_last_piece();
				if($defined($(count+'')) && $(count+'').getProperty('class') == 'notAndWord') {
					remove_last_piece();
				}
				if(count > 0)
					update_results(0);
				else
					$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'startMsg', false).'")';
echo <<<STRICT
);
			}
		});
		
		$('startOver').addEvent('click', function(e) {
			start_over();
		});

		$('newvalue').addEvent('keyup',function(event)
		{
			// user hits the enter key
			if (event.which == 13 || event.keyCode == 13)
			{
			if (replace_flag)
				replace_text(replace_id);
			else
				insert_text()
			}
				
			// user hits the escape key
			if (event.which == 27 || event.keyCode == 27)
			{
				cancel_entry();
			}
		});
		
		
		$('web').addEvent('click',function(e)
		{

			updateSearchTypeSelection( 'web' ); // dimic: update styles in order to show correct selection

			type = 'web';
			typeee = 'Web';
/*			$('picked').setHTML(
STRICT;
echo 'translate("'.outputLanguage($xml, 'webSearch', false).'")';
echo <<<STRICT
);*/
		});

            $('web').addEvent('dblclick',function(e)
           {
            unloadCustomPopup('typesBox');
            });
            


		$('news').addEvent('click',function(e)
		{
			updateSearchTypeSelection( 'news' ); // dimic: update styles in order to show correct selection

			type = 'news';
/*			$('picked').setHTML(
STRICT;
echo 'translate("'.outputLanguage($xml, 'newsSearch', false).'")';
echo <<<STRICT
);*/
		});

            $('news').addEvent('dblclick',function(e)
		{
            unloadCustomPopup('typesBox');
            });            



           


// dimic: -------------- video --
		$('video').addEvent('click',function(e)
		{
			updateSearchTypeSelection( 'video' ); // dimic: update styles in order to show correct selection

			type = 'video';
		});

            $('video').addEvent('dblclick',function(e)
		{
            unloadCustomPopup('typesBox');
            });            
// ----



		$('image').addEvent('click',function(e)
		{
			updateSearchTypeSelection( 'image' ); // dimic: update styles in order to show correct selection

			type = 'image';
/*			$('picked').setHTML(
STRICT;
echo 'translate("'.outputLanguage($xml, 'pictureSearch', false).'")';
echo <<<STRICT
);*/
		});
		
            $('image').addEvent('dblclick',function(e)
		{
            unloadCustomPopup('typesBox');
            }); 
		
		$('typeButton').addEvent('click',function(e) {
			loadCustomPopup('typesBox');
		});


// dimic: --------- moderate --
		$('moderate').addEvent('click',function(e)
		{
			updateSearchSafeSelection( 'moderate' ); // dimic: update styles in order to show correct selection

			safeSearch = 'moderate';
		});

		$('moderate').addEvent('dblclick',function(e)
		{
			unloadCustomPopup('typesBox');
		});            

// dimic: --------- strict --
		$('strict').addEvent('click',function(e) 
		{
			updateSearchSafeSelection( 'strict' ); // dimic: update styles in order to show correct selection

			safeSearch = 'strict';
		});

		$('strict').addEvent('dblclick',function(e)
		{
			unloadCustomPopup('typesBox');
		});            


		
		$('closeTypes').addEvent('click',function(e) {
			unloadCustomPopup('typesBox');
			if(type == 'image') {
				$('Bluebox').setStyle('min-height', '480px');
				$('Bluebox').setStyle('height', '480px');
			} else {
				$('Bluebox').setStyle('min-height', '240px');
				$('Bluebox').setStyle('height', '240px');
			}
			update_results(0);
		});
		
		$$('.puzzle').each(function(item)
		{
			item.addEvent('mousedown', function(e)
			{
				e = new Event(e).stop();

				var clone = this.clone()
					.setStyles(this.getCoordinates()) // this returns an object with left/top/bottom/right, so its perfect
					.setStyles({'opacity': 0.7, 'position': 'absolute', 'margin': '0'})
					.addEvent('emptydrop', function()
					{
						this.remove();
						drop.removeEvents();
					}).inject(document.body);

				drop.addEvents(
				{
					'drop': function()
					{
						drop.removeEvents();
						clone.remove();
						attach_piece(item, true);

// dimic: fix: If a NOT is the first piece on the board, it causes an error state and the board darkens and doesn't return to white
//
// just do not call methods in a emtpy object (count == 0)
//
						if( 0 < count )
						{
							boardTooLongWith($(count+''));
							var itemid = item.getProperty('id');
						}
// dimic: end
						dropFx.start('7389AE').chain(dropFx.start.pass('f8f8f8', dropFx));
					},
					'over': function()
					{
						dropFx.start('98B5C1');
					},
					'leave': function()
					{
						dropFx.start('f8f8f8');
					}
				});

				var drag = clone.makeDraggable(
				{
					droppables: [drop]
				}); // this returns the dragged element

				drag.start(e); // start the event manual
			});
		
			//item.addEvent('mousedoubleclick', function(e)
			//{
			//	e = new Event(e).stop();
			//	insert_text();
			//});
			
		});	
		get_searches();
		$('clear').addEvent('click',function(e)
		{
			searches.empty();
			searches.set('count',false);
			searches.save();
			get_searches();			
		});
		
		/*$('print').addEvent('click',function(e)
		{
			frames['piframe'].location.href = "printresults.php";
		});*/

// dimic: these below aren't defined and only cause an error...
// so I made them commented		
/*
		$('americaFlag').addEvent('click',function(e) {
			changeLang(0);
		});
		$('mexicoFlag').addEvent('click',function(e) {
			changeLang(1);
		});
		$('swedenFlag').addEvent('click',function(e) {
			changeLang(2);
		});
*/
// dimic: end
	});

	function get_searches()
	{
		searches.load();
		var str = '';
		var sr = $('saved_results');
		var x = 1;
		str = '<table id=\"results_table\" cellpadding=\"3\" border=\"0\" class=\"test\"><tr class=\"row_title\"><td>'+
STRICT;
echo 'translate("'.outputLanguage($xml, 'searchesFromSession', false).'")';
echo <<<STRICT
+'</td><td>'+
STRICT;
echo 'translate("'.outputLanguage($xml, 'numberResults', false).'")';
echo <<<STRICT
+'</td></tr>';		
		if (searches.get('count') > 10)
			x = searches.get('count') - 10;
		for (; x <= searches.get('count'); x++)
		{
			str += '<tr id="result'+x+'" class="results"><td style="color: red;">' + searches.get('boolean'+x) + '</td><td>' + searches.get('results'+x) + '</td></tr>';			
			str += '<tr class="search-results" id="searchresult' + x + '"></tr>';
		}
		if (searches.get('count') == null)
			str += '<tr class="results"><td>'+
STRICT;
echo 'translate("'.outputLanguage($xml, 'none', false).'")';
echo <<<STRICT
+'</td><td>'+
STRICT;
echo 'translate("'.outputLanguage($xml, 'none', false).'")';
echo <<<STRICT
+'</td></tr>';
		str += '</table>';
		sr.setHTML(str);
		
		$$('tr.results').addEvent('click',function(e)
		{
			unloadCustomPopup('searchesBox');
			start_over();
			searchstr = '';
			//alert(this.getElementsByTagName('td').item(1).innerHTML);
			var brokeident2=this.getElementsByTagName('td').item(1).innerHTML.split(':');
	var sstype=brokeident2[0];
	if(sstype=="Web")
	{
		var sstype2="web";
	}
	else if(sstype=="News")
	{
		var sstype2="news";
	}
		else if(sstype=="Image")
	{
		var sstype2="image";
	}
		else if(sstype=="Video")
	{
		var sstype2="video";
	}
	type=sstype2;
			rebuildBoard(this.getElementsByTagName('td').item(0).innerHTML)
		});
		
	}

function translate(words) {
	var langArr = words.split("|");
	var output = langArr[currentLang];
	return output;
}

// dimic: function manages "selected" style for search SafeSearch options
function updateSearchSafeSelection( safety )
{
//alert( "set non to '" + safeSearch + "' and set CSS to '" + safety + "'" );

	// clear previous selection...
	if( document.getElementById( safeSearch ) )
	{
		var elm = document.getElementById( safeSearch );
		elm.className = 'searchSafeNotSelected';
	}

	// set style to the new item...
	if( document.getElementById( safety ) )
	{
		var elm = document.getElementById( safety );
		elm.className = 'searchSafeSelected';
	}		
}

// dimic: function manages "selected" style for search type options
function updateSearchTypeSelection( itemName )
{
//alert( "set non to " + type + " and set CSS to " + itemName + "'" );

	// clear previous selection...
	if( document.getElementById( type ) )
	{
		var elm = document.getElementById( type );
		elm.className = 'searchTypeNotSelected';
	}

	// set style to the new item...
	if( document.getElementById( itemName ) )
	{
		var elm = document.getElementById( itemName );
		elm.className = 'searchTypeSelected';
	}		
}

//translates the page
function changeLang(lang) {
	var transArr;
	$$('.translate').each(function(item) {
		transArr = item.getAttribute('id').split('|');
		item.innerHTML = transArr[lang];
	});
	currentLang = lang;
	var langLocs = Array();
	langLocs[0] = "en";
	langLocs[1] = "es";
	langLocs[2] = "sv";
	langLocs[3] = "pt";
	GUnload();
	google.load("search", "1", {"language" : langLocs[currentLang]});
	google.load("language", "1");
	$('msg').setText(
STRICT;
echo 'translate("'.outputLanguage($xml, 'startMsg', false).'")';
echo <<<STRICT
);
}
</script>
<div id="overlay"> </div>
<div id="dialogue" class="dialogue">
	<div id='close' class="close"><img src='images/clear_board_icon.gif' width=19 height=20></div>
	<h3 id="dialogueHeader" class="box">
STRICT;
echo outputLanguage($xml, 'keyword', true);
echo <<<STRICT
</h3>
	<p class="box"><input type="text" name="newvalue" id="newvalue"> <input id='addentry' type="button" value="Add"></p>
</div>
<div id="searchesBox">
	<div id='closeSearches' class="close"><img src='images/clear_board_icon.gif' width=19 height=20></div><a href="javascript:void(0);" id="clear">
STRICT;
echo outputLanguage($xml, 'clear', true);
echo <<<STRICT
</a>
    <div id="saved_results">
    </div>
</div>
<div id="typesBox">

    <div id='typesInfo' class='typesInfo'>
STRICT;
echo outputLanguage($xml, 'searchWhat', true);
echo <<<STRICT
	</div>

    <div id="typesContainer">
        
        <div id='web'>
STRICT;
echo outputLanguage($xml, 'webSearch', true);
echo <<<STRICT
        </div>
        
        
        <div id='news'>
STRICT;
echo outputLanguage($xml, 'newsSearch', true);
echo <<<STRICT

        </div>


        <div id='video'>
STRICT;
echo outputLanguage($xml, 'videoSearch', true);
echo <<<STRICT

        </div>

        <div id='image'>
STRICT;
echo outputLanguage($xml, 'pictureSearch', true);
echo <<<STRICT

        </div>
        
<!--        <br/> -->
<!-- dimic: we don't need this 'indicator' anymore
        <div id='picked'>
STRICT;
echo outputLanguage($xml, 'webSearch', true);
echo <<<STRICT
		</div>
-->

	</div>

	<br/>

	<div id='safeInfo' class='safeInfo'>
STRICT;
echo outputLanguage($xml, 'safeSearch', true);
echo <<<STRICT
	</div>

<!--    <div id="safeContainer"> -->
    <div id="typesContainer">

        <div id='strict'>
STRICT;
echo outputLanguage($xml, 'safeStrict', true);
echo <<<STRICT

        </div>

        <div id='moderate'>
STRICT;
echo outputLanguage($xml, 'safeModerate', true);
echo <<<STRICT

        </div>

	</div>

	<br/>

	<div id='closeTypes' class="close" style="margin-top: 10px; position: relative; top: 0px; right: 0px; width: 97px; height: 28px;"><img src='images/search_go_icon.jpg'></div>
	
    
</div>

<div id="main">
   	<img class="border" src="images/box-top.png" alt="" />
    <div id="box">
    	<div id="Abox">
        	<h1 id="typeButton">
STRICT;
echo outputLanguage($xml, 'type', true);
echo <<<STRICT
</h1>
            <div id="blankWord" class="puzzle">
STRICT;
echo outputLanguage($xml, 'greenPiece', true);
echo <<<STRICT
</div>
            <div id="andWord" class="puzzle">
STRICT;
echo outputLanguage($xml, 'bluePiece', true);
echo <<<STRICT
</div>
            <div id="notWord" class="puzzle">
STRICT;
echo outputLanguage($xml, 'redPiece', true);
echo <<<STRICT
</div>
            <div id="orWord" class="puzzle">
STRICT;
echo outputLanguage($xml, 'orangePiece', true);
echo <<<STRICT
</div>
            <div id="urlWord" class="puzzle">
STRICT;
echo outputLanguage($xml, 'purplePiece', true);
echo <<<STRICT
</div>
        </div>
    	<div id="board">
        	&nbsp;
        	<p id="msg">
STRICT;
echo outputLanguage($xml, 'startMsg', true);
echo <<<STRICT
</p>
        </div>
   	<div id="Cbox">
		<img class="border" src="images/top-ul.jpg" alt="" />
		<ul>    
	         <li id = "trash"><img src="images/ico-trash.jpg" alt="" /><br />
STRICT;
echo outputLanguage($xml, 'trash', true);
echo <<<STRICT
</li>
		</ul>
		<img class="border" src="images/bottom-ul.jpg" alt="" />

		<img class="border" src="images/top-ul.jpg" alt="" />
		<ul>
                <li id="saveSearch"><a href="javascript:void(0);"><br /><img src="images/ico-save.jpg" alt="" />
STRICT;
echo outputLanguage($xml, 'saveSearch', true);
echo <<<STRICT
</a></li>
                <li id="viewSearch"><a href="javascript:void(0);"><br /><img src="images/ico-view.jpg" alt="" />
STRICT;
echo outputLanguage($xml, 'viewSearch', true);
echo <<<STRICT
</a></li>
                <li id="startOver"><a href="javascript:void(0);"><br /><img src="images/ico-start.jpg" alt="" />
STRICT;
echo outputLanguage($xml, 'startOver', true);
echo <<<STRICT
</a></li>
                <li id="removeLastPiece"><a href="javascript:void(0);"><img src="images/ico-back.jpg" alt="" />
STRICT;
echo outputLanguage($xml, 'back', true);
echo <<<STRICT
</a></li>
            </ul>
            <img class="border" src="images/bottom-ul.jpg" alt="" />
	</div>
    </div>
   	<img class="border" src="images/box-bottom.png" alt="" />
    <h2 style="font-size:12px">
<p id="msg2">
STRICT;
echo outputLanguage($xml, 'yourSearch', true);
echo <<<STRICT
: </p>



    <div id="Bluebox"></span>
    	<h3>
<span style='font-size: 9px;'>(<a target='_blank' href='http://boolify.org/numbers-note.php'>Results numbers are offline until resolved</a>)</span></h3>
        <div id="searchControl">
        </div>
        <span id="branding">
    </div>
<div style="text-align: center; font-size: 10px;">Copyright (c) The Public Learning Media Laboratory, 2010. Read our <a href=http://www.boolify.org/privacy.php>privacy policy</a>. </div>
</div>
<div id='notAndWord' class='puzzle'>
STRICT;
echo outputLanguage($xml, 'redAndPiece', true);
echo <<<STRICT
</div>
STRICT;
?>