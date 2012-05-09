<?= Form::errors_to_labels_string($user->errors) ?>
<?= Form::build(array(
	'id' => 'login-form',
	'class' => 'styled-form styled-form-fancy-legends',
	'fields' => array(
		'Login to the Admin' => array(
			'email' => array('value' => $user->email),
			'password' => array('type' => 'password'),
		),
		'<div class="submit">',
			Form::submit(array('class' => 'button', 'value' => 'Log In')),
		'</div>',
	)
)) ?>
