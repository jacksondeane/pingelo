$(function() {
	$('div#winner_div').click(function() {
		$('td#winner_container').text($(this).text());
		$('input#post_winner_id').val($(this).text());

		return true;
	});

	$('div#loser_div').click(function() {
		$('td#loser_container').text($(this).text());
		$('input#post_loser_id').val($(this).text());

		return true;
	});
});