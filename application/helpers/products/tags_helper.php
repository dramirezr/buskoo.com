<?php if (!defined('BASEPATH'))exit ('No direct script access allowed');

function get_tags($post_id){

	$CI = & get_instance();
	$CI->load->model('business_model');
	
	$tags_product_id = ci_config('tags_product_id');

	//Load the product specs
	$prodcts = $CI->business_model->get_products($post_id);
	
	$tags = array();
	foreach($prodcts  as $p){
		if(in_array($p->product_id, $tags_product_id) && $p->active == 1){
			$i_data = unserialize($p->implementation_data);
			if(isset($i_data['tags']) && $i_data['tags'])
				$tags[] = preg_replace('/(?:\s\s+|\n|\t)/', ' ', trim(strtolower($i_data['tags'])));
		}
	}
	
	if(!count($tags))
		return '';
		
	return (count($tags) == 1 ) ? $tags[0] : implode(' ', $tags);
}

