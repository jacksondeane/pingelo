<?php
require_once APP_PATH . 'lib/helpers/EloRating.php';

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
		
		$elo_rating = new Rating(116, 84, 1, 0);
		$new_ratings = $elo_rating->getNewRatings();
		
		Paraglide::render_view('main/index', array(
			//'breadcrumbs' => $this->_breadcrumbs,
			//'tabs' => $this->_tabs,
			//'title' => 'Admin Login',
			'leaders' => $leaders,
			'new_ratings' => $new_ratings,
		));
	}
}
