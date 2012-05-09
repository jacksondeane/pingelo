<?php
class UserRoleAssignment extends Paragon {
	protected static $_table = 'user_role_assignments';
	
	protected static $_belongs_to = array(
		'role' => array(
			'class' => 'UserRole',
			'foreign_key' => 'user_role_id',
		),
		'user' => 'User',
	);
	
	public $id;
	public $user_id;
	public $user_role_id;
	
	public function validate() {
		parent::validate();
		
		$existing_role_assignment = self::find_one(array(
			'conditions' => array(
				'user_id' => $this->user_id,
				'user_role_id' => $this->user_role_id,
			),
		));
		
		if (!empty($existing_role_assignment)) {
			$this->errors['user_id'] = 'This user already has the role ' . $this->role->name;
		}
	
		return empty($this->errors);
	}
}
