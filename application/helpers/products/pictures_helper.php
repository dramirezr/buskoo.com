<?php if (!defined('BASEPATH'))exit ('No direct script access allowed');

function pictures_show($post_id){
	$CI = & get_instance();
	$CI->load->model('media');
	$pics = $CI->media->select(array('post_id' => $post_id));
	
	if(!count($pics))
		return NULL;
	
	//Load the youtube videos
	$ytvideo_prod_id = ci_config('ytvideo_product_id');
	$prodcts = $CI->business_model->get_products($post_id);

	$ytvideos = array();
	foreach($prodcts  as $p){
		if(in_array($p->product_id, $ytvideo_prod_id)){
			$i_data = unserialize($p->implementation_data);
			if(isset($i_data['ytvideo']) && $i_data['ytvideo'])
				$ytvideos[] = "https://www.youtube.com/watch?v=".$i_data['ytvideo'] ;
		}
	}

	ob_start();
	
	?>
	<div id="galleria-<?=$post_id?>" style="height:320px"></div>
	
	<script>
						
				
		var data = [
	<? foreach($pics as $p): ?>
		<? 
			$url = ci_config('media_server_show_url').'/'.$p->hash;
			$thumb_url = ci_config('media_server_thumb_url').'/'.$p->hash; 
		?>
				    {
				        image: '<?=$url?>',
				        thumb: '<?=$thumb_url?>'
				    },
	<? endforeach; ?>

	<? if(count($ytvideos)): ?>
		<? foreach($ytvideos as $v): ?>
				    {
				        video: '<?=$v?>'
				    },
		<?endforeach;?>
	<? endif; ?>
				  ];

		/*Galleria.ready(function(options) {

		});*/
		
		Galleria.run("#galleria-<?=$post_id?>", {
			dataSource: data
		});

	</script>
	
	<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}
