<?= form_open('products/ytvideo/save', array('id' => 'form-product-ytvideo-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('ytvideo.title')?></legend>
    	<div class="row">
    		<div class="large-12 columns"><small><?=lang('ytvideo.disclaimer')?></small></div>
    	</div>
    	<div class="row">
    		<div class="large-12 columns">&nbsp;</div>
    	</div>
		<div class="row">
	      <div class="large-4 columns">
	        <input type="url" name="ytvideo-<?=$bz_product_id?>" id="ytvideo-<?=$bz_product_id?>" value="<?=$ytvideo?>">
	      </div>
	    </div>
	
 </fieldset>	
 <a href="#" class="small button" id="save-ytvideo-<?=$bz_product_id?>"><?=lang('ytvideo.save')?></a>
</form>

<script>
	$(document).ready(function(){
		$('#save-ytvideo-<?=$bz_product_id?>').click(function(e){
			
			var url = $('#ytvideo-<?=$bz_product_id?>').val();

			$.ajax({
		        type : "POST",
		        url : $('#form-product-ytvideo-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            ytvideo : url,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });

			
		});
	});
</script>