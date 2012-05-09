<?php
class UserRolePermission extends Paragon {
	protected static $_table = 'user_role_permissions';
	
	protected static $_belongs_to = array(
		'permission' => array(
			'class' => 'UserPermission',
			'foreign_key' => 'user_permission_id',
		),
		'role' => array(
			'class' => 'UserRole',
			'foreign_key' => 'user_role_id',
		),
	);
	
	public $id;
	public $user_permission_id;
	public $user_role_id;
	
	public function delete() {
		if ($this->permission->name == 'admin' && $this->role->name == 'admin') {
			$this->errors['delete'] = 'You cannot delete the admin permission from the admin role';
			return false;
		}
		
		if ($this->permission->name == 'superadmin' && $this->role->name == 'superadmin') {
			$this->errors['delete'] = 'You cannot delete the superadmin permission from the superadmin role';
			return false;
		}

		return parent::delete();
	}
	
	public function validate() {
		parent::validate();
		
		$existing_role_permission = self::find_one(array(
			'conditions' => array(
				'id' => ActiveRecord::condition('not', $this->id),
				'user_permission_id' => $this->user_permission_id,
				'user_role_id' => $this->user_role_id,
			),
		));
		
		if ($existing_role_permission != null) {
			$this->errors['name'] = 'The permission ' . htmlentities($this->permission->name) . ' is already assigned to this role';
		}
		
		return empty($this->errors);
	}
}
