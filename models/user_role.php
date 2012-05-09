<?php
class UserRole extends Paragon {
	protected static $_table = 'user_roles';
	
	protected static $_has_many = array(
		'role_permissions' => 'UserRolePermission',
	);
	
	protected static $_has_and_belongs_to_many = array(
		'permissions' => array(
			'class' => 'UserPermission',
			'table' => 'user_role_permissions',
			'foreign_key' => 'user_permission_id',
			'primary_key' => 'user_role_id',
		),
		'users' => array(
			'class' => 'User',
			'table' => 'user_role_assignments',
			'foreign_key' => 'user_id',
			'primary_key' => 'user_role_id',
		),
	);
	
	public $id;
	
	public $name;
	
	private function _permissions_string() {
		$permission_strings = array();
		$permissions = $this->permissions();
		foreach ($permissions as $permission) $permission_strings[] = $permission->name;
		sort($permission_strings);
		return implode(', ', $permission_strings);
	}
	
	public function __get($field) {
		if ($field == 'permissions_string') {
			return $this->_permissions_string();
		}
		
		return parent::__get($field);
	}
	
	public function available_permissions() {
		$user_permissions = $this->permissions;
		$user_permission_ids = array();
		foreach ($user_permissions as $permission) $user_permission_ids[] = $permission->id;
		
		$available_permissions = UserPermission::find(array(
			'conditions' => array(
				'id' => ActiveRecord::condition('not', $user_permission_ids),
			),
			'order' => 'name',
		));
		return $available_permissions;
	}
	
	public function available_permission_values() {
		$available_permissions = $this->available_permissions();
		$values = array();
		foreach ($available_permissions as $permission) $values[$permission->id] = $permission->name;
		return $values;
	}

	public function delete() {
		if ($this->name == 'admin') {
			$this->errors['delete'] = 'You cannot delete the admin role';
			return false;
		}
		
		if ($this->name == 'superadmin') {
			$this->errors['delete'] = 'You cannot delete the superadmin role';
			return false;
		}
	
		$role_permissions = $this->role_permissions;
		
		foreach ($role_permissions as $role_permission) {
			$role_permission->delete();
		}
		
		return parent::delete();
	}
	
	public function validate() {
		parent::validate();
		
		$existing_role = self::find_one(array(
			'conditions' => array(
				'id' => ActiveRecord::condition('not', $this->id),
				'name' => $this->name,
			),
		));
		
		if ($existing_role != null) {
			$this->errors['name'] = 'A role already exists with this name';
		}
		
		return empty($this->errors);
	}
}
