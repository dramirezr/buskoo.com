<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Promo extends CI_Controller {
	
	var $params = array();
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('post');
		$this->load->model('business_model');
		$this->lang->load('products/promo');
		
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
				
		$this->params['promo'] = isset($i_data['promo']) ? $i_data['promo'] : '';
		
		$this->params['user'] = $this->session->userdata('user');
		$this->params['bz_product_id'] = $post_product_id;
		
		$this->load->library('user_agent');
		
		$this->load->view('products/promo/index', $this->params);
		
	}
	
	function save(){
		$post_id = $this->input->post('post_id', TRUE); 
		$bz_product_id = $this->input->post('bz_product_id', TRUE);		
		$name = $this->input->post('name', TRUE);
		$desc = $this->input->post('desc', TRUE);
		$date = $this->input->post('date', TRUE);
		
		if($date){
			$date_parts = explode('-', $date);
			if(!checkdate ( $date_parts[1] , $date_parts[2] , $date_parts[0] )){
				die( json_encode(array('status' => 'error', 'msg' => lang('promo.notvaliddate'))));
			}
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
		
		$promo = array(
			'name' => $name,
			'desc' => $desc,
			'date' => $date
		);
		
		$i_data['promo'] = $promo;
		
		$serialized = serialize($i_data);
		
		$this->business_model->update_bz_product($bz_product_id, array('implementation_data' => $serialized));
		
		die( json_encode(array('status' => 'success', 'msg' => lang('promo.success'))));
	}
}