<?php
require_once APP_PATH . 'lib/classes/admin_controller_base.php';

class CategoriesController extends AdminControllerBase {
	public static $title = 'Categories';
	
	public $title_field = 'name';
	public $model = 'Category';
	public $models = array('Category', 'Event', 'User');
	public $noun = 'category';
	public $readonly_fields = array('date_created', 'date_updated');
	
	public $search_fields = array(
		'name' => array(
			'admin_url' => array(
				'_section_',
				array('%s'),
				array('id'),
			),
			'sortable' => true,
		),
		'is_visible' => array(
			'sortable' => true,
			'boolean' => true,
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
		'events' => array(
			'title' => 'Events',
			'function' => 'events',
		),
		'delete' => array(
			'title' => 'Delete',
			'function' => 'delete',
		),
	);

	public function events($id = null) {
		$category = $this->_get_item_or_redirect($id);
		$order = !empty($_GET['order']) ? Event::order($_GET['order']) : null;
		$events = $category->events(array(
			'order' => $order,
		));
		
		$fields = array(
			'title' => array(
				'url_format' => array(
					Paraglide::url('admin/events', '%s'),
					array('id'),
				),
//				'sortable' => true,
			),
			'category.name' => array(
				'label' => 'Category',
				'url_format' => array(
					Paraglide::url('admin/categories', '%s'),
					array('category_id'),
				),
//				'sortable' => true,
			),
			'status' => array(
//				'sortable' => true,
				'values' => Event::$validations['status']['values'],
			),
			'is_featured' => array(
				'boolean' => true,
//				'sortable' => true,
			),
			'date_starts' => array(
//				'sortable' => true,
			),
			'date_ends' => array(
//				'sortable' => true,
			),
		);
		
		$this->_add_breadcrumb($category->name, Paraglide::url('admin/categories', null, $category->id));
		$this->_add_breadcrumb('Events', Paraglide::url('admin/categories', 'events', $category->id));
		
		if (empty($events)) {
			$this->_render_view('admin/list-none', array(
				'breadcrumbs' => $this->_breadcrumbs,
				'controller' => $this,
				'item' => $category,
				'section' => $this->_section,
				'tabs' => $this->_tabs,
				'title' => $category->name,
				'user' => $this->_user,
			));
			return;
		}

		$this->_render_view('admin/list', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'controller' => $this,
			'fields' => $fields,
			'item' => $category,
			'items' => $events,
			'order' => $order,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => $category->name,
			'user' => $this->_user,
		));
	}
}
