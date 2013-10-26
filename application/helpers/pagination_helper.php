<?php

if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
	
function pagination($start, $rows_per_page, $num_found){
	
	if($num_found < $rows_per_page)
		return '';
		
	//Calculating the number of pages 
	$pages = $num_found / $rows_per_page;

	$mod = $num_found % $rows_per_page;
	if($mod){
		$pages++;
	}

	$current_page = ($start / $rows_per_page) + 1;
	
	?>
		<div class="pagination-centered">
		  <ul class="pagination">
	<? if($start > 0): ?>	  
		    <li class="arrow"><a class="goto-page" start="<?=($start - $rows_per_page)?>" href="javascript:void(0)">&laquo;</a></li>
	<? endif; ?>
	<?php 
	$nstart = 0;
	for($page=1; $page<=$pages; $page++){
		$nstart = ($page == 1) ? 0 : $nstart + $rows_per_page;
		?>
		<li <?=($current_page == $page) ? 'class="current"' : ''?>><a href="javascript:void(0)" class="goto-page" start="<?=$nstart?>"><?= $page ?></a></li>
		<?php
	}
	
	?>
	
	<? if($current_page < ($pages - 1)): ?>
			<li class="arrow"><a class="goto-page" start="<?=($start + $rows_per_page)?>" href="javascript:void(0)">&raquo;</a></li>
	<? endif; ?>
	
		  </ul>
		</div>	
	<?php
}

//<li class="unavailable"><a href="">&hellip;</a></li>