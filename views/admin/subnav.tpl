<?php if (!empty($section)): ?>
	<?php $count = count($controller->views) ?>
	<?php $i = 0 ?>
	<ul>
		<?php foreach ($controller->views as $this_view => $info): ?>
			<li class="<?= (strtolower(Paraglide::$action) == strtolower($this_view)) ? 'active' : '' ?> <?= ($i == 0) ? 'first' : '' ?> <?= ($i + 1 == $count) ? 'last' : '' ?>">
				<a href="<?= Paraglide::url('admin', $section, $this_view) ?>" <?= !empty($info['new_window']) ? 'target="_blank"' : '' ?>>
					<?= htmlentities($info['title']) ?>
				</a>
			</li>
			<?php $i++ ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
