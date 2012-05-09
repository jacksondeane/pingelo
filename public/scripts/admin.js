var inflectUnderscore = function(word) {
	var inflected = '';
	
	for (var i = 0; i < word.length; i++) {
		letter = word[i];

		if (letter.toLowerCase() != letter) {
			if (i != 0) inflected += '_';
			letter = letter.toLowerCase();
		}

		inflected += letter;
	}

	return inflected;
};

$(function() {
	$('input.date').datetimepicker();
	Corners.apply();
	RelationshipSelectors.apply();
	Wysiwygs.apply();
	
	Shadowbox.init({
		skipSetup: true
	});
});

var Corners = {
	apply: function() {
		$('div#container').corner('16px');
		$('div#info').corner('6px top');
		$('a#logout').corner('5px');
		$('div#nav ul li.first a').corner('5px tl');
		$('div#nav ul li.last a').corner('5px tr');
		$('div#subnav ul li.first a').corner('5px tl');
		$('div#subnav ul li.last a').corner('5px tr');
		$('div#actions ul li a').corner('5px');
		$('form.styled-form-fancy-legends legend').corner('5px top');
		$('form.styled-form legend.fancy').corner('5px top');
		$('form.styled-form div.submit').corner('5px bottom');
		$('div#footer').corner('6px bottom');
	}
};

var RelationshipSelectors = {
	apply: function() {
		$('input.relationship-selector').each(function() {
			var input = $(this);
			input.hide();
			var title = input.attr('title');
			var controller = inflectUnderscore(title) + 's';
			var url = base_url + 'admin/' + controller + '/relationship-selector';
			url += '?modal=1';
			
			var inputId = input.attr('id');

			if (inputId.substr(0, 'item-selected-'.length) == 'item-selected-') {
				$('<span class="selection"> | </span>').prependTo(input.parent());
				var selectedName = inputId.substr('item-selected-'.length);
				var selectedUrl = base_url + 'admin/' + controller + '/' + input.val();
				selectedName = decodeURIComponent(selectedName).replace('+', ' ');
				$('<a class="selection"></a>').attr({
					href: selectedUrl
					,target: '_blank'
				}).text(selectedName).prependTo(input.parent());
				var selectTitle = 'Select another ' + title;
			} else {
				var selectTitle = 'Select a ' + title;
			}
			
			var link = $('<a></a>').attr('href', 'url').text(selectTitle).click(function() {
				Shadowbox.open({
					content: url
					,player: 'iframe'
				});
				
				RelationshipSelectors.attachItemSelector(function(url, name) {
					Shadowbox.close();
					
					var parts = url.split('/');
					var id = parts[parts.length - 1];
					
					link.text('Select another ' + title);
					link.parent().find('.selection').remove();
					$('<span class="selection"> | </span>').prependTo(input.parent());
					$('<a class="selection"></a>').attr({
						href: url
						,target: '_blank'
					}).text(name).prependTo(input.parent());
					input.val(id);
				});
				return false;
			});
			link.appendTo(input.parent());
		});
	},
	
	attachItemSelector: function(callback) {
		window.modalEvents = function(win) {
			var anchors = win.$(win.document.body).find('td a');
			anchors.die().live('click', function() {
				var url = win.$(this).attr('href');
				var name = win.$(this).text();
				callback(url, name);
				return false;
			});
		};
	}
};

var Wysiwygs = {
	apply: function() {
		if (typeof tinyMCE == 'undefined') {
			return;
		}
		
		tinyMCE.init({
			editor_selector: 'wysiwyg'
			,mode: 'specific_textareas'
			,plugins: 'fullscreen'
			,theme: 'advanced'
			,theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,|,forecolor,backcolor,|,bullist,numlist,|,link,unlink,|,image,|,code,fullscreen'
			,theme_advanced_buttons2: ''
			,theme_advanced_buttons3: ''
			,theme_advanced_buttons4: ''
		});
	}
};
