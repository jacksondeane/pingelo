<form id="confirm-form" class="styled-form" method="post">
	<div id="confirm-message">
		Are you sure you want to delete this <?= $controller->noun ?>?
	</div>
	<input type="submit" class="button" name="confirm" value="Confirm" />
	<a href="<?= Paraglide::url('admin/' . $section, null, $item->id) ?>">Cancel</a>
</form>
