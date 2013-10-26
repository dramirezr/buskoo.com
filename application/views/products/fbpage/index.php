<?= form_open('products/fbpage/save', array('id' => 'form-product-fbpage-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('fbpage.title')?></legend>
		<div class="row">
	      <div class="large-4 columns">
	        <input type="url" pattern="<?=pattern('fburl')?>" name="fbpage_<?= $bz_product_id ?>" id="fbpage_<?= $bz_product_id ?>" value="<?= $fbpage ? $fbpage : 'http://'?>"/>
	      </div>
	    </div>
	
 </fieldset>	
 <a href="javascript:void(0)" class="small button" id="save-fbpage-<?= $bz_product_id ?>"><?=lang('fbpage.save')?></a>
</form>

<script>
	$(document).ready(function(){
		
		$('#save-fbpage-<?= $bz_product_id ?>').click(function(e){
					
			var reg = new RegExp($('#fbpage_<?= $bz_product_id ?>').attr('pattern'), '');
			var val = $('#fbpage_<?= $bz_product_id ?>').val();
			if(!reg.test(val)){
				alert('<?= lang('fbpage.formaterror') ?>');
				return false;
			}
			 		
			$.ajax({
		        type : "POST",
		        url : $('#form-product-fbpage-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            fbpage : val,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });
		    
		});
	});
</script>