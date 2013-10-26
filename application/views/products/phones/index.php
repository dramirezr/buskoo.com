<?= form_open('products/phones/save', array('id' => 'form-product-phones-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('phones.title')?></legend>
    	<div class="row">
    		<div class="large-12 columns"><small><?=lang('phones.maskland')?>: 593-0X-XXXXXXX, <?=lang('phones.maskcell')?>: 593-0X-XXXXXXXXX</small></div>
    	</div>
    	<div class="row">
    		<div class="large-12 columns">&nbsp;</div>
    	</div>
    	
	<? for($x=0; $x<$unit; $x++):?>		
		<div class="row">
	      <div class="large-4 columns">
	        <input type="tel" maxlength="16" pattern="<?=pattern('phone')?>" class="phone" name="phone-<?=$x?>" value="<?=isset($phones[$x]) ? $phones[$x] : ''?>"/>
	      </div>
	    </div>
	<? endfor; ?>	
	
 </fieldset>	
 <a href="#" class="small button" id="save-phones"><?=lang('phones.save')?></a>
</form>

<script>
	$(document).ready(function(){
		
		$(".phone").mask("(593) 0#-#######?##");
		
		//$('.phone').keypress(function(event) {
		//	keysForPhones(event);
		//});
		
		$('#save-phones').click(function(e){
			var error = false;
			var phones = new Array();
			$('#form-product-phones-<?=$bz_product_id?>').find('input, textarea, select').each(
				function(){
					if($(this).attr('pattern') && $(this).val()){
						var reg = new RegExp($(this).attr('pattern'),'');
						var val = $(this).val();
						if(!reg.test(val)){
							$(this).css('background-color', 'red');
							error = true;						
						}else{
							$(this).css('background-color', '#FFFFFF');
							phones.push(val);
						}
					}
				}
			);
			
			if(error == true)
				return false;
						
			$.ajax({
		        type : "POST",
		        url : $('#form-product-phones-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            phones : phones,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });
		    
		});
	});
</script>