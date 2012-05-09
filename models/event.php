<?php
class Event extends Paragon {
	protected static $_table = 'events';

	protected static $_belongs_to = array(
		'category' => 'Category',
		'correct_choice' => 'Choice'
	);

	protected static $_has_many = array(
		'bets' => 'Bet',
		'choices' => array(
			'class' => 'Choice',
			'order' => 'name',
		),
	);
	
	public static $validations = array(
		'category_id' => array('required' => true, 'integer' => true, 'min' => 1),
		'correct_choice_id' => array('integer' => true, 'min' => 0),

		'date_created' => array(),
		'date_updated' => array(),
		'date_starts' => array('required' => true, 'datetime' => true),
		'date_ends' => array('required' => true, 'datetime' => true),

		'is_featured' => array('boolean' => true),
		'processed' => array('boolean' => true),
		'status' => array(
			'integer' => true,
			'values' => array(
				0 => 'Canceled',
				1 => 'Public',
				2 => 'Pending',
				3 => 'Over',
			),
		),
		
		'title' => array('required' => true, 'maxlength' => 255),
		'short_title' => array('required' => true, 'maxlength' => 255),
		'slug' => array('required' => true, 'maxlength' => 255),
		'description' => array('required' => true, 'maxlength' => 65535),
		'resolution' => array('maxlength' => 65535),
	);

	public $id;
	public $category_id;
	public $correct_choice_id = 0;
	
	public $date_created;
	public $date_updated;
	public $date_starts;
	public $date_ends;

	public $is_featured = false;
	public $processed = false;
	public $status = 1;
	
	public $amount = 10;
	public $num_bets;
	public $num_comments;
	
	public $title;
	public $short_title;
	public $slug;
	public $description;
	public $resolution = '';
	
	public static function find_by_slug($slug) {
		if (empty($slug)) {
			return null;
		}
		
		return self::find_one(array(
			'conditions' => array(
				'slug' => $slug,
			),
		));
	}
	
	public static function heated($category = null, $page = 1, $limit = 10) {
		$conditions = array(
			'date_starts' => self::condition('lte', date('Y-m-d H:i:s')),
			'date_ends' => self::condition('gt', date('Y-m-d H:i:s')),
			'status' => 1,
		);
		
		if (!empty($category)) {
			$conditions['category_id'] = $category->id;
		}
		
		return self::paginate(array(
			'conditions' => $conditions,
			'order' => '-num_comments',
			'page' => 1,
			'per_page' => $limit,
		));
	}
	
	public static function next($user = null, $ignore_ids = null) {
		$conditions = array(
			'date_starts' => self::condition('lte', date('Y-m-d H:i:s')),
			'date_ends' => self::condition('gt', date('Y-m-d H:i:s')),
			'status' => 1,
		);
		
		if (!empty($ignore_ids)) {
			$conditions['id'] = self::condition('not', $ignore_ids);
		}
		
		$num_events = self::count(array(
			'conditions' => $conditions,
		));
		
		if (empty($num_events)) {
			return false;
		}
		
		$offset = rand(0, $num_events - 1);
		$event = self::find_one(array(
			'conditions' => $conditions,
			'order' => 'id',
			'offset' => $offset,
		));
		
		if (!empty($user)) {
			$bet = $event->bet($user);
			
			if (!empty($bet)) {
				if (empty($ignore_ids)) $ignore_ids = array();
				$ignore_ids[] = $event->id;
				return self::next($user, $ignore_ids);
			}
		}
		
		return $event;
	}
	
	public static function popular($category = null, $page = 1, $limit = 10) {
		$conditions = array(
			'date_starts' => self::condition('lte', date('Y-m-d H:i:s')),
			'date_ends' => self::condition('gt', date('Y-m-d H:i:s')),
			'status' => 1,
		);
		
		if (!empty($category)) {
			$conditions['category_id'] = $category->id;
		}
		
		return self::paginate(array(
			'conditions' => $conditions,
			'order' => '-num_bets',
			'page' => 1,
			'per_page' => $limit,
		));
	}
	
