<? header('Content-Type: text/html'); ?>

<? if(count($products)): ?>

<div class="section-container auto" data-section="auto">
  <? foreach($products as $index => $p): ?>
  <section>
    <p class="title" data-section-title><a href="javascript:void(0)" id="link-product-setup-<?= $p->id ?>"  ><?= $p->name ?></a></p>
    <div class="content" data-section-content id="<?= $p->id ?>"></div>
  </section>
  <script>
  	$(document).ready(function(){
  		$('#link-product-setup-<?= $p->id ?>').click(function(e){
  			load_product_setup('<?= $p->helper_file ?>', '<?= $p->id ?>');
  		})
  	});
  </script>
  <? endforeach; ?>  
</div>

  <script>
	function load_product_setup(controller, wrapper_id){
		var post_id = <?= $post_id ?>;
		
		$.ajax({
            type : "GET",
            url : '<?=current_lang()?>/products/' + controller,
            dataType : "html",
            data : {
            	post_id : post_id,
            	post_product_id: wrapper_id
            }
        }).done(function(response) {
			$('#' + wrapper_id).html(response);	
        });				
		
	}
  </script>

<? elseif(count($not_active_products)): ?>
	<h3><?=lang('bizpanel.setup.pendingproducts')?></h3>
<? else: ?>
	<h3><?=lang('bizpanel.setup.noproducts')?></h3>
	<a href="javascript:void(0)" class="small button" id="setup-add-products"><?=lang('bizpanel.setup.addproducts')?></a>
	<script>
		$(document).ready(function(){
			
			$('#setup-add-products').click(function(e){
				
				$.ajax({
		            type : "GET",
		            url : '<?=base_url($this->lang->lang().'/account/create_enterprise_form')?>',
		            dataType : "html",
		            data : {
		            	user_id: <?=$user->id?>,
		            	bz_id : $('input[name="bz-id"]').val()
		            }
		        }).done(function(response) {
		        	$('#biz-control-panel').html(response);
		        });	
		        
			})
		})
		
	</script>
<? endif; ?>