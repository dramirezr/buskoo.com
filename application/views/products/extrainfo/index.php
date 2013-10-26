<?= form_open('products/extrainfo/save', array('id' => 'form-product-extrainfo-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('extrainfo.title')?></legend>
    	<div class="row">
    		<div class="large-12 columns"><small><?=lang('extrainfo.disclaimer')?></small></div>
    	</div>
    	<div class="row">
    		<div class="large-12 columns">&nbsp;</div>
    	</div>
		<div class="row">
	      <div class="large-4 columns">
	        <textarea maxlength="<?=$max_chars?>" name="extrainfo-<?=$bz_product_id?>" id="extrainfo-<?=$bz_product_id?>"><?=$extrainfo?></textarea>
	        <small id="chcount-<?=$bz_product_id?>"></small>
	      </div>
	    </div>
	
 </fieldset>	
 <a href="#" class="small button" id="save-extrainfo-<?=$bz_product_id?>"><?=lang('extrainfo.save')?></a>
</form>

<script>
	var max_chars = <?=$max_chars?>;
	
	$(document).ready(function(){
		
		count_chars($('#extrainfo-<?=$bz_product_id?>'), $('#chcount-<?=$bz_product_id?>'), max_chars);
				
		$('#extrainfo-<?=$bz_product_id?>').keyup(function(event) {
			count_chars($(this), $('#chcount-<?=$bz_product_id?>'), max_chars);
		});
		
		$('#save-extrainfo-<?=$bz_product_id?>').click(function(e){
			e.preventDefault();
			
			var text = $('#extrainfo-<?=$bz_product_id?>').val();
			text = trunkEmails(text);
			text = trunkUrls(text);
			text = trunkMoreThan5NumbersTogether(text);
			
			$('#extrainfo-<?=$bz_product_id?>').val(text);
			count_chars($('#extrainfo-<?=$bz_product_id?>'), $('#chcount-<?=$bz_product_id?>'), max_chars);
			
			$.ajax({
		        type : "POST",
		        url : $('#form-product-extrainfo-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            extrainfo : text,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });

		});
	
	});
	

</script>