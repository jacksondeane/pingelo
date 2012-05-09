<?php
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
		Web::render_view('main/index');
	}
}
