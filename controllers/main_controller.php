<?php
require_once APP_PATH . 'lib/helpers/EloRating.php';
require_once APP_PATH . 'models/result.php';

class MainController {
	private $_user;
	
	public $models = array('User');

	public function _preprocess() {
		//$this->_user = User::logged_in_user();
	}

	public function index() {
		/*
		if (!empty($this->_user)) {
			Web::redirect('account');
		}
		*/
		//Web::render_view('main/index');
		
		$leaders = User::get_leaders();
		$last_20_results = Result::top(20);
		
		
		Paraglide::render_view('main/index', array(
			//'breadcrumbs' => $this->_breadcrumbs,
			//'tabs' => $this->_tabs,
			//'title' => 'Admin Login',
			'leaders' => $leaders,
			'last_20_results' => $last_20_results,
		));
	}
}
