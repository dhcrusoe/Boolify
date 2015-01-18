<?php
echo <<<STRICT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<script src="http://www.google.com/jsapi?key=ABQIAAAA4JIh6t6WqZ6bu2qtSfaUHRT_MH5dYcXsMMhjPNkaLjfPMzh-axSMnb84GYT7DC4z1i5TuBUrsL4K-g" type="text/javascript"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<link rel="stylesheet" type="text/css" href="results.css">
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="gifimages.css" />
<![endif]-->
<script type="text/javascript" src="mootools-release-1.11.js"></script>
<script type="text/javascript">
    //<![CDATA[

    google.load("search", "1");
    google.load("language", "1");
       
    //]]>
</script>
<script type="text/javascript">
	var searches = new Hash.Cookie('searches');
	
	function get_searches()
	{
		var str = '';
		var sr = $('saved_results');
		var x = 1;
		str = '<table id=\"results_table\" cellpadding=\"3\" border=\"0\"><tr class=\"row_title\"><td>Searches from this session</td><td>Number of results</td></tr>';		
		if (searches.get('count') > 10)
			x = searches.get('count') - 10;
		for (; x <= searches.get('count'); x++)
		{
			str += '<tr id="result'+x+'" class="results"><td style="color: red;">' + searches.get('boolean'+x) + '</td><td>' + searches.get('results'+x) + '</td></tr>';			
			str += '<tr class="search-results" id="searchresult' + x + '"></tr>';
		}
		if (searches.get('count') == null)
			str += '<tr class="results"><td>None</td><td>None</td></tr>';
		str += '</table>';
		sr.setHTML(str);
	}		
	
	window.addEvent('domready', function()
	{		
		get_searches();
		
		$('search').addEvent('click',function(e)
		{
			window.location = './';
		});
		
		$$('tr.results').addEvent('click',function(e)
		{			
			var id = this.getProperty('id').substring(6);			
			loadrow = $('searchresult'+id);
			
			if (loadrow.innerHTML != '')
			{
				loadrow.innerHTML = '';
				loadrow.setStyle('display','none');
				return;
			}
			
			// loop through any open result strings and remove them
			$$('tr.search-results').setStyle('display','none');
			loadrow.innerHTML = "<td><img src='images/spinner.gif'> Searching Google.com... </td>";
			loadrow.setStyle('display','');
			
			// PERFORM A NEW SEARCH
		    var searchControl = new google.search.SearchControl();
		 	var type = searches.get('type'+id);
		    
		    if (type == 'web')
		    	var gsearch = new google.search.WebSearch();
		    else if (type == 'news')
		    	var gsearch = new google.search.NewsSearch();
		    else
		    	var gsearch = new google.search.ImageSearch();
		    	
		    gsearch.setRestriction(GSearch.RESTRICT_SAFESEARCH,
		                      GSearch.SAFESEARCH_STRICT);
			options = new GsearcherOptions();                      
			options.setExpandMode(GSearchControl.EXPAND_MODE_OPEN);	
		
			searchControl.addSearcher(gsearch,options);    
		    
		    // Tell the searcher to draw itself and tell it where to attach
		    searchControl.draw(document.getElementById("searchresult" + id));
		   
		    //searchControl.setSearchCompleteCallback(searchControl,grabResults);
			searchControl.execute(searches.get('boolean'+id));
			
			$$('.gsc-search-box').setStyle('visibility','hidden');	
			// END SEARCH
			//showrow.innerHTML = "<td>result data 1</td><td>result data 2</td>";

			$$('.gsc-results').setStyle('background-color','#f1f1f1');
			$$('.gsc-results').setStyle('border','2px dotted #CCCCCC');
			$$('.gsc-results').setStyle('padding','4px');
		});
		
		$('clear').addEvent('click',function(e)
		{
			searches.empty();
			get_searches();			
		});
		
		$('print').addEvent('click',function(e)
		{
			frames['piframe'].location.href = "printresults.php";
		});
		
	});
</script>
<div id="notice">
<B>Note:</B>  Your search activity is cleared immediately after you close your browser windows.<br>
No information about your search activity is saved or distributed.
</div>
<div id='results_box' class='container'>
	<div id='search' class='action'><img src='images/Search.png'><br>Return to Searching</div>
	<div id='clear' class='action'><img src='images/Delete.png'><br>Clear History</div>
	<div id='print' class='action'><img src='images/Print.png'><br>Print Results</div>
</div>
<div id="saved_results">	

</div>
<div id="results">
	<div id="searchcontrol"></div>
</div>
<iframe src=""
	id="piframe"
	name="piframe" style="visibility:hidden;"></iframe>
STRICT;
?>