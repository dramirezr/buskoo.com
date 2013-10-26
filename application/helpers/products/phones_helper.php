<?php
if (!defined('BASEPATH'))exit ('No direct script access allowed');

function phones_show($post_id){
	$CI = & get_instance();
	
	$CI->load->model('business_model');
	$CI->lang->load('products/phones');
	
	//if the product is active
	$phones_product_id = ci_config('phones_product_id');
	$prod = $CI->db->get_where('bz_products', "post_id = $post_id AND active = 1 AND product_id IN (".implode(',', $phones_product_id).")")->result();
	
	$phones = array();
	foreach($prod as $p){
		$i_data = unserialize($p->implementation_data);
		if(isset($i_data['phones']) && is_array($i_data['phones']))
			$phones = array_merge($phones, $i_data['phones']);
	}
	
	return implode(" ", $phones);	
}