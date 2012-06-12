<?php
require_once APP_PATH . 'lib/helpers/EloRating.php';
require_once APP_PATH . 'models/result.php';

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
		));
		
		Paraglide::render_view('results/add', array(
			'users' => $users,
			'new_ratings' => $new_ratings,
			'losing_user' => $losing_user,
			'winning_user' => $winning_user,
			'error_msg' => $error_msg,
		));
	}
}
