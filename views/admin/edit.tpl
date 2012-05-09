<?= Form::errors_to_labels_string($item->errors) ?>

<?= Form::build(array(
	'class' => 'styled-form',
	'fields' => array(
		$fields,
		Form::submit(array('value' => 'Save', 'class' => 'button'))
	)
)) ?>
