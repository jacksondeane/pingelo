<?php
class UserPermission extends Paragon {
	protected static $_table = 'user_permissions';
	
	protected static $_has_many = array(
		'role_permissions' => array(
			'class' => 'UserRolePermission',
			'primary_key' => 'user_permission_id',
		),
	);

	public $id;
	
	public $name;
	
	public static function refresh_permissions() {
		$admin_path = realpath(dirname(__FILE__) . '/..') . '/controllers/admin/*_controller.php';
		$admin_files = glob($admin_path);
		
		foreach ($admin_files as $file) {
			$section = substr(basename($file), 0, 0 - strlen('_controller.php'));
			$controller = str_replace(' ', '', ucwords(str_replace(array('_', ' '), ' ', $section)));
			$permission_name = 'admin.' . $controller;

			$permission = self::find(array(
				'conditions' => array(
					'name' => $permission_name,
				),
			));
			
			if ($permission == null) {
				$permission = new UserPermission();
				$permission->name = $permission_name;
				$permission->save();
			}
		}
	}
	
	public function delete() {
		$role_permissions = $this->role_permissions;
		
		foreach ($role_permissions as $role_permission) {
			$role_permission->delete();
		}
		
		return parent::delete();
	}
}
