<?php if (!defined('BASEPATH'))exit ('No direct script access allowed');

function show_extrainfo($post_id){

	$CI = & get_instance();
	$CI->load->model('business_model');
	
	$extrainfo_prod_id = ci_config('extrainfo_product_id');

	//Load the product specs
	$prodcts = $CI->business_model->get_products($post_id);
	
	$extrainfo = array();
	foreach($prodcts  as $p){
		if(in_array($p->product_id, $extrainfo_prod_id) && $p->active == 1){
			$i_data = unserialize($p->implementation_data);
			if(isset($i_data['extrainfo']) && $i_data['extrainfo'])
				$extrainfo[] = htmlentities(trim(ucfirst(strtolower($i_data['extrainfo'])))) ;
		}
	}
	
	if(!count($extrainfo))
		return '';
		
	return (count($extrainfo) == 1 ) ? $extrainfo[0] : implode('<br>', $extrainfo);
}