	public static function random_featured($user = null, $ignore_ids = null) {
		$conditions = array(
			'date_starts' => self::condition('lte', date('Y-m-d H:i:s')),
			'date_ends' => self::condition('gt', date('Y-m-d H:i:s')),
			'is_featured' => true,
			'status' => 1,
		);
		
		if (!empty($ignore_ids)) {
			$conditions['id'] = self::condition('not', $ignore_ids);
		}
		
		$num_events = self::count(array(
			'conditions' => $conditions,
		));
		
		if (empty($num_events)) {
			return false;
		}
		
		$offset = rand(0, $num_events - 1);
		$event = self::find_one(array(
			'conditions' => $conditions,
			'order' => 'id',
			'offset' => $offset,
		));
		
		if (!empty($user)) {
			$bet = $event->bet($user);
			
			if (!empty($bet)) {
				if (empty($ignore_ids)) $ignore_ids = array();
				$ignore_ids[] = $event->id;
				return self::random_featured($user, $ignore_ids);
			}
		}
		
		return $event;
	}
	
	public static function recent($category = null, $page = 1, $limit = 10) {
		$conditions = array(
			'date_starts' => self::condition('lte', date('Y-m-d H:i:s')),
			'date_ends' => self::condition('gt', date('Y-m-d H:i:s')),
			'status' => 1,
		);
		
		if (!empty($category)) {
			$conditions['category_id'] = $category->id;
		}
		
		return self::paginate(array(
			'conditions' => $conditions,
			'order' => 'date_created',
			'page' => 1,
			'per_page' => $limit,
		));
	}
	
	public function cancel() {
		return true;
	}
	
	public function bet($user) {
		if (empty($user)) {
			return null;
		}
		
		$bets = $this->bets(array(
			'conditions' => array(
				'user_id' => $user->id,
			),
		));
		return !empty($bets) ? $bets[0] : null;
	}
	
	public function is_canceled() {
		return ($this->status == 0);
	}
	
	public function save() {
		if (empty($this->slug) && !empty($this->title)) {
			$this->slug = strtolower($this->title);
			$this->slug = preg_replace('/[^a-z0-9]/', '-', $this->slug);
			
			while (strpos($this->slug, '--') > -1) {
				$this->slug = preg_replace('/\-\-/', '-', $this->slug);
			}

			if (substr($this->slug, 0, 1) == '-') $this->slug = substr($this->slug, 1);
			if (substr($this->slug, -1) == '-') $this->slug = substr($this->slug, 0, -1);
		}
		
		return parent::save();
	}
	
	public function time_left() {
		$date_parts1 = explode('-', date('Y-m-d', strtotime($this->date_ends)));
		$date_parts2 = explode('-', date('Y-m-d'));
		$start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
		$end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
		$days = abs($end_date - $start_date);
		$years = floor($days / 365);
		$days -= $years * 365;

		$time1 = strtotime(date('2000-m-2 H:i:s', strtotime($this->date_ends)));
		$time2 = strtotime(date('2000-m-1 H:i:s'));
		$seconds = abs($time1 - $time2);
		$minutes = floor($seconds / 60);
		$seconds -= $minutes * 60;
		$hours = floor($minutes / 60);
		$minutes -= $hours * 60;
		
		$time_left = "{$days}:{$hours}:{$minutes}:{$seconds}";
		if (!empty($years)) $time_left = $years . ':' . $time_left;
		return $time_left;
	}
	
	public function validate() {
		if (!empty($this->slug)) {
			$event = self::find_by_slug($this->slug);
			
			if (!empty($event) && $event->id != $this->id) {
				$this->errors['slug'] = 'This slug is already taken';
			}
		}
		
		return empty($this->errors);
	}
}
