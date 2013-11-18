<?php
if (!defined('BASEPATH'))exit ('No direct script access allowed');

function show_logo($post_id){
	$CI = & get_instance();
	$CI->load->model('media');
	
	//if the product is active
	$logo_prod_id = ci_config('logo_product_id');
	$prod = $CI->db->get_where('bz_products', "active = 1 AND product_id IN (".implode(',', $logo_prod_id).")")->result();
	if(!count($prod)){
		no_logo_icon(80, 80);
		return false;
	}
	
	if($logo = $CI->media->select(array('post_id' => $post_id, 'type' => 'logo', 'state' => 1))){
		$logo = $logo[0];
		$logo_url = ci_config('media_server_show_url').'/'.$logo->hash;
		?>
		<img src="<?= $logo_url ?>"  width="80" height="80">
		<?php
	}else{
		no_logo_icon(80, 80);
	}
}


function get_logo_url($post_id){
	$CI = & get_instance();
	$CI->load->model('media');
	//if the product is active
	$logo_prod_id = ci_config('logo_product_id');
	$prod = $CI->db->get_where('bz_products', "active = 1 AND product_id IN (".implode(',', $logo_prod_id).")")->result();
	if(!count($prod)){
		return '';
	}
	
	if($logo = $CI->media->select(array('post_id' => $post_id, 'type' => 'logo', 'state' => 1))){
		$logo = $logo[0];
		return $logo->hash;
	}else{
		return '';
	}
}