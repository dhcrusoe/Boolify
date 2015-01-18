<?php
echo <<<STRICT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<link rel="stylesheet" type="text/css" href="results.css">
<script type="text/javascript" src="mootools-release-1.11.js"></script>
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
		window.print();
	});
</script>
<div id="saved_results">	

</div>
<div id="results">
	<div id="searchcontrol"></div>
</div>
STRICT;
?>