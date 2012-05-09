<form id="confirm-form" class="styled-form" method="post">
	<div id="confirm-message">
		<?= $message ?>
	</div>
	<input type="submit" class="button" name="confirm" value="Confirm" />
	<a href="<?= $cancel_url ?>">Cancel</a>
</form>
