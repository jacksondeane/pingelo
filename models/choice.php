<?php
class Choice extends Paragon {
	protected static $_table = 'choices';

	protected static $_belongs_to = array(
		'event' => 'Event',
	);

	public static $validations = array(
		'event_id' => array('required' => true, 'integer' => true, 'min' => 1),

		'date_created' => array(),
		'date_updated' => array(),
		
		'num_bets' => array('integer' => true, 'min' => 0),

		'name' => array('required' => true, 'maxlength' => 255),
	);

	public $id;
	public $event_id;
	
	public $date_created;
	public $date_updated;
	
	public $num_bets = 0;
	
	public $name;

	public static function all() {
		return self::find(array(
			'order' => 'name',
		));
	}
	
	public function __toString() {
		return $this->name;
	}
	
	public function is_favorite() {
		$choices = $this->event->choices(array(
			'order' => '-num_bets',
		));
		
		if (count($choices) < 2) {
			return false;
		}
		
		$is_tie = false;
		
		if (
			!empty($choices[1])
			&& $choices[0]->num_bets == $choices[1]->num_bets
		) {
			$is_tie = true;
		}
		
		if (
			$is_tie
			&& (
				$this->id == $choices[0]->id
				|| $this->id == $choices[1]->id
			)
		) { 
			return false;
		}
		
		return ($this->id == $choices[0]->id);
	}
	
	public function is_underdog() {
		$choices = $this->event->choices(array(
			'order' => '-num_bets',
		));
		$count = count($choices);
		
		if ($count < 2) {
			return false;
		}
		
		$is_tie = false;
		
		if (
			!empty($choices[$count - 2])
			&& $choices[$count - 1]->num_bets == $choices[$count - 2]->num_bets
		) {
			$is_tie = true;
		}
		
		if (
			$is_tie
			&& (
				$this->id == $choices[$count - 1]->id
				|| $this->id == $choices[$count - 2]->id
			)
		) { 
			return false;
		}
		
		return ($this->id == $choices[$count - 1]->id);
	}
}
