<h4><?=$biz->name?> (<?=lang('bizpanel.setup.score').': '.$biz->score_avg?>)</h4>

<? if($biz->state == 'I'): ?>
<h1><?=lang('bizpanel.setup.pendingconfirmation')?></h1>
<a class="close-reveal-modal">&#215;</a>
<? else: ?>
<div class="section-container auto" data-section>
  <section>
    <p class="title" data-section-title><a href="#panel1"><?=lang('bizpanel.tab1')?></a></p>
    <div class="content" data-slug="section1">
		<div class="row hide" id="updatebiz-error-wrapper">
			<div data-alert class="alert-box alert">
	  			<span id="updatebiz-error-msg"></span>
			</div>
		</div>

    	<?= form_open('account/update_business', array('id' => 'addbiz-form-upd', 'class' => '')) ?>
    	
    	<? show_biz_form($biz) ?>

	    <div class="row">
	      <div class="large-12 columns">&nbsp;</div>
	    </div>

	    <div class="row">
	      <div class="large-12 columns">
			<a href="javascript:void(0)" id="bizpanel-update" class="tiny button"><?=lang('bizpanel.btn.update')?></a>
	      </div>
	    </div>
    	
    	</form>
    	
    </div>
  </section>

  <section>
    <p class="title" data-section-title><a href="#panel3" id="bizpanel-tab3"><?=lang('bizpanel.tab3')?></a></p>
    <div class="content" data-slug="section3" id="bizpanel-tab3-wrapper"></div>
  </section>
  
  <section>
    <p class="title" data-section-title><a href="#panel2" id="bizpanel-tab2"><?=lang('bizpanel.tab2')?></a></p>
    <div class="content" data-slug="section2" id="bizpanel-tab2-wrapper"></div>
  </section>

  <section>
    <p class="title" data-section-title><a href="#panel4" id="bizpanel-tab4"><?=lang('bizpanel.tab4')?></a></p>
    <div class="content" data-slug="section4" id="bizpanel-tab4-wrapper"></div>
  </section>

</div>

<a class="close-reveal-modal">&#215;</a>

<script>
	
	$('#add-enterprise-form').html('');
	
	$(document).ready(function(){

		$('#bz-phones').keypress(function(event) {
			keysForPhones(event);
		});
		
		$('#bizpanel-tab3').click(function(e){
			if(!$('#bizpanel-tab3-wrapper').html()){
				$.ajax({
		            type : "POST",
		            url : '<?=current_lang()?>/account/get_biz_products_list',
		            dataType : "html",
		            data : {
		            	user_id: <?=$user->id?>,
		            	bz_id : $('input[name="bz-id"]').val(),
		            	hms1 : $('input[name="hms1"]').val()
		            }
		        }).done(function(response) {
		        	$('#bizpanel-tab3-wrapper').html(response);
		        });					
			}
		})
		
		$('#bizpanel-tab2').click(function(e){
			//if(!$('#bizpanel-tab2-wrapper').html()){
				$.ajax({
		            type : "POST",
		            url : '<?=current_lang()?>/account/get_biz_products',
		            dataType : "html",
		            data : {
		            	user_id: <?=$user->id?>,
		            	bz_id : $('input[name="bz-id"]').val(),
		            	hms1 : $('input[name="hms1"]').val()
		            }
		        }).done(function(response) {
		        	$('#bizpanel-tab2-wrapper').html(response);
		        	//$('#bizpanel-tab2-wrapper').foundation('section', 'reflow');
		        	$(document).foundation('section','reflow');
		        });				
			//}
		});
		
		$('#bizpanel-tab4').click(function(e){
				$.ajax({
		            type : "POST",
		            url : '<?=current_lang()?>/account/get_invoices',
		            dataType : "html",
		            data : {
		            	user_id: <?=$user->id?>,
		            	bz_id : $('input[name="bz-id"]').val(),
		            	hms1 : $('input[name="hms1"]').val()
		            }
		        }).done(function(response) {
		        	$('#bizpanel-tab4-wrapper').html(response);
		        	//$('#bizpanel-tab2-wrapper').foundation('section', 'reflow');
		        	$(document).foundation('section','reflow');
		        });			
		});
		
		$('#bizpanel-update').click(function(e){
			var url = $('#addbiz-form-upd').attr('action');
			var bz_type = $('select[name="bz-type"]').val();
			var name = $('input[name="bz-name"]').val();
			var phone = $('#bz-phones').val();
			var lat = $('input[name="bz-lat"]').val();
			var lng = $('input[name="bz-lng"]').val();
			var phoneReg = /<?=pattern('phone')?>/;
			
			
			if(!name || !lat || !lng || !bz_type || !phone){
				$('#updatebiz-error-msg').html('<?=lang('createbiz.error.requiredfields')?>');
				$('#updatebiz-error-wrapper').show();
				return false;
			}
			
			if (!phoneReg.test(phone)) {
				$('#updatebiz-error-msg').html('<?=lang('createbiz.error.phoneformat')?>');
				$('#updatebiz-error-wrapper').show();
					return false;
			}
			
	        $.ajax({
	            type : "POST",
	            url : url,
	            dataType : "json",
	            data : {
	            	user_id: <?=$user->id?>,
	            	bz_id : $('input[name="bz-id"]').val(),
					bz_type_id : $('select[name="bz-type"]').val(),
					bz_name : $('input[name="bz-name"]').val(),
					bz_desc : '',
					bz_addr : '',
					bz_phones : $('input[name="bz-phones"]').val(),
					bz_ceo : '',
					bz_email : $('input[name="bz-email"]').val(),
					bz_lat : $('input[name="bz-lat"]').val(),
					bz_lng : $('input[name="bz-lng"]').val(),
					hms1 : $('input[name="hms1"]').val()
	            }
	        }).done(function(response) {
	        	if(response.status == 'ok'){
					alert(response.msg);
	        	}
	        });
			
		});
			
	});
	</script> 
<? endif; ?>	