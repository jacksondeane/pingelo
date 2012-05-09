<?php
class Prize extends Paragon {
	protected static $_table = 'prizes';
	
	public static $validations = array(
		'date_created' => array(),
		'date_updated' => array(),
		
		'is_active' => array('boolean' => true),
		
		'amount' => array('required' => true, 'min' => 1),
		'quantity' => array('required' => true, 'min' => 0),
		
		'title' => array('required' => true, 'maxlength' => 255),
		'slug' => array('required' => true, 'maxlength' => 255),
		'description' => array('required' => true, 'maxlength' => 65535),
	);

	public $id;
	
	public $date_created;
	public $date_updated;
	
	public $is_active = true;
	
	public $amount;
	public $quantity;
	
	public $title;
	public $slug;
	public $description;
	
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
	
	public function save($data) {
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
	
	public function validate() {
		if (!empty($this->slug)) {
			$prize = self::find_by_slug($this->slug);
			
			if (!empty($prize) && $prize->id != $this->id) {
				$this->errors['slug'] = 'This slug is already taken';
			}
		}
		
		return empty($this->errors);
	}
}
