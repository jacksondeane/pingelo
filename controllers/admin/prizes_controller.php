<?php
require_once APP_PATH . 'lib/classes/admin_controller_base.php';

class PrizesController extends AdminControllerBase {
	public static $title = 'Prizes';
	
	public $title_field = 'title';
	public $model = 'Prize';
	public $models = array('Prize', 'User');
	public $noun = 'prize';
	public $readonly_fields = array('date_created', 'date_updated');
	
	public $search_fields = array(
		'title' => array(
			'admin_url' => array(
				'_section_',
				array('%s'),
				array('id'),
			),
			'sortable' => true,
		),
		'amount' => array(
			'sortable' => true,
		),
		'quantity' => array(
			'sortable' => true,
		),
		'is_active' => array(
			'sortable' => true,
			'boolean' => true,
		),
	);
}
