<?php
class AdminControllerBase {
	protected $_breadcrumbs;
	protected $_section;
	protected $_sections = array();
	protected $_tabs;
	protected $_user;
	
	public static $tab_position = 0;
	public static $title = 'Items';
	
	public $actions = array(
		'view' => array(
			'title' => 'View',
			'function' => 'view',
		),
		'edit' => array(
			'title' => 'Edit',
			'function' => 'edit',
		),
		'delete' => array(
			'title' => 'Delete',
			'function' => 'delete',
		),
	);
	
	public $default_view = 'catalog';
	public $model = 'Item';
	public $noun = 'item';
	public $extra_fields = array();
	public $linked_fields = array();
	public $hidden_fields = array();
	public $inline_relationships = array();
	public $option_fields = array();
	public $readonly_fields = array('date_created', 'date_updated');
	public $wysiwyg_fields = array();
	public $search_fields = array(
		'_title_' => array(
			'admin_url' => array(
				'_section_',
				array('%s'),
				array('id'),
			),
			'sortable' => true,
		),
		'date_created' => array(
			'datetime' => true,
			'sortable' => true,
		),
		'date_updated' => array(
			'datetime' => true,
			'sortable' => true,
		),
	);
	public $title_field = 'name';
	public $views = array(
		'catalog' => array(
			'title' => 'List',
			'function' => 'catalog',
		),
		'create' => array(
			'title' => 'Create',
			'function' => 'create',
		),
	);
	
	public $helpers = array('Form', 'HtmlTable');
	public $models = array('User');
	
