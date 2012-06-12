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
	
	public $elo_rank = 1000;
	public $twitter_username;
	public $profile_image_url;
	
	public $num_games;
	public $num_wins;
	
	public static function get_leaders() {
		$leaders = self::find(array(
			'order' => '-elo_rank',
		));
		
		return $leaders;
	}
	
	private static function _base_convert($numstring, $frombase, $tobase) {
		$chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$tostring = substr($chars, 0, $tobase);

		$length = strlen($numstring);
		$result = '';

		for ($i = 0; $i < $length; $i++) {
			$number[$i] = strpos($chars, $numstring{$i});
		}
		
		do {
			$divide = 0;
			$newlen = 0;
			
			for ($i = 0; $i < $length; $i++) {
				$divide = $divide * $frombase + $number[$i];
				
				if ($divide >= $tobase) {
					$number[$newlen++] = (int)($divide / $tobase);
					$divide = $divide % $tobase;
				} elseif ($newlen > 0) {
					$number[$newlen++] = 0;
				}
			}
			
			$length = $newlen;
			$result = $tostring{$divide} . $result;
		}
		while ($newlen != 0);
		
		return $result;
	}
	
	private static function _verification_code_data($verification_code) {
		$base17 = self::_base_convert($verification_code, 36, 30);
		$string = self::_base_convert($base17, 17, 36);

		if (substr($string, 0, strlen('verify')) != 'verify') {
			return null;
		}
		
		$string = substr($string, strlen('verify'));
		$parts = explode('email', $string, 2);
		
		if (count($parts) == 1) {
			return null;
		}
		
		$user_id = $parts[0];
		$email_id = $parts[1];
		$user = User::find($user_id);
		
		if ($user == null) {
			return null;
		}
		
		$email = UserEmail::find($email_id);
		
		if ($email == null) {
			return null;
		}
		
		return array('user' => $user, 'email' => $email);
	}
	
	private function _email() {
		$email = $this->primary_email;
		return ($email != null) ? $email->email : '';
	}
	
	private function _email_set($value) {
		$email = $this->primary_email;
		
		if (empty($email)) {
			$email = new UserEmail();
		}
		
		$email->user_id = $this->id;
		$email->email = $value;
		$email->save();
		
		if (empty($this->primary_email_id)) {
			$this->primary_email_id = $email->id;
			$this->save();
		}
	}
	
	private function _first_name() {
		return reset(explode(' ', $this->name));
	}

	private function _last_name() {
		return end(explode(' ', $this->name));
	}
	
	private function _verification_code($email = null) {
		if (empty($email)) $email = $this->primary_email;

		if (empty($email)) {
			return null;
		}
		
		$string = 'verify' . $this->id . 'email' . $email->id;
		$base17 = self::_base_convert($string, 36, 17);
		$alphanumeric = self::_base_convert($base17, 30, 36);
		return $alphanumeric;
	}
	
	public static function find_by_email($email) {
		$user_email = UserEmail::find_one(array(
			'conditions' => array('email' => $email)
		));
		return ($user_email != null) ? $user_email->user : null;
	}

	public static function find_by_username($username) {
		return User::find_one(array(
			'conditions' => array('username' => $username)
		));
	}

	public static function find_by_twitter_username($twitter_username) {
		return User::find_one(array(
			'conditions' => array('twitter_username' => $twitter_username)
		));
	}

	
	public static function find_by_verification_code($verification_code) {
		$data = self::_verification_code_data($verification_code);
		return ($data != null) ? $data['user'] : null;
	}
	
	public static function forgot_password($email) {
		$result = array(
			'errors' => array(),
			'result' => false,
		);
		
		if (!Validator::check_email($email)) {
			$result['errors']['email'] = 'Please enter a valid email address';
			return $result;
		}
			
		$user = self::find_by_email($email);
		
		if ($user == null) {
			$result['errors']['email'] = 'We couldn\'t find an account with that email address';
			return $result;
		}
		
		$company_conf = Web::parse_config('company');
		$mail_conf = Web::parse_config('mail');

		$message = Web::render_view('account/email.send-password', array(
			'company' => $company_conf['main']['name'],
			'email' => $user->email,
			'forgot_user' => $user,
		), true);
		
		require_once APP_PATH . 'lib/helpers/mailer.php';
		
		try {
			Mailer::send(array(
				'to' => $user->email,
				'subject' => $company_conf['main']['name'] . ' - Reset Password',
				'message' => $message,
			));
		} catch (Exception $e) {
			$result['errors'][] = 'There was an error sending the reset password email';
			return $result;
		}

		$result['result'] = true;
		$result['email'] = $user->email;
		return $result;
	}
	
	public static function hash_password($password) {
		return md5(md5($password) . $password . strrev($password) . md5(strrev($password)));
	}

	public static function logged_in_user() {
		if (empty($_COOKIE['user_id']) || empty($_COOKIE['password'])) {
			return null;
		}
		
		$user = User::find($_COOKIE['user_id']);

		if ($user == null || md5($user->password) != $_COOKIE['password']) {
			return null;
		}
		
		$facebook_user = $user->facebook_user;
		
		if ($facebook_user && !$facebook_user->is_logged_in()) {
			return null;
		}
		
		
		if (
			!$user->verified
			&& (
				Web::$controller != 'account'
				|| (
					Web::$action != 'verify'
					&& Web::$action != 'logout'
				)
			)
		) {
			Web::redirect('account', 'verify');
		}
		
		$user->check_daily_points();
		return $user;
	}
	
	public static function require_login() {
		$user = User::logged_in_user();
		
		if (!empty($user)) {
			return true;
		}

		$get = null;
		
		if (!empty($_SERVER['REQUEST_URI'])) {
			$get = array('return' => $_SERVER['REQUEST_URI']);
		}
		
		Web::redirect('account', 'login', null, $get);
	}
	
	public static function top($page = 1, $limit = 10) {
		return self::paginate(array(
			'order' => '-num_bets',
			'page' => 1,
			'per_page' => $limit,
		));
	}
	
	public static function try_login($email, $password, $redirect = true) {
		if (empty($email)) {
			return 'Please enter an <strong>email address</strong>';
		} elseif (empty($password)) {
			return 'Please enter a <strong>password</strong>';
		}
	
		$user = User::find_by_email($email);

		if ($user == null) {
			return 'We could not find an account with that email address';
		}
		
		if (!$user->active) {
			return 'We could not find an active account with that email address';
		}
		
		$hashed_password = User::hash_password($password);

		if ($hashed_password != $user->password) {
			return 'You entered an incorrect password';
		}

		return $user->login($redirect);
	}
	
	public function __get($field) {
		if ($field == 'email') {
			return $this->_email();
		} elseif ($field == 'first_name') {
			return $this->_first_name();
		} elseif ($field == 'last_name') {
			return $this->_last_name();
		} elseif ($field == 'verification_code') {
			return $this->_verification_code();
		}
		
		return parent::__get($field);
	}
	
	public function __set($field, $value) {
		if ($field == 'email') {
			return $this->_email_set($value);
		}
		
		return null;
	}
	
	public function __toString() {
		return $this->name;
	}
	
	public function check_daily_points() {
		// if today is the user's first day, they don't get daily points
		if (date('Y-m-d') == date('Y-m-d', strtotime($this->date_created))) {
			return false;
		}
		
		$existing_daily_deposit = $this->total('transactions', array(
			'date_created' => self::condition('gte', date('Y-m-d')),
			'type' => 1,
		));
		
		if (!empty($existing_daily_deposit)) {
			return false;
		}
		
		Transaction::create(array(
			'user_id' => $this->id,
			'type' => 1,
			'amount' => 0,
		));
	}

	public function delete() {
		$roles = $this->roles;
		
		foreach ($roles as $role) {
			$this->remove_relationship('roles', $role);
		}
		
		$emails = $this->emails;
		
		foreach ($this->emails as $email) {
			$email->delete();
		}
		
		return parent::delete();
	}

	public function has_permission($permission) {
		$roles = $this->roles;

		foreach ($roles as $role) {
			$num_permissions = $role->total('permissions', array(
				'name' => array($permission, 'superadmin'),
			));
			
			if ($num_permissions > 0) {
				return true;
			}
		}

		return false;
	}
	
	public function initial_deposit() {
		$existing_initial_deposit = $this->total('transactions', array(
			'type' => 0,
		));
		
		if (!empty($existing_initial_deposit)) {
			return false;
		}
		
		Transaction::create(array(
			'user_id' => $this->id,
			'type' => 0,
			'amount' => 1,
		));
	}
	
	public function is_admin() {
		return $this->has_permission('admin');
	}

	public function login($redirect = true) {
		setcookie('user_id', $this->id, null, SITE_ROOT);
		setcookie('password', md5($this->password), null, SITE_ROOT);

		if (!$redirect) {
			return;
		}
		
		if (is_string($redirect)) {
			Web::redirect_to($redirect);
		}

		Web::redirect('account');
	}
	
	public function logout($redirect = true) {
		setcookie('user_id', false, time() - 3600, SITE_ROOT);
		setcookie('password', false, time() - 3600, SITE_ROOT);

		if (!$redirect) {
			return;
		}
		
		if (is_string($redirect)) {
			Web::redirect($redirect);
		}
		
		Web::redirect('account');
	}
		
	public function register($data) {
		$user_email = new UserEmail();
		$this->set_values($data);
		$this->validate();
		$this->validate_passwords($data);
		
		if ($user_email->validate_emails($data)) {
			$user_email->email = $data['email'];
			$user_email->validate();
		}
		
		if (!empty($user_email->errors)) {
			$this->errors = array_merge($this->errors, $user_email->errors);
		}

		if (!empty($this->errors)) {
			return false;
		}
		
		$this->active = 1;
		
		if (!$this->save()) {
			return false;
		}
		
		$user_email->user_id = $this->id;
		$user_email->save();
		
		$this->primary_email_id = $user_email->id;
		$this->save();
		
		$this->send_email_verification();
		return true;
	}
	
	public function reset_password($data) {
		if (empty($data['password'])) {
			$this->errors['password'] = 'Please enter the <strong>Password</strong> field';
		}
		
		if (empty($data['password_verify'])) {
			$this->errors['password_verify'] = 'Please enter the <strong>Verify Password</strong> field';
		}
		
		if (!empty($this->errors)) {
			return false;
		}
		
		if ($data['password'] != $data['password_verify']) {
			$this->errors['password_verify'] = 'Your <strong>Password</strong> and <strong>Verification Password</strong> do not match';
			return false;
		}
		
		$this->password = self::hash_password($data['password']);
		$this->save();
		return true;
	}

	public function save() {
		if (empty($this->username) && !empty($this->name)) {
			$i = 0;
			
			do {
				$this->username = strtolower($this->name);
				$this->username = preg_replace('/[^a-z0-9]/', '-', $this->username);
				
				while (strpos($this->username, '--') > -1) {
					$this->username = preg_replace('/\-\-/', '-', $this->username);
				}
				
				if (substr($this->username, 0, 1) == '-') $this->username = substr($this->username, 1);
				if (substr($this->username, -1) == '-') $this->username = substr($this->username, 0, -1);
				
				if ($i > 0) $this->username .= $i;
				
				$user = self::find_by_username($this->username);
			} while (!empty($user) && $user->id != $this->id);
		} elseif (!empty($this->username)) {
			$this->username = strtolower($this->username);
			$this->username = preg_replace('/[^a-z0-9]/', '-', $this->username);

			while (strpos($this->username, '--') > -1) {
				$this->username = preg_replace('/\-\-/', '-', $this->username);
			}
			
			if (substr($this->username, 0, 1) == '-') $this->username = substr($this->username, 1);
			if (substr($this->username, -1) == '-') $this->username = substr($this->username, 0, -1);
		}
	
		$new_account = empty($this->id);
		parent::save();
		
		if (!empty($new_account)) {
			$this->initial_deposit();
		}
	
		if (!empty($this->primary_email_id)) {
			if (!$this->primary_email->validate()) {
				$this->errors['email'] = $this->primary_email->errors['email'];
				return false;
			}
		}

		if (!parent::save()) {
			return false;
		}
		
		if (!empty($this->primary_email->id)) {
			$this->primary_email->save();
		}

		return true;
	}
	
	public function send_email_verification() {
		$company_conf = Web::parse_config('company');
		$mail_conf = Web::parse_config('mail');

		$message = Web::render_view('account/email.send-verification', array(
			'company' => $company_conf['main']['name'],
			'email' => $this->email,
			'new_user' => $this,
		), true);
		
		require_once APP_PATH . 'lib/helpers/mailer.php';
		Mailer::send(array(
			'to' => $this->email,
			'subject' => $company_conf['main']['name'] . ' - Email Verification',
			'message' => $message,
		));
		return true;
	}
	
	public function set_values($vars, $params = array()) {
		if (!empty($vars['email']) && !empty($this->primary_email_id)) {
			$this->primary_email->email = $vars['email'];
		}
		
		return parent::set_values($vars, $params);
	}
	
	public function to_output_array() {
		$output = array();
		$fields = $this->fields();
		foreach ($fields as $field) $output[$field] = $this->$field;
		
		unset($output['date_created']);
		unset($output['date_updated']);
		unset($output['password']);
		
		return $output;
	}
	
	public function validate() {
		if (!empty($this->username)) {
			$user = self::find_by_username($this->username);
			
			if (!empty($user) && $user->id != $this->id) {
				$this->errors['username'] = 'This username is already taken';
			}
		}
		
		return empty($this->errors);
	}
	
	public function validate_passwords($vars) {
		if (empty($vars['password'])) {
			$this->errors['password'] = 'Please enter the <strong>Password</strong> field';
		} elseif (!empty($vars['password']) && empty($vars['password_verify'])) {
			$this->errors['password_verify'] = 'Please enter the <strong>Verification Password</strong> field';
		} elseif (!empty($vars['password']) && $vars['password'] != $vars['password_verify']) {
			$this->errors['password'] = 'Your <strong>Verification Password</strong> does not match your <strong>Password</strong>';
		}
	
		$this->password = self::hash_password($this->password);
		return empty($this->errors);
	}
}
