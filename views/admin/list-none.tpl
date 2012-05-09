There are no <?= strtolower($controller->noun) ?> items which match this request.
<?php if (!empty($create_url)): ?>
	<a href="<?= $create_url ?>">Add one now.</a>
<?php else: ?>
	<a href="<?= Paraglide::url('admin/' . $section, 'create') ?>">Add one now.</a>
<?php endif; ?>
