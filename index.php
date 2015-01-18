<?php

$startingLanguage = 0;

function translate($tag) {
	global $xml;
	global $lang;

	return $xml->$lang->$tag;
}

$lang = isset($_GET["currentLang"]) ? $_GET["currentLang"] : "en";
if (!isset($xml->$lang)) {
	$lang = "en";
}

function outputLanguage($xml, $tag, $span)
{
	global $startingLanguage;
	$return = '';
	if($span)
	{
		$return .= '<span class="translate" id="';
	}
	$numLangs = count($xml->language);
	for($n=0;$n<$numLangs;$n++)
	{
		$return .= $xml->language[$n]->$tag;
		if($n<$numLangs-1)
		{
			$return .= '|';
		}
	}
	if($span)
	{
		$return .= '">'.$xml->language[$startingLanguage]->$tag.'</span>';
	}
	return $return;
}

if(!file_exists('language.xml')) {
	die('Could not find language file.');
} else {
	$xml = new SimpleXMLElement('language.xml',NULL,TRUE);
	$xml->asXML();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="This material is part of the source file for Boolify, a search tool.">
	<title>Boolify Source Code</title>
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.21.min.js"></script>
	<script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/jquery.autoGrowInput.js"></script>
	<script type="text/javascript" src="js/jquery.dotdotdot-1.5.1.js"></script>
	<script type="text/javascript" src="js/jquery.tinyscrollbar.min.js"></script>
	<script type="text/javascript" src="js/jquery.doubletap.js"></script>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" src="js/PuzzleBoard.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		PuzzleBoard.init(
			{
				"blankNotification": "<?php echo translate("blankNotification"); ?>",
				"wordOrPhraseNotification": "<?php echo translate("wordOrPhraseNotification"); ?>",
				"exactWordOrPhraseNotification": "<?php echo translate("exactWordOrPhraseNotification"); ?>",
				"synonymNotification": "<?php echo translate("synonymNotification"); ?>",
				"urlNotification": "<?php echo translate("urlNotification"); ?>",
				"notNotification": "<?php echo translate("notNotification"); ?>",
				"orNotification": "<?php echo translate("orNotification"); ?>",
				"removedNotification": "<?php echo translate("removedNotification"); ?>",
				"searchSavedNotification": "<?php echo translate("searchSavedNotification"); ?>"
			});	
	});
	</script>
	<link rel="stylesheet" type="text/css" href="css/boolify.css">
	<!--[if lt IE 9]>
	<style type="text/css">
		.piece input {
			height: 22px;
			padding-top: 6px;
		}
	</style>
	<![endif]-->

</head>
<div id="header">
	
    <div id="ihead2">
    <div id="before_tags1"></div>
  <h1 id="tags1" >Boolify: Core Function and Source Files</h1>
  <div id="after_tags1"></div>
      
		
</div><!--ihead2-->
</div>


				<div id="puzzle-board-container">
			<div id="puzzle-board">
				<div id="piece-bin">
					<span id="new-word-or-phrase-piece" class="piece">
						<span class="label"><?php echo translate("wordOrPhrasePiece"); ?></span>
						<span class="separator"></span>
						<span class="term"></span>
						<a class="close-button"></a>
					</span>
					<span id="new-exact-word-or-phrase-piece" class="piece">
						<span class="label"><?php echo translate("exactWordOrPhrasePiece"); ?></span>
						<span class="separator"></span>
						<span class="term"></span>
						<a class="close-button"></a>
					</span>
					<span id="new-not-piece" class="piece">
						<span class="label"><?php echo translate("notPiece"); ?></span>
						<span class="separator"></span>
						<span class="term"></span>
						<a class="close-button"></a>
					</span>
					<span id="new-or-piece" class="piece">
						<span class="label"><?php echo translate("orPiece"); ?></span>
						<span class="separator"></span>
						<span class="term"></span>
						<a class="close-button"></a>
					</span>
					<span id="new-url-piece" class="piece">
						<span class="label"><?php echo translate("urlPiece"); ?></span>
						<span class="separator"></span>
						<span class="term"></span>
						<a class="close-button"></a>
					</span>
					<span id="new-synonym-piece" class="piece">
						<span class="label"><?php echo translate("synonymPiece"); ?></span>
						<span class="separator"></span>
						<span class="term"></span>
						<a class="close-button"></a>
					</span>
				</div>
				<div id="puzzle-board-right">
					<div id="piece-area"></div>
					<div id="bottom-bar">
						<div id="notification-area"></div>
						<div id="button-area">
							<button type="button" id="save-button" class="shadow"><?php echo translate("saveButtonText"); ?></button>
							<button type="button" id="view-button" class="shadow"><?php echo translate("viewButtonText"); ?></button>
							<button type="button" id="reset-button" class="shadow"><?php echo translate("resetButtonText"); ?></button>
						</div>
					</div>
				</div>
				<div id="saved-search-results">
					<div class="viewport">
						<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
						<div class="overview">
							<h2><?php echo translate("savedSearchResultsText"); ?></h2>
							<div id="saved-search-result-container">
							</div>
						</div>
					</div>
					<a href="#" class="close-button"></a>
				</div>
			</div>
			<div id="cse" style="width: 100%;"></div>
		</div>

	
</body>
</html>
