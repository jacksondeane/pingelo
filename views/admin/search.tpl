<?= Form::build(array(
	'class' => 'styled-form',
	'method' => 'get',
	'fields' => array(
		$search_fields,
		Form::submit(array('class' => 'button', 'value' => 'Search')),
	),
)) ?>

<?php if (!empty($conditions)): ?>
	<br /><br />
	<?php if (!empty($items)): ?>
		<?php Paraglide::render_view('admin/list') ?>
	<?php else: ?>
		<?php Paraglide::render_view('admin/list-none') ?>
	<?php endif; ?>
<?php endif; ?>
