<?php if (!defined('BASEPATH'))exit ('No direct script access allowed');

function show_promos($post_id){

	$CI = & get_instance();
	$CI->load->model('business_model');
	$CI->lang->load('products/promo');
	
	$promo_product_id = ci_config('promo_product_id');

	//Load the product specs
	$prodcts = $CI->business_model->get_products($post_id);
	
	$promos = array();
	foreach($prodcts  as $p){
		if(in_array($p->product_id, $promo_product_id) && $p->active == 1){
			$i_data = unserialize($p->implementation_data);
			if(isset($i_data['promo']) && $i_data['promo'])
				$promos[] = $i_data['promo'] ;
		}
	}
	
	if(!count($promos))
		return '';
	
	$now = new DateTime('NOW');
	$now = $now->getTimestamp();
	
	ob_start();
	?>
	
	 <section>
	    <p class="title" data-section-title><a href="#panel3" class="enterprise-panel3" pid="<?=$d->id?>"><?= lang('promo.tabname') ?></a></p>
	    <div class="content" data-section-content>
	    	<!--<div class="row">
		    	<div class="large-12 columns">
		    		<small><?=lang('promo.followmenews')?></small>
			    	<a href="#" class="large button expand alert radius"><?= lang('promo.followme') ?></a>
		    	</div>
	    	</div>--> 
	    	
	    	<? foreach($promos as $p): ?>
	    	<?php
	    		$show = true;
	    		if($p['date']){
	    			$date_parts = explode('-', $p['date']);
	    			$date = new DateTime("{$date_parts[2]}-{$date_parts[1]}-{$date_parts[0]} 11:59:59");
	    			$date = $date->getTimestamp();
	    			$show = ($now > $date) ? false : true;
	    		}
	    		 
	    	?>
	    	
		    	<? if($show): ?>
			    	<div class="row">
			    		<div class="large-12 columns">
					    	<div class="panel">
				  				<h5><?=$p['name']?></h5>
								<p><?=$p['desc']?></p>
							</div>
						</div>
					</div>
				<? endif; ?>
			<? endforeach; ?>
	</section>

	<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}
