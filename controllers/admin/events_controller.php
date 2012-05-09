<?php
require_once APP_PATH . 'lib/classes/admin_controller_base.php';

class EventsController extends AdminControllerBase {
	public static $title = 'Events';
	
	public $title_field = 'title';
	public $model = 'Event';
	public $models = array('Choice', 'Event', 'User');
	public $noun = 'event';
	public $option_fields = array(
		'correct_choice_id' => 'choices',
	);
	public $linked_fields = array(
		'category_id' => 'categories',
	);
	public $hidden_fields = array();
	public $readonly_fields = array('date_created', 'date_updated', 'processed');
	
	public $search_fields = array(
		'title' => array(
			'admin_url' => array(
				'_section_',
				array('%s'),
				array('id'),
			),
			'sortable' => true,
		),
		'category.name' => array(
			'label' => 'Category',
			'admin_url' => array(
				'categories',
				array('%s'),
				array('category_id'),
			),
			'sortable' => true,
		),
		'status' => array(
			'sortable' => true,
		),
		'is_featured' => array(
			'boolean' => true,
			'sortable' => true,
		),
		'date_starts' => array(
			'sortable' => true,
		),
		'date_ends' => array(
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
		'choices' => array(
			'title' => 'Choices',
			'function' => 'choices',
		),
		'cancel' => array(
			'title' => 'Cancel',
			'function' => 'cancel',
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
		'featured' => array(
			'title' => 'Featured',
			'function' => 'featured',
			'conditions' => array(
				'is_featured' => true,
			),
		),
		'create' => array(
			'title' => 'Create',
			'function' => 'create',
		),
	);
	
	public function _preprocess() {
		parent::_preprocess();
		$this->search_fields['status']['values'] = Event::$validations['status']['values'];
	}
	
	public function cancel($id = null) {
		$event = $this->_get_item_or_redirect($id);
		$message = 'Are you sure you want to cancel this event?';
		$this->_confirm(array(
			'id' => $event->id,
			'title' => 'Cancel',
			'message' => $message,
			'function' => 'cancel',
		));
	}
	
	public function choices($id = null) {
		$event = $this->_get_item_or_redirect($id);
		
		if (!empty($_POST['name'])) {
			$choice = new Choice();
			$choice->event_id = $event->id;
			$choice->name = $_POST['name'];
			$choice->save();
			Web::redirect('admin/events', 'choices', $event->id);
		}
		
		if (!empty($_POST['delete_choice']) && !empty($_POST['choice_id'])) {
			$choice = $event->choices($_POST['choice_id']);

			if (!empty($choice)) {
				$choice->delete();
				Web::redirect('admin/events', 'choices', $event->id);
			}
		}
		
		$choices = $event->choices;
		
		$this->_add_breadcrumb($event->name, Web::url('admin/events', null, $event->id));
		$this->_add_breadcrumb('Choices', Web::url('admin/events', 'choices', $event->id));
		
		$this->_render_view('admin-custom/events/choices', array(
			'breadcrumbs' => $this->_breadcrumbs,
			'choices' => $choices,
			'controller' => $this,
			'item' => $event,
			'section' => $this->_section,
			'tabs' => $this->_tabs,
			'title' => $event->title,
			'user' => $this->_user,
		));
	}
	
	public function create() {
		$this->hidden_fields[] = 'correct_choice_id';
		return parent::create();
	}
	
	public function featured() {
		$this->catalog();
	}
}
