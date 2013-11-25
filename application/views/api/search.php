		<? header('Content-Type: text/html'); 

		if($results->response->numFound > 1): ?>
		
		<p><? 

		if($distance=='50000')
			echo sprintf(lang('search.resultstitleaall'), $results->response->numFound, $text) ;
		else
			echo sprintf(lang('search.resultstitle'), $results->response->numFound, $text, $distance) ;

		?></p>
		
		<div class="row full-width">
			<div class="large-5 columns">
			<dl class="sub-nav" id="sort-wrapper">
			  <dt><?=lang('dashboard.searchform.sortby')?>:</dt>
			  <dd id="score-dd"><a href="javascript:void(0)" class="sort-onresults-option" id="score"><?=lang('dashboard.searchform.sortby.score')?></a></dd>
			  <dd id="score_avg-dd"><a href="javascript:void(0)" class="sort-onresults-option" id="score_avg"><?=lang('dashboard.searchform.sortby.scoreavg')?></a></dd>
			  <dd id="geodist-dd"><a href="javascript:void(0)" class="sort-onresults-option" id="geodist"><?=lang('dashboard.searchform.sortby.distance')?></a></dd>
			</dl>			
			</div>
    	</div>
		<? endif; ?>
		
		<?= pagination($start, $rows, $numFound) ?>
		<div class="row full-width" id="results-wrapper">
			<? $index = $start + 1?>
			<? foreach($docs as $i => $d): ?>
			<? $this->view('api/templates/'.str_replace(' ', '_', $d->post_type_en), array('d' => $d, 'index' => $index)); ?>	
			<? $score_avg = number_format($d->score_avg, 2);
				$scores[$d->id] = '{id: '.$d->id.', name: "'.ucfirst($d->name).'", score_avg: '.$score_avg.'}'; 
				$index ++;
			?>		
			<? endforeach; ?>
		</div>
		
		<input type="hidden" name="serach-results-orderby" />
		<?= pagination($start, $rows, $numFound) ?>
		
		<a class="close-reveal-modal">&#215;</a>

		<script>		
		<? if($results->response->numFound): ?>
			var posts = {}; 
			<? foreach($scores as $id => $s): ?>
			posts[<?=$id?>] = <?=$s?>;<?="\n"?>
			<? endforeach; ?>
			
			var orderby = '<?=$sort?>';
			
			$('.qualify-post').click(function(e){
				//e.preventDefault();
				var post_id = $(this).attr('post-id');
				var post = posts[post_id];
    			
    			$('#post_id').val(post_id);
    			$('#qf-post-name').html(post.name);
    			
    			$('#search-result-wrapper').foundation('reveal', 'close');    			
    			$('#add-qualification-wrapper').foundation('reveal', 'open');
    		});
			
			$('.goto-page').click(function (e){
				e.preventDefault();
				
				var start = parseInt($(this).attr('start'));
				
				$('input[name="search-start"]').val(start);

				search(false, orderby);
			})
			
			$('.sort-onresults-option').click(function(e){
				var value = $(this).attr('id');
				var active = value + '-dd';
				
				$('#sort-wrapper dd').each(function(index, elem){
					$(elem).removeClass('active');
				});
				
				$('#' + active).addClass('active');
				
				change_sort(value);		
			});
			
			highlightcurrentsort();
			
			function highlightcurrentsort(){
				$('#sort-wrapper dd').each(function(index, elem){
					$(elem).removeClass('active');
				});
				
				var active = '<?=$sort?>';
				active = active + '-dd';
				$('#' + active).addClass('active');	
			}
			
			$('.enterprise-panel2').click(function(e){
				//e.preventDefault();
				
				var post_id = $(this).attr('id');	
				
				if(!$('#section-content-' + post_id).html()){
						
					$.ajax({
			            type : "GET",
			            url : base_url + lang + '/api/show_products',
			            dataType : "HTML",
			            data : {
			                post_id : post_id
			            }
			        }).done(function(response) {
			            $('#section-content-' + post_id).html(response);
			        });
			        			
				}
				
			});
			
			$('.enterprise-panel2, .enterprise-panel3, .qualify-post, .set-directions, .where-is').click(function(e){
				
				var post_id = $(this).attr('pid');
				
				$.ajax({
			    	type : "GET",
			        url : base_url + lang + '/api/update_views',
			        dataType : "JSON",
			            data : {
			                post_id : post_id
			            }
			        }).done(function(response) {});				
			});
			
		<? endif; ?>
		
			$('.set-directions').click(function(e){
				//e.preventDefault();
				var lat = $(this).attr('lat');
				var lng = $(this).attr('lng');
				var d = $(this).attr('dist');
				var bz_name = $(this).attr('bz-name');
				var post_id = $(this).attr('post-id');
				var phones = $(this).attr('phones');
				var address = $(this).attr('address');

				//console.log(lat);
				set_directions(lat, lng, d, bz_name, post_id, false, phones, address);
				$('#add-qualification-wrapper').foundation('reveal', 'close');
			});
			
			$('.where-is').click(function(e){
				//e.preventDefault();
				var lat = $(this).attr('lat');
				var lng = $(this).attr('lng');
				var d = $(this).attr('dist');
				var bz_name = $(this).attr('bz-name');
				var post_id = $(this).attr('post-id');
				var msg1 = $(this).attr('msg1');
				var phones = $(this).attr('phones');
				var address = $(this).attr('address');
				//console.log(lat);
				set_directions(lat, lng, d, bz_name, post_id, true, phones, address);
				$('#add-qualification-wrapper').foundation('reveal', 'close');
			});
			
			
		</script>
		