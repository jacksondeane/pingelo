<?php
require_once APP_PATH . 'lib/classes/admin_controller_base.php';

class MainController extends AdminControllerBase {
	public static $tab_position = -1;
	public static $title = 'Main';
	
	public $views = array();

	public function login() {
		if (!empty($_POST['email']) && !empty($_POST['password'])) {
			User::try_login($_POST['email'], $_POST['password'], Paraglide::url('admin'));
		}
		
		if (empty($user)) {
			$user = new User();
		}
		
		$this->_add_breadcrumb('Login', Paraglide::url('admin', 'login'));
		Paraglide::render_view('admin/login', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'tabs' => $this->_tabs,
			'title' => 'Admin Login',
			'user' => $user,
		));
	}

	public function logout() {
		$this->_user->logout();
	}
}
