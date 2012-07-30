<?php
//require_once APP_PATH . 'models/bet.php';
//require_once APP_PATH . 'models/transaction.php';
//require_once APP_PATH . 'models/user_email.php';
//require_once APP_PATH . 'models/user_role.php';

class User extends Paragon {
	protected static $_table = 'users';

	/*
	protected static $_belongs_to = array(
		'primary_email' => 'UserEmail',
	);
	
	protected static $_has_and_belongs_to_many = array(
		'roles' => array(
			'class' => 'UserRole',
			'table' => 'user_role_assignments',
			'foreign_key' => 'user_role_id',
			'primary_key' => 'user_id',
			'order' => 'name',
		),
	);
	*/
	/*
	protected static $_has_many = array(
		'bets' => array(
			'class' => 'Bet',
			'order' => '-date_created',
		),
		'emails' => 'UserEmail',
		'transactions' => 'Transaction',
	);
*/
/*	
	protected static $_has_one = array(
		'facebook_user' => 'FacebookUser',
	);

	public static $validations = array(
		'active' => array('boolean' => true),
		'verified' => array('boolean' => true),
		
		'points' => array('integer' => true, 'min' => 0),
		'gender' => array(
			'required' => true,
			'values' => array(
				'f' => 'Female',
				'm' => 'Male',
			),
		),
		'name' => array('required' => true, 'maxlength' => 255),
		'username' => array('required' => true, 'maxlength' => 255),
		'password' => array('required' => true),
		
		'num_bets' => array('integer' => true, 'min' => 0),
		'num_losses' => array('integer' => true, 'min' => 0),
		'num_wins' => array('integer' => true, 'min' => 0),
		'wins_to_losses' => array('min' => 0),
	);
*/
	public $id;
	
	public $date_created;
	public $date_updated;
	
	public $elo_rank = 100;
	public $twitter_username;
	public $profile_image_url;
	
	public $num_games = 0;
	public $num_wins = 0;
	
	public $is_active = 1;

	public static function get_leaders() {
		$leaders = self::find(array(
			'conditions' => array(
				'num_games' => self::condition('gt', 9),
				'is_active' => 1, ),
			'order' => '-elo_rank',
		));
		
		return $leaders;
	}
	
	public static function find_by_twitter_username($twitter_username) {
		return User::find_one(array(
			'conditions' => array('twitter_username' => $twitter_username)
		));
	}

}
