<?php
if (!defined('BASEPATH'))exit ('No direct script access allowed');

function fbpage_show($post_id){
	$CI = & get_instance();
	
	$CI->load->model('business_model');
	$CI->lang->load('products/fbpage');
	
	//if the product is active
	$fbpage_product_id = ci_config('fbpage_product_id');
	$prod = $CI->db->get_where('bz_products', "post_id = $post_id AND active = 1 AND product_id IN (".implode(',', $fbpage_product_id).")")->result();
	
	
	$pages = array();
	foreach($prod as $p){
		$i_data = unserialize($p->implementation_data);
		if(isset($i_data['fbpage']) && $i_data['fbpage'])
			$pages[] = '<h6 class="clear-margin font-weight-normal"><small>' .
					'<a href="'.$i_data['fbpage'].'" target="_blank">'.lang('fbpage.linkmsg').'</a>' .
					'</small></h6>';
	}
	
	return implode("\n", $pages);	
}