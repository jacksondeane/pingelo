<?php
class Category extends Paragon {
	protected static $_table = 'categories';

	protected static $_has_many = array(
		'events' => array(
			'class' => 'Event',
			'order' => 'date_starts',
		),
	);

	public static $validations = array(
		'date_created' => array(),
		'date_updated' => array(),
		
		'is_visible' => array('boolean' => true),
		
		'num_bets' => array('integer' => true, 'min' => 0),
		
		'name' => array('required' => true, 'maxlength' => 255),
		'slug' => array('required' => true, 'maxlength' => 255),
	);

	public $id;

	public $date_created;
	public $date_updated;
	
	public $is_visible = true;
	
	public $num_bets = 0;
	
	public $name;
	public $slug;

	public static function all() {
		return self::find(array(
			'conditions' => array(
				'is_visible' => true,
			),
			'index' => 'name',
			'order' => 'name',
		));
	}
	
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
	
	public static function popular($page = 1, $limit = 10) {
		return self::paginate(array(
			'order' => '-num_bets',
			'page' => 1,
			'per_page' => $limit,
		));
	}
	
	public function __toString() {
		return $this->name;
	}
	
	public function save() {
		if (empty($this->slug) && !empty($this->name)) {
			$this->slug = strtolower($this->name);
			$this->slug = preg_replace('/[^a-z0-9]/', '-', $this->slug);

			while (strpos($this->slug, '--') > -1) {
				$this->slug = preg_replace('/\-\-/', '-', $this->slug);
			}

			if (substr($this->slug, 0, 1) == '-') $this->slug = substr($this->slug, 1);
			if (substr($this->slug, -1) == '-') $this->slug = substr($this->slug, 0, -1);
		}
		
		return parent::save();
	}
	
	public function validate() {
		if (!empty($this->slug)) {
			$category = self::find_by_slug($this->slug);
			
			if (!empty($category) && $category->id != $this->id) {
				$this->errors['slug'] = 'This slug is already taken';
			}
		}
		
		return empty($this->errors);
	}
}
