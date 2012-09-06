<?php
require_once APP_PATH . 'models/user.php';
//require_once APP_PATH . 'models/event.php';
//require_once APP_PATH . 'models/transaction.php';

class Result extends Paragon {
	protected static $_table = 'results';
	
	protected static $_belongs_to = array(
		'winner_user' => 'User',
		'loser_user' => 'User',
	);
	
	/*
	protected static $_has_many = array(
		'transactions' => 'Transaction',
	);
	
	public static $validations = array(
		'choice_id' => array('integer' => true, 'required' => true, 'min' => 1),
		'event_id' => array('label' => 'Event', 'integer' => true, 'required' => true, 'min' => 1),
		'user_id' => array('integer' => true, 'required' => true, 'min' => 1),
		
		'date_created' => array(),
		'date_updated' => array(),
		
		'message' => array('maxlength' => 255),
	);
	*/
	
	public $id;
	
	public $winner_user_id;
	public $winner_rank_before;
	public $winner_rank_after;
	public $winner_num_wins;
	public $winner_num_games;
	
	public $loser_user_id;
	public $loser_rank_before;
	public $loser_rank_after;
	public $loser_num_wins;
	public $loser_num_games;
	
	public $date_created;
	public $date_updated;
	
	public static function top($limit = null) {
		return self::find(array(
			'order' => '-date_updated',
			'limit' => $limit,
		));
	}

	public static function get_results_for_user($twitter_username = null) {

		$user = User::find_by_twitter_username($twitter_username);

		$results;

		if (!empty($user)) {
			$wins = self::find(array(
			'conditions' => array(
				'winner_user_id' => $user->id),
			'order' => '-date_updated',
			));

			$losses = self::find(array(
			'conditions' => array(
				'loser_user_id' => $user->id),
			'order' => '-date_updated',
			));	
			
			//Merge Wins and Losses
			$merged_results = array_merge($wins, $losses);

			//Compare Function used in Sort
			function cmp($a, $b) {
    			return strcmp($a->date_updated, $b->date_updated);
			}

			//Sort Merged Wins and Losses
			usort($merged_results, "cmp");


			$final_results = array();
			foreach ($merged_results as $r => $result) {

				if ($result->winner_user_id == $user->id) {
					//IS WINNER
					$final_results[] = $result->winner_rank_after;

				} else if ($result->loser_user_id == $user->id) {
					//IS LOSER
					$final_results[] = $result->loser_rank_after;

				}

			}
		}

		return $final_results;
	}

	public static function get_records_for_user($twitter_username = null) {
		//[username, wins, losses, win%]
		$user = User::find_by_twitter_username($twitter_username);

		$results;
		if (!empty($user)) {
			$wins = self::find(array(
			'conditions' => array(
				'winner_user_id' => $user->id),
			'order' => '-date_updated',
			));

			$losses = self::find(array(
			'conditions' => array(
				'loser_user_id' => $user->id),
			'order' => '-date_updated',
			));	
			
			//Merge Wins and Losses
			$merged_results = array_merge($wins, $losses);

			//Compare Function used in Sort
			function cmp($a, $b) {
    			return strcmp($a->date_updated, $b->date_updated);
			}
			//Sort Merged Wins and Losses
			usort($merged_results, "cmp");


			$final_results = array();
			foreach ($merged_results as $r => $result) {

				if ($result->winner_user_id == $user->id) {
					//IS WINNER
					$final_results[] = $result->winner_rank_after;

				} else if ($result->loser_user_id == $user->id) {
					//IS LOSER
					$final_results[] = $result->loser_rank_after;

				}

			}
		}

		return $final_results;
	}
}
