<?php
if (!defined('BASEPATH'))exit ('No direct script access allowed');

function email_show($post_id){
	$CI = & get_instance();
	
	$CI->load->model('business_model');
	$CI->lang->load('products/email');
	
	//if the product is active
	$email_product_id = ci_config('email_product_id');
	$prod = $CI->db->get_where('bz_products', "post_id = $post_id AND active = 1 AND product_id IN (".implode(',', $email_product_id).")")->result();
	
	$emails = $ms = array();
	foreach($prod as $p){
		$i_data = unserialize($p->implementation_data);
		if(isset($i_data['emails']) && is_array($i_data['emails'])){
			foreach($i_data['emails'] as $e)
				$ms[] = '<a href="mailto:'.$e.'">'.$e.'</a>';
			
			$emails = array_merge($emails, $ms);
			$ms = array();
		}
			
	}
	
	return implode(", ", $emails);	
}

function getEmail($post_id){
	$CI = & get_instance();
	
	$CI->load->model('business_model');
	$CI->lang->load('products/email');
	
	//if the product is active
	$email_product_id = ci_config('email_product_id');
	$prod = $CI->db->get_where('bz_products', "post_id = $post_id AND active = 1 AND product_id IN (".implode(',', $email_product_id).")")->result();
	
	$emails = $ms = array();
	foreach($prod as $p){
		$i_data = unserialize($p->implementation_data);
		if(isset($i_data['emails']) && is_array($i_data['emails'])){
			foreach($i_data['emails'] as $e)
				$ms[] = $e;
			
			$emails = array_merge($emails, $ms);
			$ms = array();
		}
			
	}
	
	return implode(", ", $emails);	
}