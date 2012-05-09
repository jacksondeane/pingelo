<?php
class Transaction extends Paragon {
	protected static $_table = 'transactions';
	
	protected static $_belongs_to = array(
		'bet' => 'Bet',
		'user' => 'User',
	);
	
	public static $validations = array(
		'bet_id' => array('integer' => true, 'min' => 0),
		'user_id' => array('required' => true, 'integer' => true, 'min' => 1),
		
		'date_created' => array(),
		
		'type' => array(
			'integer' => true,
			'values' => array(
				0 => 'Initial Deposit',
				1 => 'Daily Login',
				2 => 'Bet Deduction',
				3 => 'Bet Canceled',
				4 => 'Bet Won',
				5 => 'Bet Tied',
			),
		),
		
		'amount' => array('required' => true, 'integer' => true),
		'old_balance' => array('integer' => true, 'min' => 0),
		'new_balance' => array('integer' => true, 'min' => 0),
	);

	public $id;
	public $bet_id;
	public $user_id;
	
	public $date_created;
	
	public $type = 0;
	
	public $amount = 0;
	public $old_balance = 0;
	public $new_balance = 0;
	
	public static function create($params) {
		$user = User::find($params['user_id']);
		$transaction = new Transaction();
		$transaction->bet_id = !empty($params['bet_id']) ? $params['bet_id'] : 0;
		$transaction->user_id = !empty($user) ? $user->id : 0;
		$transaction->type = $params['type'];
		$transaction->amount = $params['amount'];
		$transaction->old_balance = !empty($user) ? $user->points : 0;
		$transaction->new_balance = !empty($user) ? ($user->points + $transaction->amount) : 0;

		if ($transaction->save()) {
			$user->points += $transaction->amount;
			$user->save();
		}
		
		return $transaction;
	}
}