	private function _get_fields($class, $item, $view = 'view') {
		$fieldValues = array();
		$primary_key = 'id';
		
		if (!empty($item->$primary_key)) {
			$fieldValues[$primary_key] = $item->primary_key;
		}
		
		if (!empty($class->title_field)) {
			$fieldValues[$class->title_field] = $item->{$class->title_field};
		}
	
		$model = get_class($item);
		$vars = get_class_vars($class->model);
		
		if (array_key_exists('validations', $vars)) {
			$validations = $vars['validations'];
			unset($vars['validations']);
		} else {
			$validations = array();
		}

		foreach ($vars as $var => $val) {
			if ($var == $class->title_field) {
				continue;
			}
			
			if ($var == $class->title_field . '_id') {
				continue;
			}
			
			if ($item->$var === null && $item->$primary_key !== null) {
				continue;
			}
			
			if ($item->$var !== null && !is_scalar($item->$var)) {
				continue;
			}
			
			if (in_array($var, $class->hidden_fields)) {
				continue;
			}
			
			if (empty($item->$var)) {
				if ($var == $primary_key) {
					continue;
				}
				
				if ($var == 'date_created' || $var == 'date_updated') {
					continue;
				}
				
				if (in_array($var, $class->readonly_fields)) {
					continue;
				}
			}

			$fieldValues[$var] = $item->$var;
		}

		if (!empty($class->extra_fields)) {
			foreach ($class->extra_fields as $field => $where) {
				if ($where == '_field_') {
					$val = $item->$field;
				} elseif ($where == '_function_') {
					$val = $item->$field();
				} else {
					$val = $class->$where($item);
				}
				
				$fieldValues[$field] = $val;
			}
		}
		
		$fields = array();
		$options = !empty($class->field_options) ? $class->field_options : array();
		$relationships = Paragon::_get_static(get_class($item), '_relationships');
		
		foreach ($fieldValues as $field => $value) {
			$original_field = $field;
		
			if (!empty($class->inline_relationship)) {
				$label = ucwords(str_replace('_', ' ', $field));
				$field = strtolower($class->inline_relationship) . '.' . $field;
			} else {
				$label = null;
			}
		
			$fields[$field] = array(
				'value' => $value,
			);
			
			if (in_array($original_field, $class->readonly_fields)) {
				$fields[$field]['type'] = 'text';
			}

			if (!empty($class->wysiwyg_fields) && in_array($field, $class->wysiwyg_fields) && $view == 'edit') {
				if (!empty($fields[$field]['class'])) {
					$fields[$field]['class'] .= ' newline wysiwyg';
				} else {
					$fields[$field]['class'] = 'newline wysiwyg';
				}
			}
			
			if (!empty($label) && empty($fields[$field]['label'])) {
				$fields[$field]['label'] = $label;
			}
			
			if ($original_field == 'id') {
				$fields[$field]['type'] = 'text';
			}
			
			if (substr($field, -3) == '_id') {
				$field_without_id = substr($field, 0, -3);

				if (empty($relationships['belongs_to'][$field_without_id])) {
					unset($fields[$field]);
					continue;
				}
				
				if (in_array($field_without_id, $class->inline_relationships)) {
					unset($fields[$field]);
					continue;
				}
				
				if (empty($fields[$field]['label'])) {
					$field_words = explode('_', $field_without_id);
					$label = ucwords(str_replace('_', ' ', $field_without_id));
					$fields[$field]['label'] = $label;
				}
				
				if (
					$view == 'view'
					|| (
						$view == 'edit'
						&& in_array($field, $this->readonly_fields)
					)
				) {
					$value_object = $item->$field_without_id;
					
					if (!empty($value_object)) {
						$fields[$field]['value'] = (string) $value_object;
					
						if (!empty($class->linked_fields[$field])) {
							$fields[$field]['url_format'] = array(
								Paraglide::url('admin/' . $class->linked_fields[$field], null, $value_object->__primary_key__),
								array(),
							);
						}
					} else {
						$fields[$field]['value'] = '(none)';
					}
				} elseif ($view == 'edit') {
					$relationship = $relationships['belongs_to'][$field_without_id];
					$model = $relationship['class'];
					
					if (!empty($this->option_fields[$field])) {
						$value_objects_count = $item->total($this->option_fields[$field], $conditions);
					} else {
						$value_objects_count = call_user_func(array($model, 'count'), array(
							'start' => 0,
						));
					}
					
					$value_object = $item->$field_without_id;

					if ($value_objects_count > 200) {
						$fields[$field]['type'] = 'input';
						$fields[$field]['value'] = $item->$field;
						$fields[$field]['class'] = 'relationship-selector';
						$fields[$field]['title'] = $model;
						
						if (!empty($value_object)) {
							$fields[$field]['id'] = 'item-selected-' . htmlspecialchars(urlencode($value_object));
						}
					} else {
						$conditions = array();
						
						if (property_exists($model, 'user_id')) {
							$conditions['user_id'] = $this->_user->id;
						}

						if (!empty($this->option_fields[$field])) {
							$value_objects = $item->{$this->option_fields[$field]}(array(
								'conditions' => $conditions,
								'start' => 0,
							));
						} else {
							$value_objects = call_user_func(array($model, 'find'), array(
								'conditions' => $conditions,
								'start' => 0,
							));
						}
						
						$values = array('' => '');
						
						foreach ($value_objects as $object) {
							$values[$object->__primary_key__] = (string) $object;
						}
						
						$fields[$field]['type'] = 'dropdown';
						$fields[$field]['values'] = $values;
		
						if (!empty($value_object)) {
							$fields[$field]['value'] = $value_object->__primary_key__;
						}
					}
				}
			} else {
				if (!empty($class->linked_fields[$field])) {
					$fields[$field]['url_format'] = array(
						'%s',
						array($class->linked_fields[$field]),
					);
				}
			}
			
			if (!empty($validations[$original_field])) {
				if (isset($validations[$original_field]['boolean'])) {
					$fields[$field]['boolean'] = $validations[$original_field]['boolean'];
					
					if ($view == 'edit') {
						$fields[$field]['type'] = 'checkbox';
						$fields[$field]['checked'] = !empty($value);
						$fields[$field]['value'] = 1;
					}
				}
			
				if (isset($validations[$original_field]['date'])) {
					$fields[$field]['date'] = $validations[$original_field]['date'];
				}
				
				if (isset($validations[$original_field]['datetime'])) {
					$fields[$field]['datetime'] = $validations[$original_field]['datetime'];
				}
				
				if (!empty($validations[$original_field]['maxlength']) && $validations[$original_field]['maxlength'] > 255) {
					$fields[$field]['type'] = 'textarea';
					
					if ($validations[$original_field]['maxlength'] > 2056) {
						$fields[$field]['rows'] = 16;
						$fields[$field]['cols'] = 110;
					}
				}
				
				if (isset($validations[$original_field]['timestamp'])) {
					$fields[$field]['timestamp'] = $validations[$original_field]['timestamp'];
				}
				
				if (isset($validations[$original_field]['values'])) {
					if (
						$view == 'view'
						|| (
							$view == 'edit'
							&& in_array($field, $this->readonly_fields)
						)
					) {
						if (!empty($validations[$original_field]['values'][$item->$field])) {
							$fields[$field]['value'] = $validations[$original_field]['values'][$item->$field];
						}
					} elseif ($view == 'edit') {
						$fields[$field]['type'] = 'dropdown';
						$fields[$field]['values'] = $validations[$original_field]['values'];
					}
				}
			}
			
			if (!empty($options[$field])) {
				$field_options = method_exists(get_class($class), $options[$field]) ? $class->{$options[$field]}() : $options[$field];
			
				if (!empty($field_options['url_format'])) {
					$fields[$field]['url_format'] = $field_options['url_format'];
				}
			}
			
			if (!empty($fields[$field]['date']) || !empty($fields[$field]['datetime']) || !empty($fields[$field]['timestamp'])) {
				if (!empty($fields[$field]['class'])) {
					$fields[$field]['class'] = $fields[$field]['class'] . ' date';
				} else {
					$fields[$field]['class'] = 'date';
				}
			}
		}

		return $fields;
	}
	
