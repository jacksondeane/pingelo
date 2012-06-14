<?php
require_once APP_PATH . 'lib/apis/google/apiClient.php';
require_once APP_PATH . 'lib/apis/google/contrib/apiOauth2Service.php';
require_once APP_PATH . 'lib/helpers/EloRating.php';
require_once APP_PATH . 'models/result.php';

class oauth2callbackController {
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


		session_start();
		$client = new apiClient();
		$client->setApplicationName("Pingelo");
		// Visit https://code.google.com/apis/console?api=plus to generate your
		// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.

		$client->setClientId('191654827151.apps.googleusercontent.com');
		$client->setClientSecret('cX8eLDSE5mdFY6BXwzz1pXmn');
		$client->setRedirectUri('https://www.pingelo.com/oauth2callback');
		$client->setDeveloperKey('AIzaSyCOPD8gAKBEm4guY-o-TS8HOl600Zm3BHs');

		$oauth2 = new apiOauth2Service($client);

		if (isset($_GET['code'])) {
			error_log('authenticate');
		  	$client->authenticate();

		  	$_SESSION['token'] = $client->getAccessToken();
		  	$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		  	header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));

		}

		if (isset($_SESSION['token'])) {
			error_log('HAVE TOKEN');
		 	$client->setAccessToken($_SESSION['token']);
		}

		if (isset($_REQUEST['logout'])) {
			error_log('logout');
		  	unset($_SESSION['token']);
		  	$client->revokeToken();
		}

		if ($client->getAccessToken()) {
			error_log('getAccessToken');
		  	$user = $oauth2->userinfo->get();

		  	// These fields are currently filtered through the PHP sanitize filters.
		  	// See http://www.php.net/manual/en/filter.filters.sanitize.php
		  	$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		  	$img = filter_var($user['picture'], FILTER_VALIDATE_URL);
		  	$personMarkup = "$email<div><img src='$img?sz=50'></div>";

		  	// The access token may have been updated lazily.
		  	$_SESSION['token'] = $client->getAccessToken();
		} else {
			error_log('createAuthUrl');
		  	$authUrl = $client->createAuthUrl();
		}




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
			'auth_url' => $authUrl,
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
			'auth_url' => $auth_url,
			//'new_ratings' => $new_ratings,
			//'losing_user' => $losing_user,
			//'winning_user' => $winning_user,
			//'error_msg' => $error_msg,
		));
	}
}
