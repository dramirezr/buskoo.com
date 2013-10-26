<?= form_open('products/email/save', array('id' => 'form-product-email-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('email.title')?></legend>
		<div class="row">
	      <div class="large-4 columns">
	        <input type="email" pattern="<?=pattern('email')?>" name="email_1_<?= $bz_product_id ?>" id="email_1_<?= $bz_product_id ?>" value="<?= isset($emails[0]) ? $emails[0] : ''?>"/>
	      </div>
	    </div>
		<div class="row">
	      <div class="large-4 columns">
	        <input type="email" pattern="<?=pattern('email')?>" name="email_2_<?= $bz_product_id ?>" id="email_2_<?= $bz_product_id ?>" value="<?= isset($emails[1]) ? $emails[1] : ''?>"/>
	      </div>
	    </div>
	
 </fieldset>	
 <a href="javascript:void(0)" class="small button" id="save-website-<?= $bz_product_id ?>"><?=lang('email.save')?></a>
</form>

<script>
	$(document).ready(function(){
		
		$('#save-website-<?= $bz_product_id ?>').click(function(e){
			
			var emails = new Array();
			var error = false;
			
			$('#form-product-email-<?=$bz_product_id?>').find('input, textarea, select').each(
				function(){
					if($(this).attr('pattern') && $(this).val()){
						var reg = new RegExp($(this).attr('pattern'),'');
						var val = $(this).val();
						if(!reg.test(val)){
							error = true;
							return false;
						}else{
							emails.push(val);
						}
					}
				}
			);
			
			if(error){
				alert('<?= lang('email.formaterror') ?>');
				return false;	
			}
							 		
			$.ajax({
		        type : "POST",
		        url : $('#form-product-email-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            emails : emails,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });
		    
		});
	});
</script>