	private function _get_fieldsets($class, $item, $view = 'view') {
		$fields = $this->_get_fields($class, $item, $view);
		$fieldsets = array(
			ucwords(str_replace('_', ' ', $class->noun)) => $fields,
		);

		if (!empty($class->inline_relationships)) {
			foreach ($class->inline_relationships as $relationship) {
				$instance = $item->$relationship;
				
				if ($instance == null) {
					$relationships = Paragon::_get_static(get_class($item), '_relationships');
					
					if (!empty($relationships['belongs_to'][$relationship])) {
						$info = $relationships['belongs_to'][$relationship];
						$instance = new $info['class'];
					}
				}
				
				$admin = new stdClass();
				$admin->inline_relationships = array();
				$admin->inline_relationship = $relationship;
				$admin->model = get_class($instance);
				$admin->hidden_fields = array();
				$admin->readonly_fields = array();
				$admin->title_field = null;
				
				foreach ($class->hidden_fields as $field) {
					if (substr($field, 0, strlen($relationship . '.')) == $relationship . '.') {
						$admin->hidden_fields[] = substr($field, strlen($relationship . '.'));
					}
				}
				
				foreach ($class->readonly_fields as $field) {
					if (substr($field, 0, strlen($relationship . '.')) == $relationship . '.') {
						$admin->readonly_fields[] = substr($field, strlen($relationship . '.'));
					}
				}
				
				$fields = $this->_get_fields($admin, $instance, $view);
				$fieldsets[ucwords(str_replace('_', ' ', $relationship))] = $fields;
			}
		}

		return array('fieldsets' => $fieldsets);
	}
	
	private function _process_class() {
		foreach ($this->search_fields as $field => $info) {
			if ($field == '_title_') {
				$this->search_fields[$field]['field'] = $this->title_field;
			}
			
			if (!empty($info['admin_url'])) {
				$url_section = $info['admin_url'][0];
				if ($url_section == '_section_') $url_section = $this->_section;
				$url_params = array_merge(array($url_section), $info['admin_url'][1]);
				$options = !empty($info['admin_url'][3]) ? $info['admin_url'][3] : null;
				$this->search_fields[$field]['url_format'] = array(
					Paraglide::url('admin', null, $url_params),
					$info['admin_url'][2],
					$options,
				);
			}
		}
	}
	
	private function _set_sections() {
		if (empty($this->_user)) {
			return;
		}
	
		$admin_path = realpath(dirname(__FILE__) . '/../..') . '/controllers/admin/*_controller.php';
		$admin_files = glob($admin_path);
		
		foreach ($admin_files as $file) {
			require_once $file;
			$section = substr(basename($file), 0, 0 - strlen('_controller.php'));
			$class = str_replace(' ', '', ucwords(str_replace(array('_', ' '), ' ', $section))) . 'Controller';
			
			$permission_name = 'admin.' . substr($class, 0, 0 - strlen('Controller'));
			
			if (!$this->_user->has_permission($permission_name)) {
				continue;
			}
			
			$vars = get_class_vars($class);
			$title = $vars['title'];
			$this->_sections[] = array(
				'class' => $class,
				'file' => $file,
				'section' => $section,
				'tab_position' => $vars['tab_position'],
				'title' => $vars['title'],
			);
			
			if (get_class($this) == $class) {
				$this->_section = $section;
				self::$title = $vars['title'];
				$this->_add_breadcrumb(self::$title, Paraglide::url('admin/' . $this->_section));
			}
		}
	}
	
