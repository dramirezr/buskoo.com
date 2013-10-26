<?= form_open('products/website/save', array('id' => 'form-product-website-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('website.title')?></legend>
		<div class="row">
	      <div class="large-4 columns">
	        <input type="url" pattern="<?=pattern('url')?>" name="website_<?= $bz_product_id ?>" id="website_<?= $bz_product_id ?>" value="<?= $website ? $website : 'http://'?>"/>
	      </div>
	    </div>
	
 </fieldset>	
 <a href="javascript:void(0)" class="small button" id="save-website-<?= $bz_product_id ?>"><?=lang('website.save')?></a>
</form>

<script>
	$(document).ready(function(){
		
		$('#save-website-<?= $bz_product_id ?>').click(function(e){
					
			var reg = new RegExp($('#website_<?= $bz_product_id ?>').attr('pattern'), '');
			var val = $('#website_<?= $bz_product_id ?>').val();
			if(!reg.test(val)){
				alert('<?= lang('website.formaterror') ?>');
				return false;
			}
			 		
			$.ajax({
		        type : "POST",
		        url : $('#form-product-website-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            website : val,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });
		    
		});
	});
</script>