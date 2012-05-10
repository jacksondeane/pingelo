<?php
require_once APP_PATH . 'lib/helpers/EloRating.php';

class ResultsController {
	private $_user;
	
	public $models = array('User');

	public function _preprocess() {
		//$this->_user = User::logged_in_user();
	}

	public function index() {
		$leaders = User::get_leaders();
		
		$elo_rating = new Rating(116, 84, 1, 0);
		$new_ratings = $elo_rating->getNewRatings();
		
		Paraglide::render_view('results/index', array(
			'leaders' => $leaders,
			'new_ratings' => $new_ratings,
		));
	}
	
	public function add() {
				
		$leaders = User::get_leaders();
				
		Paraglide::render_view('results/add', array(
			'leaders' => $leaders,
			'new_ratings' => $new_ratings,
		));
	}
}
