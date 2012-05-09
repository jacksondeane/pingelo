<span id="time">
	<?= date('l, F jS, Y') ?>
	|
	<strong><?= date('g:ia') ?></strong>
	|
	<em>Admin v1.0</em>
</span>
<span id="account">
	<?php if (!empty($user) && !empty($user->id)): ?>
		Logged in as:
		<a href="<?= Paraglide::url('admin', null, array('users', $user->id)) ?>"><?= $user->email ?></a>
		|
		<a href="<?= Paraglide::url('admin', 'logout') ?>" id="logout">Logout</a>
	<?php endif; ?>
</span>
	