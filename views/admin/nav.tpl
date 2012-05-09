<ul>
	<?php $count = count($tabs) ?>
	<?php $i = 0 ?>
	<?php foreach ($tabs as $key => $tab): ?>
		<li class="<?= (!empty($section) && strtolower($section) == strtolower(str_replace(' ', '_', $tab))) ? 'active' : '' ?> <?= ($i == 0) ? 'first' : '' ?> <?= ($i + 1 == $count) ? 'last' : '' ?>">
			<a href="<?= Paraglide::url('admin', null, strtolower(str_replace(' ', '-', $tab))) ?>"><?= $tab ?></a>
		</li>
		<?php $i++ ?>
	<?php endforeach; ?>
</ul>
