<?php
require_once APP_PATH . 'lib/classes/paragon.php';
require_once APP_PATH . 'lib/classes/paragon_drivers/mysqli_master_slave_driver.php';
require_once APP_PATH . 'lib/classes/validator.php';
require_once APP_PATH . 'models/category.php';
require_once APP_PATH . 'models/event.php';
require_once APP_PATH . 'models/user.php';

class Hooks {
	static public function preprocess() {
		error_reporting(E_ALL);
		
		// configure paragon with mysqli
		$mysqli_driver = new MysqliMasterSlaveDriver(array(
			'master' => $GLOBALS['database'],
			'slave' => $GLOBALS['database'],
		));
		Paragon::set_connection($mysqli_driver);
		
		// configure facebook
		//$facebook_config = Web::parse_config('facebook');
		//$GLOBALS['data']['facebook_app_id'] = $facebook_config['app']['id'];
		
		// configure the default timezone
		date_default_timezone_set('America/New_York');
		
		if (get_magic_quotes_gpc()) {
			foreach ($_GET as $key => $val) {
				$_GET[$key] = stripslashes($val);
			}
			
			foreach ($_POST as $key => $val) {
				$_POST[$key] = stripslashes($val);
			}
			
			foreach ($_COOKIE as $key => $val) {
				$_COOKIE[$key] = stripslashes($val);
			}
		}
		
		//list($GLOBALS['data']['top_categories'], $pagination) = Category::popular(1, 5);
		//list($GLOBALS['data']['top_events'], $pagination) = Event::popular(null, 1, 5);
		//list($GLOBALS['data']['top_users'], $pagination) = User::top(1, 5);
	}
	
	static public function postprocess() {
	}
}
