<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extrainfo extends CI_Controller {
	
	var $params = array();
	var $max_chars;
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('post');
		$this->load->model('business_model');
		$this->lang->load('products/extrainfo');
		
		$this->max_chars = ci_config('extrainfo.max_characters');
	}
	
	function index(){
		
		$post_id = $this->input->get('post_id');
		$post_product_id = $this->input->get('post_product_id');
		
		//Load the current post
		$this->params['post'] = $this->post->get_by_id($post_id);

		//Load the product specs
		$prodcts = $this->business_model->get_products($post_id);
		$product = null;
		foreach($prodcts  as $p){
			if($post_product_id == $p->id){
				$product = $p;
				break;
			}
		}
		$this->params['product'] = $product;

		$i_data = unserialize($product->implementation_data);
				
		$this->params['extrainfo'] = isset($i_data['extrainfo']) ? $i_data['extrainfo'] : ''; 
		
		$this->params['user'] = $this->session->userdata('user');
		$this->params['bz_product_id'] = $post_product_id;
		$this->params['max_chars'] = $this->max_chars;
		
		$this->load->view('products/extrainfo/index', $this->params);
		
	}
	
	function save(){
		$post_id = $this->input->post('post_id', TRUE); 
		$user_id = $this->input->post('user_id', TRUE);
		$bz_product_id = $this->input->post('bz_product_id', TRUE);
		$extrainfo = $this->input->post('extrainfo', TRUE);
		
		//Truncate string
		if(strlen($extrainfo) > $this->max_chars){
			$extrainfo = substr($extrainfo, 0, $this->max_chars);
		}
		
		$prodcts = $this->business_model->get_products($post_id);
		$product = null;
		foreach($prodcts  as $p){
			if($bz_product_id == $p->id){
				$product = $p;
				break;
			}
		}
		
		$i_data = unserialize($product->implementation_data);
		$i_data['extrainfo'] = $extrainfo;
		
		$serialized = serialize($i_data);
		
		$this->business_model->update_bz_product($bz_product_id, array('implementation_data' => $serialized));
		
		die( json_encode(array('status' => 'success', 'msg' => lang('extrainfo.success'))));
	}
}