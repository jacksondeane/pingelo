<?php
require_once APP_PATH . 'models/facebook_user.php';
require_once APP_PATH . 'models/event.php';
require_once APP_PATH . 'models/transaction.php';

class Bet extends Paragon {
	protected static $_table = 'bets';
	
	protected static $_belongs_to = array(
		'choice' => 'Choice',
		'event' => 'Event',
		'user' => 'User',
	);
	
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
	
	public $id;
	public $choice_id;
	public $event_id;
	public $user_id;
	
	public $date_created;
	public $date_updated;
	
	public $message;
	
	public static function process_results() {
		$events_to_process = Event::find(array(
			'conditions' => array(
				'processed' => 0,
			),
		));
		
		foreach ($events_to_process as $event) {
			$user = $bet->user;
			$correct_choice = $event->correct_choice();
			$bets = $event->bets(array(
				'conditions' => array(
					'status' => 4,
				),
			));
			
			if (empty($bets)) {
				continue;
			}
			
			foreach ($bets as $bet) {
				if ($bet->did_win()) {
					// stats: num_wins and wins_to_losses
					$user->num_wins++;
					
					if ($user->num_losses > 0) {
						$user->wins_to_losses = $user->num_wins / $user->num_losses;
					} else {
						$user->wins_to_losses = $user->num_wins;
					}
					
					$user->save();
					
					Transaction::create(array(
						'user_id' => $user->id,
						'bet_id' => $bet->id,
						'type' => 4,
						'amount' => $bet->amount,
					));
				} else {
					// stats: num_losses and wins_to_losses
					$user->num_losses++;
					$user->wins_to_losses = $user->num_wins / $user->num_losses;
					$user->save();
				}
				
				$bet->status = 5;
				$bet->save();
			}
			
			$event->processed = true;
			$event->save();
		}
	}
	
	public function did_win() {
		$event = $this->event;
		
		if ($event->result != 3) {
			return false;
		}

		return ($event->choice_id == $this->choice_id);
	}

	public function save() {
		if (!empty($this->id)) {
			return parent::save();
		}
		
		if (!parent::save()) {
			return false;
		}
		
		// stats: num_bets
		$this->user->num_bets++;
		$this->user->save();
		$this->event->num_bets++;
		$this->event->save();
		$this->event->category->num_bets++;
		$this->event->category->save();
		$this->choice->num_bets++;
		$this->choice->save();
		
		return true;
	}
}
