<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Terms extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->lang->load('api');
		$this->load->model('business_model');
	}
	
	function index(){
		$lang = current_lang();
		
		$content = get_config_val("terms_$lang");
		$this->load->view('page', array('content' => $content));
	}
}