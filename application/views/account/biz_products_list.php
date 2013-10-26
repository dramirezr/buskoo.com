<? header('Content-Type: text/html'); ?>

<? if(count($products)): ?>
<?= form_open('api', array('id' => 'bz-products-list-form', 'class' => '')) ?>
</form>
 
<div class="row">
  <div class="small-12 small-centered columns">
 
	<table>
	  <thead>
	    <tr>
	      <th width="200"><?=lang('bizpanel.products.th1')?></th>
	      <th><?=lang('bizpanel.products.th2')?></th>
	      <th width="150">&nbsp;</th>
	    </tr>
	  </thead>
	  <tbody>
	  <? foreach($products as $index => $p): ?>
	    <tr>
	      <td><?= $p->name ?></td>
	      <td><?= $p->description ?></td>
	      <td>
	      	<? if($p->active == 1): ?>
	      	<a href="javascript:void(0)" class="small button alert disable-product" bz-product-id="<?=$p->id?>" id="disable-btn-<?=$p->id?>"><?=lang('bizpanel.products.disable')?></a>
	      	<a href="javascript:void(0)" class="small button enable-product" bz-product-id="<?=$p->id?>" id="enable-btn-<?=$p->id?>" style="display:none;"><?=lang('bizpanel.products.enable')?></a>
	      	<? else: ?>
	      	<a href="javascript:void(0)" class="small button alert disable-product" bz-product-id="<?=$p->id?>" id="disable-btn-<?=$p->id?>" style="display:none;"><?=lang('bizpanel.products.disable')?></a>
	      	<a href="javascript:void(0)" class="small button enable-product" bz-product-id="<?=$p->id?>" id="enable-btn-<?=$p->id?>"><?=lang('bizpanel.products.enable')?></a>
	      	<? endif; ?>
	      </td>
	    </tr>
	  <? endforeach; ?>  
	  </tbody>
	</table>
  
  </div>
</div>

<div class="row">
	<div class="large-12 columns">
		<?=lang('bizpanel.products.availableprod')?>
	</div>
</div>

<div class="row">
	<div class="large-12 columns">&nbsp;</div>
</div>

<div class="row">
	<div class="large-12 columns">
		<a href="javascript:void(0)" class="small button" id="bizpanel-products-addmoreprod"><?=lang('bizpanel.products.addmoreprod')?></a>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('a.small.button.alert.disable-product').click(function(e){
			e.preventDefault();
			
			if(!confirm('<?=lang('bizpanel.products.inactivateconfirm')?>'))
				return false;
			
			var bz_product_id = $(this).attr('bz-product-id');
			
			$.ajax({
				type : "POST",
				url : '<?=current_lang()?>/api/disable_product',
		            dataType : "json",
		            data : {
		            	user_id: <?=$user->id?>,
		            	bz_product_id : $(this).attr('bz-product-id'),
		            	hms1 : $('input[name="hms1"]').val()
		            }
		    }).done(function(response) {
		        if(response.status == 'ok'){
		        	
		        	$('#disable-btn-' + bz_product_id).hide();
		        	$('#enable-btn-' + bz_product_id).show();
		        }
			});			
		});	
		
		$('a.small.button.enable-product').click(function (e){
			e.preventDefault();

			if(!confirm('<?=lang('bizpanel.products.activateconfirm')?>'))
				return false;
			
			var bz_product_id = $(this).attr('bz-product-id');
			
			$.ajax({
				type : "POST",
				url : '<?=current_lang()?>/api/enable_product',
		            dataType : "json",
		            data : {
		            	user_id: <?=$user->id?>,
		            	bz_product_id : $(this).attr('bz-product-id'),
		            	hms1 : $('input[name="hms1"]').val()
		            }
		    }).done(function(response) {
		        if(response.status == 'ok'){
		        	$('#disable-btn-' + bz_product_id).show();
		        	$('#enable-btn-' + bz_product_id).hide();
		        }
			});			

		});	
		
		$('#bizpanel-products-addmoreprod').click(function (e){
			
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
		});
	})
</script>


<? elseif(count($not_active_products)): ?>
	<h3><?=lang('bizpanel.setup.pendingproducts')?></h3>
<? else: ?>
	<h3><?=lang('bizpanel.setup.noproducts')?></h3>
	<a href="javascript:void(0)" class="small button" id="setup-add-products"><?=lang('bizpanel.setup.addproducts')?></a>
	<script>
		$(document).ready(function(){
			
			$('a.small.button.alert').click(function(e){
				console.log($(this));
			});
			
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