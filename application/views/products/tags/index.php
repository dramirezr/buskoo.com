<?= form_open('products/tags/save', array('id' => 'form-product-tags-'.$bz_product_id, 'class' => '')) ?>
	<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
 <fieldset>
    <legend><?=lang('tags.title')?></legend>
    	<div class="row">
    		<div class="large-12 columns"><small><?=sprintf(lang('tags.disclaimer'), $unit)?></small></div>
    	</div>
    	<div class="row">
    		<div class="large-12 columns">&nbsp;</div>
    	</div>
		<div class="row">
	      <div class="large-4 columns">
	        <textarea maxlength="<?=$max_chars?>" name="tags-<?=$bz_product_id?>" id="tags-<?=$bz_product_id?>"><?=$tags?></textarea>
	        <small><span id="wordcount-<?=$bz_product_id?>"></span> <?=lang('tags.usedwords')?></small> - <small><span id="chcount-<?=$bz_product_id?>"></span> <?=lang('tags.remainingchars')?></small>
	      </div>
	    </div>
	
 </fieldset>	
 <a href="#" class="small button" id="save-tags-<?=$bz_product_id?>"><?=lang('tags.save')?></a>
</form>

<script>
	var max_chars = <?=$max_chars?>;
	var max_words = <?=$unit?>;
	var words = 0;
	
	$(document).ready(function(){
		
		count_chars($('#tags-<?=$bz_product_id?>'), $('#chcount-<?=$bz_product_id?>'), max_chars);
		words = word_count($('#tags-<?=$bz_product_id?>'), $('#wordcount-<?=$bz_product_id?>'), max_words);
		$('#wordcount-<?=$bz_product_id?>').html(words);
		
		$('#tags-<?=$bz_product_id?>').keyup(function(event) {
			count_chars($(this), $('#chcount-<?=$bz_product_id?>'), max_chars);
			words = word_count($(this), $('#wordcount-<?=$bz_product_id?>'), max_words);
			$('#wordcount-<?=$bz_product_id?>').html(words);
		});
		
		$('#save-tags-<?=$bz_product_id?>').click(function(e){
			e.preventDefault();
			
			var text = $('#tags-<?=$bz_product_id?>').val();
			
			for(x=0; x < words; x++){
				text = trunkMoreThan1BlankSpace(text);	
			}
			
			text = text.split(" ").splice(0,max_words).join(" ");
			
			$('#tags-<?=$bz_product_id?>').val(text);
			
			count_chars($('#tags-<?=$bz_product_id?>'), $('#chcount-<?=$bz_product_id?>'), max_chars);
			
			$.ajax({
		        type : "POST",
		        url : $('#form-product-tags-<?=$bz_product_id?>').attr('action'),
		        dataType : "json",
		        data : {
		        	post_id : <?=$post->id?>,
		        	bz_product_id : <?=$bz_product_id?>,
		            tags : text,
		            hms1 : $('input[name="hms1"]').val()
		        }
		    }).done(function(response) {
		        alert(response.msg);
		    });

		});
	
	});
	

</script>