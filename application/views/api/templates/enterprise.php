<?
	$distance = (float) $d->_dist_;
	$unit = 'Km';
	if($distance < 1){
		$distance = $distance * 1000;
		$unit = 'Mts';
	}
		
	$distance = number_format($distance, 2).$unit;								
	
	$score_avg = number_format($d->score_avg, 2);
		
	//Load the types
	$types = $this->business_model->get_biz_types($d->id);
	$main_type = isset($types[0]) ? $types[0] : NULL;
	
	$tmp = array();
	foreach($types as $t){
		$tmp[] = $t->name;
	}
			
	$str_types = implode(', ', $tmp);
	$tmp = array();

	$post = $this->business_model->get_by_id($d->id);
	$post->last_update = isset($post->last_update) ? $post->last_update : '';
	$post->visits = isset($post->visits) ? $post->visits : 0;

/*	extrainfo="<?= show_extrainfo($d->id) ?>"
	address="<?= ucfirst($d->address) ?>"
	phones="<?= $d->phones ?>"
	email="<?= email_show($d->id) ?>"
	website="<?= website_show($d->id) ?>"
	facebook="<?= fbpage_show($d->id) ?>"

	$extrainfo = show_extrainfo($d->id) .' '. ucfirst($d->address) .' ';
	$extrainfo.= $d->phones .' '. email_show($d->id) .' ';
	$extrainfo.= website_show($d->id).' '.fbpage_show($d->id) ;
	
*/
	
?>

			<h4 class="subheader clear-margin"><?= $index.'. '.ucwords($d->name) ?></h4>
			<h6 class="clear-margin font-weight-normal line-height-08"><small><?= $str_types ?></small></h6>
			<h5 class="clear-margin font-weight-normal line-height-08 margin-bottom-5px"><small><?= lang('search.distance') ?>: <?= $distance ?>, <?= lang('search.score') ?>: <?= $score_avg ?>, ID: <?= $d->id ?>, <?= lang('search.lastupdate').': '.$post->last_update ?>, <?= lang('search.visits').': '.$post->visits ?></small></h5>

			<div class="row">
				<div class="large-4 columns">
					<nav class="breadcrumbs">
						<a href="javascript:void(0)" post-id="<?= $d->id ?>" class="qualify-post" pid="<?=$d->id?>"><?= lang('search.review') ?></a>
						<a href="javascript:void(0)" address="<?= ucfirst($d->address) ?>" phones="<?= $d->phones ?>" post-id="<?= $d->id ?>" bz-name="<?=$d->name?>" lat="<?= $d->location_0_coordinate ?>" lng="<?= $d->location_1_coordinate ?>" dist="<?= $d->_dist_ ?>" class="set-directions"  pid="<?=$d->id?>"><?= lang('search.howtoget') ?></a>
						<a href="javascript:void(0)" address="<?= ucfirst($d->address) ?>" phones="<?= $d->phones ?>" post-id="<?= $d->id ?>" bz-name="<?=$d->name?>" lat="<?= $d->location_0_coordinate ?>" lng="<?= $d->location_1_coordinate ?>" dist="<?= $d->_dist_ ?>" class="where-is"  pid="<?=$d->id?>"><?= lang('search.whereis') ?></a>
					</nav>
				</div>
			</div>
			<div class="row">&nbsp;</div>	  	

			<div class="section-container auto" data-section>
				<section>
    				<p class="title" data-section-title><a href="#panel1"><?= lang('search.start') ?></a></p>
    				<div class="content" data-section-content>
					  	<div class="row">
							<div class="large-9 columns">
								<div class="row">
									<div class="large-2 columns"><? show_logo($d->id) ?></div>
									<div class="large-10 columns">
										<h6 class="clear-margin font-weight-normal"><?= show_extrainfo($d->id) ?></h6>
										<h6 class="clear-margin font-weight-normal"><?= ucfirst($d->address) ?></h6>
										<h6 class="clear-margin font-weight-normal"><?= lang('search.phone') ?>: <?= $d->phones ?></h6>
										<h6 class="clear-margin font-weight-normal"><?= email_show($d->id) ?></h6>								
										<?= website_show($d->id) ?>								
										<?= fbpage_show($d->id) ?>								
									</div>
								</div>
							</div>
							<div class="large-3 columns">
								<div class="row"></div>
								<div class="row"></div>
							</div>
						</div>
    				</div>
  				</section>
  				
  				 <section>
				    <p class="title" data-section-title><a href="#panel2" class="enterprise-panel2" id="<?=$d->id?>" pid="<?=$d->id?>"><?= lang('search.more') ?></a></p>
				    <div class="content" data-section-content id="section-content-<?=$d->id?>"></div>
 				</section>
				
				<?= show_promos($d->id) ?>
			</div>