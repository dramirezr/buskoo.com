<?php
if (!defined('BASEPATH'))exit ('No direct script access allowed');

function website_show($post_id){
	$CI = & get_instance();
	
	$CI->load->model('business_model');
	$CI->lang->load('products/website');
	
	//if the product is active
	$webpage_product_id = ci_config('website_product_id');
	$prod = $CI->db->get_where('bz_products', "post_id = $post_id AND active = 1 AND product_id IN (".implode(',', $webpage_product_id).")")->result();
	
	
	$pages = array();
	foreach($prod as $p){
		$i_data = unserialize($p->implementation_data);
		if(isset($i_data['website']) && $i_data['website'])
			$pages[] = '<h6 class="clear-margin font-weight-normal">' .
					'<a href="'.$i_data['website'].'" target="_blank">'.lang('website.linkmsg').'</a>' .
					'</h6>';
	}
	
	return implode("\n", $pages);	
}