$(function() {
	$('a#challenge-user').click(function() {
		// bypass shadowbox for now
		return true;
		
		var url = $(this).attr('href') + '?modal=1';
		Shadowbox.open({
			player: 'iframe'
			,content: url
			
			,height: 700
			,width: 700
		});
		return false;
	});
});
