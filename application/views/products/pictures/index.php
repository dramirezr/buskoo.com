<? for($x=0; $x<$unit; $x++):?>
	<?
		$media = isset($pics[$x]) ? $pics[$x] : NULL;
	?>
	<?= form_open_multipart('products/logo/update', array('id' => 'form-product-pics-'.$x.'-'.$bz_product_id, 'class' => '')) ?>
		<input type="hidden" name="media_id" <?=isset($media->id) ? 'value="'.$media->id.'"' : ''?>/>
		<input type="hidden" name="bz_product_id" value="<?=$bz_product_id?>" />
		<div class="row" style="border-bottom: 1px solid #CCCCCC; padding-bottom: 10px;">
		  	<div class="large-2 columns" id="logo-output-<?=$x?>-<?=$bz_product_id?>">
			<? if(isset($media->hash)): ?>
			  	<img src="<?= ci_config('media_server_show_url').'/'.$media->hash; ?>"  width="80" height="80">
			  <? else: ?>
			  	<?no_logo_icon(80, 80)?>
			<? endif; ?> 
		  	</div>
			
			<div class="large-10 columns" id="logo-output">
		  		<div class="row">
		  			<div class="large-12 columns" id="process-inputs-1">
		  				<input type="file" name="picture" class="picture" id="<?="picture-$x-$bz_product_id"?>" row-index="<?=$x?>" bz-product-id="<?=$bz_product_id?>"/>
		  			</div>
		  		</div>
		  		<div class="row">
		  			<div class="large-12 columns" id="process-inputs-2">
		  				<a href="javascript:void(0)" id="<?="upload-logo-button-$x-$bz_product_id"?>" row-index="<?=$x?>" bz-product-id="<?=$bz_product_id?>" class="small button disabled upload-logo-button" ><?=lang('picture.upload')?></a>
		  			</div>
		  		</div>
		  	</div>
			
		</div>
	</form>
<? endfor; ?>

<script>
	$(document).ready(function(){
		$('.picture').change(function (e){
			var row_index = $(this).attr('row-index');
			var bz_product_id = $(this).attr('bz-product-id');
			$('#upload-logo-button-' + row_index + '-' +bz_product_id).removeClass("small button disabled upload-logo-button").addClass("small button upload-logo-button");
		})
	
	
		$('.small.button.upload-logo-button').click(function(e){
			e.preventDefault();
			
			var row_index = $(this).attr('row-index');
			var bz_product_id = $(this).attr('bz-product-id');
			var form_id = 'form-product-pics-' + row_index + '-' + bz_product_id
			
			//var val = $('#'+form_id + ' input[name="media-id"]').val();
			//console.log(val)			
			if($('#'+form_id + ' input[name="picture"]').val()){
				ajaxFileUpload_<?=$bz_product_id?>(form_id, row_index, bz_product_id);
			}
						
		});

		function ajaxFileUpload_<?=$bz_product_id?>(form_id, row_index, bz_product_id)
		{
			$('#logo-output-'+row_index+'-'+bz_product_id).html('<img src="assets/images/loading.gif">');
			$("#process-inputs-1, #process-inputs-2").hide();
			
			$.ajaxFileUpload
			(
				{
					url:'<?=current_lang()?>/products/pictures/upload',
					secureuri:false,
					fileElementId:'picture-'+row_index+'-'+bz_product_id,
					dataType: 'json',
					data:{
						post_id: <?=$post->id?>,
						user_id: <?=$user->id?>,
						bz_product_id : bz_product_id,
						media_id: $('#'+form_id + ' input[name="media_id"]').val(),
						hms1: $('input[name="hms1"]').val()
					},
					success: function (data, status){
						
						$("#process-inputs-1, #process-inputs-2").show();
						
						if(data.status == 'ok'){
							$('#'+form_id + ' input[name="picture"]').val('');
							$('#logo-output-'+row_index+'-'+bz_product_id).html('<img src="' + data.url + '" width="100" height="100" >');
							$('#'+form_id + ' input[name="media_id"]').val(data.media_id);
						}else{
							$('#logo-output-'+row_index+'-'+bz_product_id).html('');
						}
						
						alert(data.msg);			
					},
					error: function (data, status, e)
					{
						console.log(data);
					}
				}
			)
			
			return false;
	
		}		
	});
	
	
</script>