	private function _set_tabs() {
		$tabs = array();
		
		foreach ($this->_sections as $i => $section) {
			if (!empty($section['tab_position'])) {
				$tabs[$section['tab_position']] = $section['title'];
			}
		}

		foreach ($this->_sections as $i => $section) {
			if (!empty($section['tab_position'])) {
				continue;
			}
			
			$section['tab_position'] = $i;

			while (!empty($tabs[$section['tab_position']])) {
				$section['tab_position']++;
			}
			
			$tabs[$section['tab_position']] = $section['title'];
		}

		ksort($tabs);
		$this->_tabs = $tabs;
	}
	
	protected function _add_breadcrumb($title, $url) {
		$this->_breadcrumbs[] = array(
			'title' => $title,
			'url' => $url,
		);
	}
	
	protected function _create($item) {
		$item->set_values($_GET);
	
		if (!empty($_POST)) {
			foreach ($this->wysiwyg_fields as $field) {
				if (!empty($_POST[$field])) {
					$_POST[$field] = stripslashes($_POST[$field]);
				}
			}
			
			$item->set_values($_POST);
			
			$vars = get_class_vars(get_class($item));

			if (!empty($vars['validations'])) {
				foreach ($vars['validations'] as $field => $info) {
					if (!isset($_POST[$field])) {
						continue;
					}
					
					if (!empty($info['date'])) {
						$item->$field = date('Y-m-d', strtotime($_POST[$field]));
					}
					
					if (!empty($info['datetime'])) {
						$item->$field = date('Y-m-d H:i:s', strtotime($_POST[$field]));
					}
					
					if (!empty($info['timestamp'])) {
						$item->$field = strtotime($_POST[$field]);
					}
				}
			}
			
			$inline_relationship_instances = array();
			
			if (!empty($this->inline_relationships)) {
				foreach ($this->inline_relationships as $relationship) {
					$relationships = Paragon::_get_static(get_class($item), '_relationships');
				
					if (!empty($relationships['belongs_to'][$relationship])) {
						$info = $relationships['belongs_to'][$relationship];
						$instance = new $info['class'];
						
						$instance->set_values($_POST, array(
							'prefix' => $relationship,
						));
						
						$inline_relationship_instances[] = array(
							'instance' => $instance,
							'info' => $info,
						);
					}
				}
			}
			
			if ($item->save()) {
				foreach ($inline_relationship_instances as $instance_info) {
					$instance = $instance_info['instance'];
					$info = $instance_info['info'];
					
					if ($instance->save() && !empty($info)) {
						$item->{$info['foreign_key']} = $instance->__primary_key__;
						$item->save();
						
						if (!empty($info['primary_key']) && isset($instance->{$info['primary_key']})) {
							$instance->{$info['primary_key']} = $item->__primary_key__;
							$instance->save();
						}
					}
				}
			
				$this->_post_create($item);
				Paraglide::redirect('admin/' . $this->_section, null, $item->__primary_key__);
			}
		}
		
		$fieldsets = $this->_get_fieldsets($this, $item, 'edit');

		$this->_add_breadcrumb('Create', Paraglide::url('admin/' . $this->_section, 'create'));
		$this->_render_view('admin/create', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'controller' => $this,
			'fields' => $fieldsets,
			'item' => $item,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => 'New ' . ucwords(str_replace('_', ' ', $this->noun)),
			'titleField' => $this->title_field,
			'user' => $this->_user,
		));
	}
	
	protected function _confirm($params) {
		if (!empty($params['id'])) {
			$item = $this->_get_item_or_redirect($params['id']);
		} else {
			Paraglide::redirect('admin/' . $this->_section);
		}
		
		if (!empty($_POST)) {
			if (!empty($_POST['cancel'])) {
				Paraglide::redirect('admin/' . $this->_section, null, $item->__primary_key__);
			}
		
			if (!empty($_POST['confirm'])) {
				$item->{$params['function']}();
				Paraglide::redirect('admin/' . $this->_section, null, $item->__primary_key__);
			}
		}
	
		$this->_add_breadcrumb($item->{$this->title_field}, Paraglide::url('admin/' . $this->_section, null, $item->__primary_key__));
		$this->_add_breadcrumb($params['title'], Paraglide::url('admin/' . $this->_section, Paraglide::$action, $item->__primary_key__));
		$this->_render_view('admin/confirm', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'cancel_url' => Paraglide::url('admin/' . $this->_section, null, $item->__primary_key__),
			'controller' => $this,
			'item' => $item,
			'message' => $params['message'],
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => $item->{$this->title_field},
			'user' => $this->_user,
		));
	}
	
	protected function _get_item_or_redirect($id) {
		if (empty($id)) {
			Paraglide::redirect('admin/' . $this->_section);
		}
	
		$item = call_user_func(array($this->model, 'find'), $id);
		
		if (empty($item)) {
			Paraglide::redirect('admin/' . $this->_section);
		}
		
		return $item;
	}
	
	protected function _post_create($item) {
	}
	
	protected function _post_edit($item) {
	}
	
	protected function _remove_breadcrumb($title) {
		if (!empty($this->_breadcrumbs[$title])) {
			unset($this->_breadcrumbs[$title]);
		}
	}
	
	protected function _render_view($view, $params) {
		$view_part = substr($view, strlen('admin/'));
		
		if (file_exists(APP_PATH . 'views/admin-custom/' . $this->_section . '/' . $view_part . '.tpl')) {
			$view = 'admin-custom/' . $this->_section . '/' . $view_part;
		} elseif (file_exists(APP_PATH . 'views/admin-custom/' . $view_part . '.tpl')) {
			$view = 'admin-custom/' . $view_part;
		}

		Paraglide::render_view($view, $params);
	}
	
	public function _preprocess() {
		$user = User::logged_in_user();
		
		if (($user == null || !$user->is_admin()) && (Paraglide::$controller != 'main' || Paraglide::$action != 'login')) {
			Paraglide::redirect('admin', 'login');
		}
	
		$this->_user = $user;
		
		foreach ($this->views as $view => $info) {
			$this->default_view = $view;
			break;
		}

		$this->_add_breadcrumb('Admin', Paraglide::url('admin'));
		$this->_set_sections();
		
		$permission_name = 'admin.' . str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_section)));

		if (empty($user) || !$user->has_permission($permission_name)) {
			if (Paraglide::$controller != 'main' || Paraglide::$action != 'login') {
				Paraglide::redirect('admin');
			}
		}
		
		$this->_set_tabs();
		$this->_process_class();
	}
	
	public function catalog() {
		$view = $this->views[Paraglide::$action];
		
		if (Paraglide::$action != 'catalog' && !empty($this->views[Paraglide::$action])) {
			$params = array();

			foreach ($GLOBALS['arguments'] as $param) {
				$params[] = urlencode($param);
			}
			
			$this->_add_breadcrumb($view['title'], Paraglide::url('admin/' . $this->_section, Paraglide::$action, $params));
		} else {
			$this->_add_breadcrumb('List', Paraglide::url('admin/' . $this->_section, 'catalog'));
		}
		
		$conditions = array();
		
		if (!empty($view) && !empty($view['conditions'])) {
			$conditions = $view['conditions'];
		}
		
		$search_fields = $this->search_fields;
		$fields = $this->search_fields;
		
		// we do this to init the class
		call_user_func(array($this->model, 'find'), array('limit' => 0));
		$relationships = Paragon::_get_static($this->model, '_relationships');

		foreach ($search_fields as $field => $info) {
			if (isset($info['searchable']) && empty($info['searchable'])) {
				unset($search_fields[$field]);
				continue;
			}
			
			$like = true;
			
			if (!empty($info['boolean'])) {
				$search_fields[$field]['values'] = array(
					'' => '',
					0 => 'No',
					1 => 'Yes',
				);
				$search_fields[$field]['type'] = 'dropdown';
			}
			
			$field_parts = explode('.', $field);
			
			if (count($field_parts) > 1) {
				$relationship_name = $field_parts[0];
				$relationship_value_field = $field_parts[1];
				$relationship = null;
				
				if (!empty($relationships['belongs_to'][$relationship_name])) {
					$relationship = $relationships['belongs_to'][$relationship_name];
					$type = 'belongs_to';
				} elseif (!empty($relationships['has_and_belongs_to_many'][$relationship_name])) {
					$relationship = $relationships['has_and_belongs_to_many'][$relationship_name];
					$type = 'has_and_belongs_to_many';
				} elseif (!empty($relationships['has_one'][$relationship_name])) {
					$relationship = $relationships['has_one'][$relationship_name];
					$type = 'has_one';
				} elseif (!empty($relationships['has_many'][$relationship_name])) {
					$relationship = $relationships['has_many'][$relationship_name];
					$type = 'has_many';
				}
				
				if (!empty($relationship)) {
					$model = $relationship['class'];
					$value_objects_count = call_user_func(array($model, 'count'), array(
						'start' => 0,
					));
	
					if ($value_objects_count > 200 || (!empty($info['type']) && $info['type'] == 'input')) {
						$search_fields[$field]['type'] = 'input';
					} else {
						$like = false;
						$value_objects = call_user_func(array($model, 'find'), array(
							'start' => 0,
						));
						$values = array('' => '');
						
						foreach ($value_objects as $object) {
							$values[$object->__primary_key__] = $object->$relationship_value_field;
						}
						
						$search_fields[$field]['type'] = 'dropdown';
						$other_key = ($type == 'belongs_to') ? 'id' : $relationship['foreign_key'];
						$new_field = $relationship_name . '.' . $other_key;
						$search_fields[$new_field] = $search_fields[$field];
						unset($search_fields[$field]);
						$field = $new_field;
						$info['values'] = $values;
					}
				}
			}
			
			if (!empty($info['values'])) {
				$values = array('' => '');
				
				foreach ($info['values'] as $key => $value) {
					$values[$key] = $value;
				}

				$search_fields[$field]['values'] = $values;
				$search_fields[$field]['type'] = 'dropdown';
			}

			$var = !empty($info['field']) ? $info['field'] : $field;
			$var = str_replace('.', '_', $var);

			if (!isset($_GET[$var]) || strlen($_GET[$var]) == 0) {
				continue;
			}

			if (!empty($like)) {
				$conditions[$var] = Paragon::condition('like', $_GET[$var]);
			} else {
				$conditions[$var] = $_GET[$var];
			}
			
			$search_fields[$field]['value'] = $_GET[$var];
		}
		
		foreach ($fields as $field => $info) {
			if (isset($info['searchable']) && empty($info['searchable'])) {
				unset($fields[$field]);
				continue;
			}
		}

		$order = call_user_func(array($this->model, 'order'), !empty($_GET['order']) ? $_GET['order'] : null);
		
		if ($order == null && !empty($view['order'])) {
			$order = $view['order'];
		}
		
		list($items, $pagination) = call_user_func(array($this->model, 'paginate'), array(
			'conditions' => $conditions,
			'order' => $order,
			'page' => !empty($_GET['page']) ? $_GET['page'] : 1,
			'per_page' => 100,
		));
		
		if (empty($items)) {
			$this->_render_view('admin/list-none', array(
				'breadcrumbs' => $this->_breadcrumbs,
				'controller' => $this,
				'search_fields' => $search_fields,
				'section' => $this->_section,
				'tabs' => $this->_tabs,
				'title' => 'No ' . self::$title,
				'user' => $this->_user,
			));
			return;
		}

		$this->_render_view('admin/list', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'conditions' => $conditions,
			'controller' => $this,
			'fields' => $fields,
			'items' => $items,
			'order' => $order,
			'pagination' => $pagination,
			'search_fields' => $search_fields,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => 'List ' . self::$title,
			'user' => $this->_user,
		));
	}
	
	public function create() {
		$item = new $this->model();
		$this->_create($item);
	}
	
	public function delete($id = null) {
		$item = $this->_get_item_or_redirect($id);
		
		if (!empty($_POST)) {
			if (!empty($_POST['cancel'])) {
				Paraglide::redirect('admin/' . $this->_section, null, $item->__primary_key__);
			}
		
			if (!empty($_POST['confirm'])) {
				if ($item->delete()) {
					Paraglide::redirect('admin/' . $this->_section);
				}
			}
		}
	
		$this->_add_breadcrumb($item->{$this->title_field}, Paraglide::url('admin/' . $this->_section, null, $item->__primary_key__));
		$this->_add_breadcrumb('Delete', Paraglide::url('admin/' . $this->_section, 'delete', $item->__primary_key__));
		$this->_render_view('admin/delete', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'controller' => $this,
			'item' => $item,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => $item->{$this->title_field},
			'user' => $this->_user,
		));
	}
	
	public function edit($id = null) {
		$item = $this->_get_item_or_redirect($id);
		$fieldsets = $this->_get_fieldsets($this, $item, 'edit');
	
		if (!empty($_POST)) {
			foreach ($this->wysiwyg_fields as $field) {
				if (!empty($_POST[$field])) {
					$_POST[$field] = stripslashes($_POST[$field]);
				}
			}
			
			$item->set_values($_POST);
			$vars = get_class_vars(get_class($item));
			
			if (!empty($vars['validations'])) {
				foreach ($vars['validations'] as $field => $info) {
					if (!empty($info['boolean'])) {
						$item->$field = !empty($_POST[$field]);
					} elseif (!isset($_POST[$field])) {
						continue;
					}
					
					if (!empty($info['date'])) {
						$item->$field = date('Y-m-d', strtotime($_POST[$field]));
					}
					
					if (!empty($info['datetime'])) {
						$item->$field = date('Y-m-d H:i:s', strtotime($_POST[$field]));
					}
					
					if (!empty($info['timestamp'])) {
						$item->$field = strtotime($_POST[$field]);
					}
				}
			}
			
			if ($item->save()) {
				foreach ($this->inline_relationships as $relationship) {
					$instance = $item->$relationship;
				
					if ($instance == null) {
						$relationships = Paragon::_get_static(get_class($item), '_relationships');
					
						if (!empty($relationships['belongs_to'][$relationship])) {
							$info = $relationships['belongs_to'][$relationship];
							$instance = new $info['class'];
						}
					} else {
						$info = null;
					}
					
					if ($instance != null) {
						$instance->set_values($_POST, array(
							'prefix' => $relationship,
						));
						
						if ($instance->save() && !empty($info)) {
							$item->{$info['foreign_key']} = $instance->__primary_key__;
							$item->save();
						}
					}
				}
			
				$this->_post_edit($item);
				Paraglide::redirect('admin/' . $this->_section, null, $item->__primary_key__, !empty($this->actions['edit']['query_string']) ? $this->actions['edit']['query_string'] : null);
			}
			
			foreach ($fieldsets['fieldsets'] as $key => $fieldset) {
				$fieldsets['fieldsets'][$key] = Form::update_field_values($fieldset, $_POST);
			}
		}
		
		$this->_add_breadcrumb($item->{$this->title_field}, Paraglide::url('admin/' . $this->_section, null, $item->__primary_key__));
		$this->_add_breadcrumb('Edit', Paraglide::url('admin/' . $this->_section, 'edit', $item->__primary_key__));

		$this->_render_view('admin/edit', array(
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
	
	public function index($id = null) {
		if (!empty($id)) {
			Paraglide::load('admin/' . $this->_section . '/view/' . $id);
			return;
		}
		
		if ($this->_section != 'main') {
			Paraglide::load('admin/' . $this->_section . '/' . $this->default_view);
			return;
		}
		
		$this->_render_view('admin/index', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'controller' => $this,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => 'Admin',
			'user' => $this->_user,
		));
	}
	
	public function relationship_selector() {
		$GLOBALS['data']['stay_modal'] = true;
		$this->search();
	}

	public function view($id = null) {
		$item = $this->_get_item_or_redirect($id);
		$this->_add_breadcrumb($item->{$this->title_field}, Paraglide::url('admin/' . $this->_section, null, $item->__primary_key__));
		$items = array();
		$fields = $this->_get_fields($this, $item, 'view');
		$items[ucwords(str_replace('_', ' ', $this->noun))] = array(
			'item' => $item,
			'fields' => $fields,
		);

		if (!empty($this->inline_relationships)) {
			foreach ($this->inline_relationships as $relationship) {
				$instance = $item->$relationship;
				
				if ($instance != null) {
					$admin = new stdClass();
					$admin->inline_relationships = array();
					$admin->inline_relationship = $relationship;
					$admin->model = get_class($instance);
					$admin->title_field = 'id';
					$admin->hidden_fields = array();
					$admin->readonly_fields = array();
					
					foreach ($this->hidden_fields as $field) {
						if (substr($field, 0, strlen($relationship . '.')) == $relationship . '.') {
							$admin->hidden_fields[] = substr($field, strlen($relationship . '.'));
						}
					}
					
					$fields = $this->_get_fields($admin, $instance, 'view');
					$items[ucwords(str_replace('_', ' ', $relationship))] = array(
						'item' => $instance,
						'fields' => $fields,
					);
				}
			}
		}

		$this->_render_view('admin/view', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'controller' => $this,
			'item' => $item,
			'items' => $items,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => $item->{$this->title_field},
			'titleField' => $this->title_field,
			'user' => $this->_user,
		));
	}
}
