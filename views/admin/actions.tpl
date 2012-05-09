<?php if (!empty($item) && $item->__primary_key__ != null): ?>
	<?php $count = count($controller->actions) ?>
	<ul>
		<?php $i = 0 ?>
		<?php foreach ($controller->actions as $action => $info): ?>
			<li id="item-action-<?= $action ?>" class="<?= (Paraglide::$action == $action) ? 'active' : '' ?> <?= ($i == 0) ? 'first' : '' ?> <?= ($i + 1 == $count) ? 'last' : '' ?>">
				<a href="<?= Paraglide::url('admin', $section, array($action, $item->__primary_key__)) ?>" <?= !empty($info['new_window']) ? 'target="_blank"' : '' ?>>
					<span><?= htmlentities($info['title']) ?></span>
				</a>
			</li>
			<?php $i++ ?>
		<?php endforeach; ?>
	</ul>
<?php elseif (!empty($search_fields)): ?>
	<?= Form::build(array(
		'class' => 'styled-form styled-form-newline-labels styled-form-fancy-legends',
		'method' => 'get',
		'fields' => array(
			'Looking for something?' => $search_fields,
			'<div class="submit">',
				Form::submit(array('class' => 'button', 'value' => 'Search')),
			'</div>',
		),
	)) ?>
<?php endif; ?>
