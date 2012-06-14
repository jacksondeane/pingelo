$(function() {
	$('div#winner_div').click(function() {
		

		var selector = 'div#loser_div[value="' + $('span#winner_container').text() + '"]';
		$(selector).show();

		$('span#winner_container').text($(this).text());
		$('input#post_winner_id').val($(this).text());

		var selector = 'div#loser_div[value="' + $(this).text() + '"]';
		$(selector).hide();

		return true;
	});

	$('div#loser_div').click(function() {

		var selector = 'div#winner_div[value="' + $('span#loser_container').text() + '"]';
		$(selector).show();

		$('span#loser_container').text($(this).text());
		$('input#post_loser_id').val($(this).text());

		var selector = 'div#winner_div[value="' + $(this).text() + '"]';
		$(selector).hide();

		return true;
	});
});