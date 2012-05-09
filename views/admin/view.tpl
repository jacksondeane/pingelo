<?php foreach ($items as $key => $item): ?>
	<strong class="item-title"><?= $key ?></strong>
	<?= HtmlTable::build_single(array(
		'class' => 'styled-table',
		'fields' => $item['fields'],
		'item' => $item['item'],
	)) ?>
<?php endforeach; ?>
