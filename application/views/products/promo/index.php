<?= form_open('products/promo/save', array('id' => 'form-product-promo-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('promo.title')?></legend>
    	<div class="row">
    		<div class="large-12 columns"><small><?=lang('promo.disclaimer')?></small></div>
    	</div>
    	<div class="row">
    		<div class="large-12 columns">&nbsp;</div>
    	</div>
		<div class="row">
	      <div class="large-4 columns">
	      	
	        <input type="text" placeholder="<?=lang('promo.name')?>" maxlength="<?=ci_config('promo.name.max_characters')?>" name="promo-name-<?=$bz_product_id?>" id="promo-name-<?=$bz_product_id?>" value="<?= isset($promo['name']) ? $promo['name'] : '' ?>"/>
	      </div>
	    </div>

		<div class="row">
	      <div class="large-4 columns">
	      	 <label for="promo-date-<?=$bz_product_id?>"><?=lang('promo.date')?></label> <small>dd-mm-aaaa</small>
	        <input type="date" maxlength="10" name="promo-date-<?=$bz_product_id?>" id="promo-date-<?=$bz_product_id?>" value="<?= isset($promo['date']) ? $promo['date'] : ''?>"/>
	      </div>
	    </div>

		<div class="row">
	      <div class="large-4 columns">
	        <textarea maxlength="<?=ci_config('promo.desc.max_characters')?>" placeholder="<?=lang('promo.desc')?>" name="promo-desc-<?=$bz_product_id?>" id="promo-desc-<?=$bz_product_id?>"><?= isset($promo['desc']) ? $promo['desc'] : '' ?></textarea>
	        <small><span id="chcount-<?=$bz_product_id?>"></span> <?=lang('promo.remainingchars')?></small>
	      </div>
	    </div>
	
 </fieldset>	
 <a href="#" class="small button" id="save-promo-<?=$bz_product_id?>"><?=lang('promo.save')?></a>
</form>

<script>
	var max_chars = <?=ci_config('promo.desc.max_characters')?>;
	
	$(document).ready(function(){
		
		count_chars($('#promo-desc-<?=$bz_product_id?>'), $('#chcount-<?=$bz_product_id?>'), max_chars);

		$('#promo-desc-<?=$bz_product_id?>').keyup(function(event) {
			count_chars($(this), $('#chcount-<?=$bz_product_id?>'), max_chars);
		});
		
		<? if($this->agent->is_browser('Firefox')): ?>
		$("#promo-date-<?=$bz_product_id?>").mask("9999-99-99");
		<? endif; ?>
		
		$('#save-promo-<?=$bz_product_id?>').click(function(e){
			e.preventDefault();
			
			var name = $('#promo-name-<?=$bz_product_id?>').val();
			var desc = $('#promo-desc-<?=$bz_product_id?>').val();
			var date = $('#promo-date-<?=$bz_product_id?>').val();
			
			if(!name){
				alert('<?=lang('promo.requiredname')?>');
				return false;
			}

			if(!desc){
				alert('<?=lang('promo.requireddesc')?>');
				return false;
			}
			
			if(date && !isValidDate(date)){
				alert('<?=lang('promo.notvaliddate')?>');
				return false;
			}

			$.ajax({
		        type : "POST",
		        url : $('#form-product-promo-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            name : name,
		            desc : desc,
		            date : date,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });
		});
		
		
	});
</script>