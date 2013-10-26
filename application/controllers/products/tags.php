<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends CI_Controller {
	
	var $params = array();
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('post');
		$this->load->model('business_model');
		$this->lang->load('products/tags');
		
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
				
		$this->params['tags'] = isset($i_data['tags']) ? $i_data['tags'] : ''; 
		$this->params['unit'] = $i_data['unit'];
		$this->params['max_chars'] = $i_data['unit'] * 20 + $i_data['unit'];
		
		$this->params['user'] = $this->session->userdata('user');
		$this->params['bz_product_id'] = $post_product_id;
		
		$this->load->view('products/tags/index', $this->params);
		
	}
	
	function save(){
		$post_id = $this->input->post('post_id', TRUE); 
		$user_id = $this->input->post('user_id', TRUE);
		$bz_product_id = $this->input->post('bz_product_id', TRUE);
		$tags = $this->input->post('tags', TRUE);
				
		$prodcts = $this->business_model->get_products($post_id);
		$product = null;
		foreach($prodcts  as $p){
			if($bz_product_id == $p->id){
				$product = $p;
				break;
			}
		}
		
		$i_data = unserialize($product->implementation_data);
		
		$max_chars = $i_data['unit'] * 20 + $i_data['unit'];
		
		//Truncate string
		if(strlen($tags) > $max_chars){
			$tags = substr($tags, 0, $this->max_chars);
		}

		$i_data['tags'] = preg_replace('/(?:\s\s+|\n|\t)/', ' ', trim(strtolower($tags)));
		
		$serialized = serialize($i_data);
		
		$this->business_model->update_bz_product($bz_product_id, array('implementation_data' => $serialized));
		
		$this->business_model->syncronize($post_id);
		
		die( json_encode(array('status' => 'success', 'msg' => lang('tags.success'))));
	}
}