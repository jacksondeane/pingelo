<?php
/*********
 File Name: user_email.php
 Description: This file is model for user_emails table and is useful in getting info of user email id
 ***********/
class UserEmail extends Paragon {
	protected static $_table = 'user_emails';
	
	protected static $_belongs_to = array(
		'user' => 'User',
	);
	
	public static $validations = array(
		'user_id' => array(),
		
		'active' => array('boolean' => true),
		
		'email' => array('required' => true, 'email' => true, 'maxlength' => 255),
	);
	
	public $id;
	public $user_id;
	
	public $active = true;
	
	public $email;
	
	public function __toString() {
		return $this->email;
	}

	public function validate() {
		parent::validate();
		
		$existing_email = self::find_one(array(
			'conditions' => array(
				'email' => $this->email,
			),
		));
		
		if (!empty($existing_email) && $this->id != $existing_email->id) {
			$this->errors['email'] = 'Another user already has this email address';
		}
		
		// we make a special case to allow admin as an email address
		if ($this->email == 'admin' && !empty($this->errors['email'])) {
			unset($this->errors['email']);
		}
		
		return empty($this->errors);
	}
	
	public function validate_emails($vars) {
		if (empty($vars['email'])) {
			$this->errors['email'] = 'Please enter the <strong>Email</strong> field';
		} elseif (!empty($vars['email']) && empty($vars['email_verify'])) {
			$this->errors['email_verify'] = 'Please enter the <strong>Verification Email</strong> field';
		} elseif (!empty($vars['email']) && $vars['email'] != $vars['email_verify']) {
			$this->errors['email'] = 'Your <strong>Verification Email</strong> does not match your <strong>Email</strong>';
		}

		return empty($this->errors);
	}
}
