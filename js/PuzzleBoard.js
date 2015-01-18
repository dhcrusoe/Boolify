var PuzzleBoard = {
	"TYPE_WORD_OR_PHRASE": "word-or-phrase-piece",

	"TYPE_EXACT_WORD_OR_PHRASE": "exact-word-or-phrase-piece",

	"TYPE_SYNONYM": "synonym-piece",

	"TYPE_URL": "url-piece",

	"TYPE_NOT": "not-piece",

	"TYPE_OR": "or-piece",

	"_strings": {},

	"_receiveId": null,

	"_customSearchControl": null,

	"init": function(strings) {
		$.extend(PuzzleBoard._strings, strings);
		PuzzleBoard._setup();
		PuzzleBoard._assignEvents();
		PuzzleBoard.resetBoard();
	},

	"_setup": function() {
		$('#piece-bin > span').draggable({
			"helper": "clone",
			"connectToSortable" :"#piece-area",
			"cursor": "pointer",
			"start": function (event, ui) {
				var id =  ui.helper.prevObject.attr("id").replace("new-", "");
				ui.helper.addClass(id);
				ui.helper.width(ui.helper.prevObject.width());
			}
		});

		$('#piece-area').sortable({
			"placeholder": "piece placeholder",
			"tolerance": "pointer",
			"start": function(event, ui) {
				ui.placeholder.width(ui.helper.width());
			},
			"stop": function(event, ui) {
				if (PuzzleBoard._receiveId) {
					ui.item.addClass(PuzzleBoard._receiveId);
					PuzzleBoard._receiveId = null;
					PuzzleBoard.addInputElement(ui.item);
					PuzzleBoard._showPieceNotification(ui.item);
				}
				PuzzleBoard.updateSearch();
				ui.item.doubletap(function() {
					$(this).trigger("dblclick");
					return false;
				});
			},
			"receive": function(event, ui) {
				PuzzleBoard._receiveId = ui.item.attr("id").replace("new-", "");
			},
			"cursor": "pointer"
		});

		$("#saved-search-results").tinyscrollbar();

		google.load(
			"search",
			"1",
			{
				"language": "en",
				"style": google.loader.themes.V2_DEFAULT,
				"callback": function() {
					var customSearchOptions = {};
					PuzzleBoard._customSearchControl = new google.search.CustomSearchControl(
	      				'010392948077131302743:istn9tzdtdm',
	      				customSearchOptions
	      			);
					PuzzleBoard._customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
					PuzzleBoard._customSearchControl.draw('cse');
				}
			}
		);
	},

	"addInputElement": function(piece) {
		var spanElement = piece.children(".term");
		var inputElement = $("<input>");
		inputElement.attr("type", "text");
		inputElement.attr("name", "piece-term");
		inputElement.val(spanElement.text());
		piece.children(".separator").addClass("inverted");
		inputElement.width(spanElement.width());
		spanElement.remove();
		inputElement.blur(function(event) {
			PuzzleBoard.addSpanElement(piece);
		}).keypress(function(event) {
			if (event.which === 13) {
				PuzzleBoard.addSpanElement(piece);
			}
		});

		inputElement.appendTo(piece).focus();
		if (inputElement.width() < 10) {
			inputElement.width(10);
		}
		inputElement.autoGrowInput({
			"comfortZone": 10,
		});
	},

	"addSpanElement": function(piece) {
		var inputElement = piece.children("input");
		var spanElement = $("<span>");
		spanElement.addClass("term");
		spanElement.text(inputElement.val());
		
		piece.children(".separator").removeClass("inverted");
		inputElement.remove();
		
		spanElement.appendTo(piece);
		if (PuzzleBoard._getTermType(piece) == PuzzleBoard.TYPE_URL) {
			PuzzleBoard._checkUrl(piece);
		}
		$("span.tester").remove();
		PuzzleBoard.updateSearch();
	},

	"_assignEvents": function() {
		$("#puzzle-board").on("dblclick mouseover", ".piece", function() {
			PuzzleBoard._showPieceNotification($(this));
		});

		$("#piece-area").on("dblclick", ".piece", function(event) {
			PuzzleBoard.addInputElement($(this));
		});

		$("#piece-area").on("mouseenter", ".piece", function(event) {
			$(this).find(".close-button").show();
		});

		$("#piece-area").on("mouseleave", ".piece", function(event) {
			$(this).find(".close-button").hide();
		});

		$("#piece-area").on("click", ".close-button", function(event) {
			var piece = $(this).parent();
			PuzzleBoard._removePiece(piece);
		});

		$("#save-button").click(function() {
			PuzzleBoard.saveToCookie();
		});

		$("#view-button").click(function() {
			var ids = PuzzleBoard._getCookieIds();
			$("#saved-search-result-container").html("");
			for (var i in ids) {
				if (ids.hasOwnProperty(i)) {
					var savedSearchResult = $("<a>");
					savedSearchResult.attr("href", "#");
					savedSearchResult.attr("id", "saved-search-result-" + ids[i]);
					savedSearchResult.addClass("saved-search-result");
					savedSearchResult.text(PuzzleBoard._getSearchQuery(PuzzleBoard._getCookie(ids[i])));
					$("#saved-search-result-container").append(savedSearchResult);
				}
			}
			$("#saved-search-results").show();
			$(".saved-search-result").dotdotdot({"height": 20});
			$("#saved-search-results").tinyscrollbar_update();
			$("#saved-search-results").position({
				"my": "bottom",
				"at": "top",
				"of": $("#view-button"),
				"offset": "0 -5",
				"using": function(position) {
					$(this).css("top", position.top);
					$(this).css("left", position.left);
				}
			});
		});

		$("#reset-button").click(function() {
			PuzzleBoard.resetBoard();
		});

		$("#saved-search-results .close-button").click(function(event) {
			$("#saved-search-results").hide();
		});

		$("#saved-search-result-container").on("click", ".saved-search-result", function(event) {
			var id = parseInt($(this).attr("id").replace("saved-search-result-", ""), 10);
			PuzzleBoard._recoverFromCookie(id);
			PuzzleBoard.updateSearch();
			$("#saved-search-results").hide();

			event.preventDefault();
		});

	},

	"_showPieceNotification": function(piece) {
		var type = PuzzleBoard._getTermType(piece);
		if (type == PuzzleBoard.TYPE_WORD_OR_PHRASE) {
			PuzzleBoard._notify(PuzzleBoard._strings.wordOrPhraseNotification);
		} else if (type == PuzzleBoard.TYPE_EXACT_WORD_OR_PHRASE) {
			PuzzleBoard._notify(PuzzleBoard._strings.exactWordOrPhraseNotification);
		} else if (type == PuzzleBoard.TYPE_SYNONYM) {
			PuzzleBoard._notify(PuzzleBoard._strings.synonymNotification);
		} else if (type == PuzzleBoard.TYPE_URL) {
			PuzzleBoard._notify(PuzzleBoard._strings.urlNotification);
		} else if (type == PuzzleBoard.TYPE_NOT) {
			PuzzleBoard._notify(PuzzleBoard._strings.notNotification);
		} else if (type == PuzzleBoard.TYPE_OR) {
			PuzzleBoard._notify(PuzzleBoard._strings.orNotification);
		}
	},

	"_recoverFromCookie": function(id) {
		var pieceData = PuzzleBoard._getCookie(id);
		PuzzleBoard.resetBoard();
		for (var i in pieceData) {
			if (pieceData.hasOwnProperty(i)) {
				var clone = $("#new-" + pieceData[i][0]).clone();
				clone.removeAttr("id");
				clone.addClass(pieceData[i][0]);
				clone.children(".term").text(pieceData[i][1]);
				$("#piece-area").append(clone);
			}
		}
	},

	"updateSearch": function() {
		var pieceData = [];
		$("#piece-area .piece").each(function() {
			var piece = [];
			piece[0] = PuzzleBoard._getTermType($(this));
			piece[1] = $(this).children(".term").text();
			pieceData.push(piece);
		});

		if (PuzzleBoard._customSearchControl) {
			PuzzleBoard._customSearchControl.execute(PuzzleBoard._getSearchQuery(pieceData));	
		}

		$.get("callers/tracer.php", {"notaboot": "t"});
	},

	"_getTermType": function(piece) {
		var type = "";
		if (piece.hasClass("word-or-phrase-piece")) {
			type = PuzzleBoard.TYPE_WORD_OR_PHRASE;
		} else if (piece.hasClass("exact-word-or-phrase-piece")) {
			type = PuzzleBoard.TYPE_EXACT_WORD_OR_PHRASE;
		} else if (piece.hasClass("synonym-piece")) {
			type = PuzzleBoard.TYPE_SYNONYM;
		} else if (piece.hasClass("url-piece")) {
			type = PuzzleBoard.TYPE_URL;
		} else if (piece.hasClass("not-piece")) {
			type = PuzzleBoard.TYPE_NOT;
		} else if (piece.hasClass("or-piece")) {
			type = PuzzleBoard.TYPE_OR;
		}

		return type;
	},

	"_getSearchQuery": function(pieceData) {
		var query = " ";
		for (var i in pieceData) {
			if (pieceData.hasOwnProperty(i)) {
				var type = pieceData[i][0];
				var term = pieceData[i][1];
				if (type == PuzzleBoard.TYPE_WORD_OR_PHRASE) {
					query += term + " ";
				} else if (type == PuzzleBoard.TYPE_EXACT_WORD_OR_PHRASE) {
					query += "\"" + term + "\" ";
				} else if (type == PuzzleBoard.TYPE_SYNONYM) {
					query += "~\"" + term + "\" ";
				} else if (type == PuzzleBoard.TYPE_URL) {
					query += "site:" + term + " ";
				} else if (type == PuzzleBoard.TYPE_NOT) {
					query += "-\"" + term + "\" ";
				} else if (type == PuzzleBoard.TYPE_OR) {
					query += "OR \"" + term + "\" ";
				}
			}
		}

		return query;
	},

	"saveToCookie": function() {
		var ids = PuzzleBoard._getCookieIds();
		var id = 0;
		while ($.inArray("" + id, ids) != -1) {
			id++;
		}
		ids.push(id);

		$.cookie("boolify-ids", ids);
		$.cookie("boolify-" + id, PuzzleBoard._generateCookieString());
		PuzzleBoard._notify(PuzzleBoard._strings.searchSavedNotification);
	},

	"_getCookieIds": function() {
		var ids = $.cookie("boolify-ids");
		if (ids) {
			ids = ids.split(",")
			return ids;
		}
		return [];
	},

	"_getCookie": function(id) {
		var pieceData = $.cookie("boolify-" + id);
		if (pieceData) {
			pieceData = pieceData.split(",");
			for (var i in pieceData) {
				if (pieceData.hasOwnProperty(i)) {
					pieceData[i] = pieceData[i].split("|");
					pieceData[i][1] = unescape(pieceData[i][1]);
				}
			}
			return pieceData;
		}

		return false;
	},

	"_generateCookieString": function() {
		var pieceArray = [];
		$("#piece-area .piece").each(function() {
			var type = PuzzleBoard._getTermType($(this));
			var term = escape($(this).children(".term").text());
			
			pieceArray.push(type + "|" + term);
		});

		return pieceArray.toString();
	},

	"resetBoard": function() {
		$("#piece-area").children().remove();
		PuzzleBoard.updateSearch();
		PuzzleBoard._notify(PuzzleBoard._strings.blankNotification);
	},

	"_notify": function(text) {
		$("#notification-area").text(text);
	},

	"_checkUrl": function(piece) {
		var spanElement = piece.children("span.term");
		var regexp = /^http:\/\/|https:\/\//;
		if (!regexp.test(spanElement.text())) {
			spanElement.text("http://" + spanElement.text());
		}
	},

	"_removePiece": function(piece) {
		piece.remove();
		PuzzleBoard.updateSearch();
		PuzzleBoard._notify(PuzzleBoard._strings.removedNotification);
	}
}