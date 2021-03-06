<?php
require_once APP_PATH . 'lib/helpers/EloRating.php';
require_once APP_PATH . 'models/result.php';
require_once APP_PATH . 'models/user.php';

class UsersController {
	private $_user;
	
	public $models = array('User');

	public function _preprocess() {
		//$this->_user = User::logged_in_user();
	}

	public function index($username = null) {

		$user = User::find_by_twitter_username($username);
		//$leaders = User::get_leaders();
		
		$results = Result::get_results_for_user($username);

		Paraglide::render_view('users/index', array(
			//'leaders' => $leaders,
			//'new_ratings' => $new_ratings,
			'user' => $user,
			'results' => $results,
		));
	}
	
	public function view($username = null) {
		
		$user = User::find_by_twitter_username($username);

		//$user_results = Result::find

		Paraglide::render_view('users/view', array(
			'user' => $user,
		));
	}

	public function add() {
		error_log("IN ADD");

		$new_ratings = null;
		$winning_user = null;
		$losing_user = null;
		$error_msg = null;

		if (!empty($_POST)) {
			error_log("IN POST");
			if (isset($_POST['post_winner_id']) && $_POST['post_loser_id']) {
				error_log("CREATE RESULT");

				$error_msg = "";
				
				//CREATE NEW RESULT

				$winner_id = $_POST['post_winner_id'];
				$loser_id = $_POST['post_loser_id'];
				
				if (empty($winner_id) || empty($loser_id)) {
					$error_msg = @"MUST ENTER 2 PLAYERS";
					return;
				}

				if ($winner_id == $loser_id) {
					$error_msg = @"CAN NOT HAVE A RESULT AGAINST YOURSELF";
					return;
				}

				$winning_user = User::find_by_twitter_username($winner_id);
				$losing_user = User::find_by_twitter_username($loser_id);
				

				if ($winning_user && $losing_user) {
					
					error_log("have winner and loser");
					error_log($winning_user->twitter_username);
					error_log($losing_user->twitter_username);

					//A = Winner B = Loser : Rating(ratingA, ratingB, scoreA, scoreB)

					$elo_rating = new Rating($winning_user->elo_rank, $losing_user->elo_rank, 1, 0);
					$new_ratings = $elo_rating->getNewRatings();

					
					//RESULT
					$new_result = new Result();
					$new_result->winner_user_id 	= $winning_user->id;
					$new_result->winner_rank_before = $winning_user->elo_rank;
					$new_result->winner_rank_after 	= $new_ratings['a'];
					$new_result->winner_num_wins++;
					$new_result->winner_num_games++;

					$new_result->loser_user_id 		= $losing_user->id;
					$new_result->loser_rank_before 	= $losing_user->elo_rank;
					$new_result->loser_rank_after	= $new_ratings['b'];

					if ($losing_user->num_wins >= 1) {
						$new_result->loser_num_wins -= 1;
					} else {
						$new_result->loser_num_wins = 0;
					}
					


					$new_result->loser_num_games++;

					if ($new_result->save()) {
						
						error_log("saved result, updating users");

						$winning_user->elo_rank = $new_ratings['a'];;
						$winning_user->num_wins++;
						$winning_user->num_games++;

						$losing_user->elo_rank = $new_ratings['b'];
						$losing_user->num_games++;


						$winning_user->save();
						$losing_user->save();
					}	
				}
			}
		}


		$users = User::find(array(
			'order' => '-elo_rank',
			'is_active' => 1,
		));
		
		Paraglide::render_view('results/add', array(
			'users' => $users,
			'new_ratings' => $new_ratings,
			'losing_user' => $losing_user,
			'winning_user' => $winning_user,
			'error_msg' => $error_msg,
		));
	}

	public function add_player() {
		
		$add_error_msg = null;
		$add_success_msg = null;

		if (!empty($_POST)) {
			if (isset($_POST['new_player_twitter_username'])) {
				
				$new_twitter = $_POST['new_player_twitter_username'];

				
				if (!strlen($new_twitter) > 0) {
					$error_msg = "Please enter a twitter username to add.";
				}
				//Check/Add "@" to front
				if (substr($new_twitter, 0, 1) != "@") {
					$new_twitter = "@" . $new_twitter;
				}

				$user = User::find_by_twitter_username($new_twitter);
				
				if (empty($user)) {
					
					$new_user = new User();
					$new_user->twitter_username = $new_twitter;
					$new_user->num_wins = 0;
					$new_user->num_games = 0;
					if ($new_user->save()) {
						$add_success_msg = $new_twitter . "added!";
					}

				} else {
					$add_error_msg =  $new_twitter . " already in use";
				}
			}
		}

		$users = User::find(array(
			'order' => '-elo_rank',
		));

		Paraglide::render_view('results/add', array(
			'users' => $users,
			'add_error_msg' => $add_error_msg,
			//'new_ratings' => $new_ratings,
			//'losing_user' => $losing_user,
			//'winning_user' => $winning_user,
			//'error_msg' => $error_msg,
		));
	}
}
