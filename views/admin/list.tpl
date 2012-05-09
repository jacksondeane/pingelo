<?php if (!empty($pagination)): ?>
	<?php Paraglide::render_view('admin/pagination') ?>
<?php endif; ?>

<?= HtmlTable::build(array(
	'class' => 'styled-table',
	'columns' => $fields,
	'data' => $items,
	'order' => $order,
)) ?>

<?php if (!empty($pagination)): ?>
	<?php Paraglide::render_view('admin/pagination') ?>
<?php endif; ?>
