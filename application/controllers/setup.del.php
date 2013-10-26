<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller {

	var $title = 'Setup';
	var $error = NULL;
	var $setup_params = array();
			
	function __construct(){
		parent::__construct();
		
		
		$this->setup_params['enterprise'] = $this->session->userdata('enterprise');
		// load language file
		$this->lang->load('setup');
		
		$this->load->library('grocery_CRUD');
		
	}
	
	function index(){
		
		$this->render();
	}
	
	
	function rooms(){
		
		$this->grocery_crud->set_table('room');
		
		$this->grocery_crud->where('enterprise_id',$this->setup_params['enterprise']->id);
		
		$this->grocery_crud->set_relation_n_n('features', 'room_features', 'features', 'room_id', 'features_id', 'name');
		
		$this->grocery_crud->columns('name','rate','size','active');
		
		$this->grocery_crud->display_as('name',lang('setup.rooms.name'));
		$this->grocery_crud->display_as('rate',lang('setup.rooms.rate'));
		$this->grocery_crud->display_as('size',lang('setup.rooms.size'));
		$this->grocery_crud->display_as('active',lang('setup.rooms.active'));
		$this->grocery_crud->display_as('notes',lang('setup.rooms.notes'));
		
		$this->grocery_crud->fields('enterprise_id','name','rate','size','active','features','notes');
		$this->grocery_crud->required_fields('name');
		$this->grocery_crud->field_type('enterprise_id', 'hidden', $this->setup_params['enterprise']->id);
				
		$this->setup_params['output'] = $this->grocery_crud->render();
		$this->setup_params['title'] = lang('setup.rooms.title');
		
		$this->render('crud');
	}
		
	private function render($template = 'general'){
		
		$this->template->write('title', $this->title);
		
		$this->setup_params['navigation'] = $this->load->view('templates/setup/_navigation', NULL, true);
		
		$this->template->write_view('content', "templates/setup/$template", $this->setup_params, TRUE);
		
		$this->template->render();		
	}
	
}