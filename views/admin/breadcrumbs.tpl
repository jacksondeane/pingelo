<ul>
	<?php foreach ($breadcrumbs as $key => $breadcrumb): ?>
		<?php if ($key + 1 == count($breadcrumbs)): ?>
			<li class="active">
		<?php else: ?>
			<li>
		<?php endif; ?>
		<?php if ($key > 0): ?>
			&nbsp;/
		<?php endif; ?>
			<a href="<?= $breadcrumb['url'] ?>"><?= htmlentities($breadcrumb['title']) ?></a>
		</li>
	<?php endforeach; ?>
</ul>
