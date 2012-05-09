<?php
require_once APP_PATH . 'lib/classes/admin_controller_base.php';

class UsersController extends AdminControllerBase {
	public static $title = 'Users';
	
	public $model = 'User';
	public $models = array('User', 'UserRole', 'UserRoleAssignment');
	public $noun = 'user';
	public $hidden_fields = array('password', 'primary_email.password');
	public $linked_fields = array(
		'primary_court_id' => 'courts',
	);
	public $readonly_fields = array('date_created', 'date_updated', 'password');
	
	public $inline_relationships = array(
		'primary_email',
	);
	
	public $search_fields = array(
		'name' => array(
			'admin_url' => array(
				'_section_',
				array('%s'),
				array('id'),
			),
			'sortable' => true,
		),
		'primary_email.email' => array(
			'label' => 'Email',
			'sortable' => true,
			'type' => 'input',
		),
		'points' => array(
			'sortable' => true,
		),
	);
	
	public $actions = array(
		'view' => array(
			'title' => 'View',
			'function' => 'view',
		),
		'edit' => array(
			'title' => 'Edit',
			'function' => 'edit',
		),
		'make-admin' => array(
			'title' => 'Make Admin',
			'function' => 'make_admin',
		),
		'remove-admin' => array(
			'title' => 'Remove Admin',
			'function' => 'remove_admin',
		),
		'reset-password' => array(
			'title' => 'Reset Password',
			'function' => 'reset_password',
		),
		'delete' => array(
			'title' => 'Delete',
			'function' => 'delete',
		),
	);
	
	public $views = array(
		'catalog' => array(
			'title' => 'List',
			'function' => 'catalog',
		),
		'admins' => array(
			'title' => 'Admins',
			'function' => 'admins',
			'conditions' => array(
				'roles.id' => 1,
			),
		),
		'create' => array(
			'title' => 'Create',
			'function' => 'create',
		),
	);
	
	public function _preprocess() {
		parent::_preprocess();
		
		$user_pages = array(
			'view', 'edit', 'delete',
			'reset-password', 'make-admin', 'remove-admin',
		);
		
		if (in_array(Paraglide::$action, $user_pages) && !empty($GLOBALS['arguments'])) {
			$user = $this->_get_item_or_redirect($GLOBALS['arguments'][0]);
			
			if ($user->id != $this->_user->id) {
				if ($user->is_admin()) {
					unset($this->actions['make-admin']);
				} else {
					unset($this->actions['remove-admin']);
				}
			} else {
				unset($this->actions['make-admin']);
				unset($this->actions['remove-admin']);
			}
		} else {
			unset($this->actions['make-admin']);
			unset($this->actions['remove-admin']);
		}
	}
	
	public function admins() {
		$this->catalog();
	}
	
	public function create() {
		$user = new User();
		
		// we set a non-working password
		$user->password = 1;
		
		$this->_create($user);
	}
	
	public function make_admin($id = null) {
		$user = $this->_get_item_or_redirect($id);
		
		if ($user->id == $this->_user->id || $user->is_admin()) {
			Paraglide::redirect('admin/users', null, $user->id);
		}

		$message = 'Are you sure you want to grant admin access to this user?';
		$this->_confirm(array(
			'id' => $user->id,
			'title' => 'Make Admin',
			'message' => $message,
			'function' => 'make_admin',
		));
	}

	public function remove_admin($id = null) {
		$user = $this->_get_item_or_redirect($id);
		
		if ($user->id == $this->_user->id || !$user->is_admin()) {
			Paraglide::redirect('admin/users', null, $user->id);
		}

		$this->_add_breadcrumb($user->name, Paraglide::url('admin/users', null, $user->id));
		$this->_add_breadcrumb('Remove Admin', Paraglide::url('admin/users', 'remove-admin', $user->id));
		$message = 'Are you sure you want to remove admin access from this user?';
		$this->_confirm(array(
			'id' => $user->id,
			'title' => 'Remove Admin',
			'message' => $message,
			'function' => 'remove_admin',
		));
	}

	public function reset_password($id = null) {
		$item = $this->_get_item_or_redirect($id);
		
		if (!empty($_POST)) {
			if (empty($_POST['new_password']) || empty($_POST['verify_password'])) {
				$item->errors['password'] = 'Please enter both password fields';
			} elseif ($_POST['new_password'] != $_POST['verify_password']) {
				$item->errors['password'] = '<strong>New Password</strong> and <strong>Verify Password</strong> must match';
			} else {
				$item->password = User::hash_password($_POST['new_password']);
				$item->save();
				
				if ($item->id == $this->_user->id) {
					$this->_user->login(false);
				}
				
				Paraglide::redirect('admin/users', null, $item->id);
			}
		}
		
		$fieldsets = array(
			'fieldsets' => array(
				'Reset Password' => array(
					'new_password' => array('type' => 'password'),
					'verify_password' => array('type' => 'password'),
				),
			),
		);
		
		$this->_add_breadcrumb($item->name, Paraglide::url('admin/users', null, $item->id));
		$this->_add_breadcrumb('Reset Password', Paraglide::url('admin/users', 'reset-password', $item->id));

		Paraglide::render_view('admin/edit', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'controller' => $this,
			'fields' => $fieldsets,
			'item' => $item,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => $item->{$this->title_field},
			'titleField' => $this->title_field,
			'user' => $this->_user,
		));
	}
}
