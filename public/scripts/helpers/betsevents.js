$(function() {
	if (controller_action == 'create') {
		var to_user_facebook_id = $('form#create-bet input[name=to_user_facebook_id]');
		var url = base_url + 'account/friends.js';

		$.getJSON(url, function(friends) {
			$('form#create-bet input[name=to_user]').autocomplete(friends, {
				matchContains: true
				,mustMatch: true
				,formatItem: function(data, position, items) {
					return data.name || '';
				}
			}).bind('result', function(e, data) {
				if (data) {
					$('#bet-challenge-li').show();
					to_user_facebook_id.val(data.id);
					var name = data.name.split(" ")[0];
					var image_tag = '<img src="http://graph.facebook.com/' + data.id + '/picture" alt="Photo" />';
					$('div#opponent_image').html(image_tag)
					$('div#opponent_name').html(name)
					//$('div#bet-to-user-show').html(html);
				} else {
					$('#bet-challenge-li').hide()
				}
			});
		});
	}
});
