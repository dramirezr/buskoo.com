<h4><?=lang('createbiz.title')?></h4>
<?= form_open('account/add_business', array('id' => 'addbiz-form', 'class' => '')) ?>

  <fieldset id="step1-wrapper">
    <legend><?=lang('createbiz.step1')?></legend>

	<div class="row hide" id="createbiz-error-wrapper">
		<div data-alert class="alert-box alert">
  			<span id="createbiz-error-msg"></span>
		</div>
	</div>
    
	<? show_biz_form($biz) ?>

    <div class="row">
      <div class="large-12 columns">&nbsp;</div>
    </div>

    <div class="row">
      <div class="large-12 columns">
		<a href="javascript:void(0)" id="step1-next" class="tiny button"><?=lang('createbiz.btn.next')?></a>
      </div>
    </div>
		
  </fieldset>

  <!-- STEP 2 -->	
  <fieldset id="step2-wrapper">
    <legend><?= lang('createbiz.step2') ?></legend>
	<h6 class="subheader"><?=lang('createbiz.step2.subheader')?></h6>
	
	<? if(isset($next_billing_date)): ?>
	<h6 class="subheader"><?= sprintf(lang('createbiz.step2.prorateprice'), $next_billing_date) ?></h6>
	<? endif; ?>
	
    <div class="row">
		<div class="small-3 columns">
	        <label><?=lang('createbiz.billingcicle')?></label>
	        <? if(isset($billing_cycle)): ?>
	        	<?= ($billing_cycle == 12) ? lang('createbiz.billingcicle.yearly') : lang('createbiz.billingcicle.monthly') ?>
	        	<input type="hidden" name="billing-cycle" id="billing-cycle" value="<?=$billing_cycle?>"/>
	        <? else: ?>
	        <select name="billing-cycle" id="billing-cycle">
	        	<option value="12"><?=lang('createbiz.billingcicle.yearly')?></option>
	        	<option value="1"><?=lang('createbiz.billingcicle.monthly')?></option>
	        </select>       
	        <? endif; ?>
		</div>

		<div class="small-3 columns">
	        <label><?=lang('createbiz.product')?></label>
	        <? if($post_id && isset($billing_cycle)): ?>
	        <select name="product-list" id="product-list">
				<? if(is_array($products)): ?>
					<? foreach($products as $p): ?>
						<option value="<?= $p->id ?>"><?= $p->name ?> $<?= $p->price ?></option>
					<? endforeach; ?>
				<? endif; ?>		        
	        </select>
	        <? else: ?>
	        <select name="product" id="product-list">
				<? if(is_array($products)): ?>
					<? foreach($products as $p): ?>
					<? if($p->billing_cycle == 12): ?>
						<option value="<?= $p->id ?>"><?= $p->name ?> $<?= $p->price ?></option>
					<? endif; ?>
					<? endforeach; ?>
				<? endif; ?>		        
	        </select>
	        <? endif; ?>
		</div>

		<div class="small-3 columns">
			 <label>&nbsp;</label>
			<a href="javascript:void(0)" id="add-product" class="tiny button">+</a>	
		</div>
		<div class="small-3 columns"></div>
    </div>
	
	<div class="row">
		<div class="large-12 large-centered columns">
			<table id="invoice">
			  <thead>
			    <tr>
			      <th><?=lang('createbiz.item')?></th>
			      <th class="hide-for-small"><?=lang('createbiz.description')?></th>
			      <th><?=lang('createbiz.price')?></th>
			      <th></th>
			    </tr>
			  </thead>
			  
			  <tbody>
			  </tbody>
			  
			</table>
		</div>
	</div>

    <div class="row">
      <div class="small-3 columns"><h6 class="subheader">Sub Total :</h6></div>
      <div class="small-3 columns"><h6>$<span id="sub-total">0</span></h6></div>
      <div class="small-6 columns"></div>
    </div>
    <div class="row">
      <div class="small-3 columns"><h6 class="subheader">IVA <?=get_config_val('iva')?>%:</h6></div>
      <div class="small-3 columns"><h6>$<span id="iva">0</span></h6></div>
      <div class="small-6 columns"></div>
    </div>
    
    <div class="row">
      <div class="small-3 columns"><h5 class="subheader">Total:</h5></div>
      <div class="small-3 columns"><h5>$<span id="total">0</span></h5></div>
      <div class="small-6 columns"></div>
    </div>

    <div class="row">
      <div class="small-12 columns">
      	<? if(!$post_id): ?>
      	<a href="javascript:void(0)" id="step2-prev" class="tiny button"><?=lang('createbiz.btn.prev')?></a>
      	<? endif; ?>
		<a href="javascript:void(0)" id="step2-next" class="tiny button"><?=lang('createbiz.btn.next')?></a>
		<? if($post_id): ?>
		<a href="javascript:void(0)" id="step2-cancel" class="tiny button"><?=lang('createbiz.btn.cancel')?></a>
      	<? endif; ?>
      </div>
    </div>
  </fieldset>

  <fieldset id="step3-wrapper">
    <legend><?=lang('createbiz.step3')?></legend>
    <div class="row">
      <div class="large-12 columns">
        <label><?=lang('createbiz.step3.subheader')?></label>
        <textarea disabled style="height: 175px"><?=get_config_val('adding_bz_terms_n_cond_'.$this->lang->lang())?></textarea>
      </div>
    </div>

    <div class="row">
      <div class="large-12 columns">
      	<label for="bz-accept"><input name="bz-accept" type="checkbox" id="bz-accept" > <?=lang('createbiz.accept')?></label>
      </div>
    </div>

    <div class="row">
      <div class="large-12 columns">
      	<p><?=lang('createbiz.postdisclaimer')?></p>
      </div>
    </div>

    <div class="row">
      <div class="large-12 columns">&nbsp;</div>
    </div>

    <div class="row">
      <div class="large-12 columns">
      	<a href="javascript:void(0)" id="step3-prev" class="tiny button"><?=lang('createbiz.btn.prev')?></a>
		<a href="javascript:void(0)" id="step3-post" class="tiny button success"><?=lang('createbiz.btn.post')?></a>
		<a href="javascript:void(0)" id="step3-pay" class="tiny button success"><?=lang('createbiz.btn.pay')?></a>
      </div>
    </div>

  </fieldset>
  

  <fieldset id="step4-wrapper">
    <legend><?=lang('createbiz.step4')?></legend>

	<div class="row hide" id="createbiz-step4-error-wrapper">
		<div data-alert class="alert-box alert">
  			<span id="createbiz-step4-error-msg"></span>
		</div>
	</div>
    
    <h6 class="subheader"><?=lang('createbiz.step4.subheader')?></h6>

    <div class="row">
      <div class="small-3 columns"><h3 class="subheader">Total:</h3></div>
      <div class="small-3 columns"><h3>$<span id="total-pay">0</span></h3></div>
      <div class="small-6 columns"></div>
    </div>

    <div class="row">

		  <div class="large-4 columns">
		  	<div class="panel">
				<h5><?=lang('createbiz.paymethods.bankdeposit')?></h5>
			  	<p><?=lang('createbiz.paymethods.bankdeposit.desc')?></p>
			  	<p><h6><small><?=lang('createbiz.paymethods.disclaimer1')?></small></h6></p>
			  	<div class="row">
	  				<div class="small-3 small-centered columns"><input type="radio" name="payment_method" value="bank_transfer_deposit"></div>
				</div>
		  	</div>
		  </div>

		 <!-- <div class="large-3 columns">
		  	<div class="panel">
			  	<table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"></td></tr><tr><td align="center"><a href="https://www.paypal.com/es/webapps/mpp/paypal-popup" title="Cómo funciona PayPal" onclick="javascript:window.open('https://www.paypal.com/es/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" border="0" alt="PayPal Logo"></a></td></tr></table>
			  	<p>Pague con su tarjeta de crédito y publique su negocio de inmediato.</p>
			  	<div class="row">
	  				<div class="small-3 small-centered columns"><input type="radio" name="payment_method" value="paypal"></div>
				</div>
		  	</div>
		  </div>
		-->
		  <div class="large-4 columns">
		  	<div class="panel">
				<h5>*<?=lang('createbiz.paymethods.moneypickup')?></h5>
			  	<p><?=lang('createbiz.paymethods.moneypickup.desc')?></p>
			  	<p><?= sprintf(lang('createbiz.paymethods.moneypickup.disclaimer'), 
			  			get_config_val('money_pickup_cost'), 
			  			get_config_val('money_pickup_allowed_places'), 
			  			get_config_val('money_pickup_value_restriction')) ?> </p>
			  	<p><h6><small><?=lang('createbiz.paymethods.disclaimer1')?></small></h6></p>
			  	<div class="row">
	  				<div class="small-3 small-centered columns"><input type="radio" name="payment_method" value="money_pickup" disabled></div>
				</div>
		  	</div>
		  </div>    

		  <div class="large-4 columns">
		  	<div class="panel">
				<h5>*<?=lang('createbiz.paymethods.moneyorder')?></h5>
			  	<p><?=lang('createbiz.paymethods.moneyorder.desc')?></p>
			  	<p><?= sprintf(lang('createbiz.paymethods.moneyorder.disclaimer'), 
			  			get_config_val('money_pickup_cost'), 
			  			get_config_val('money_order_value_restriction')) ?></p>
			  	<p><h6><small><?=lang('createbiz.paymethods.disclaimer1')?></small></h6></p>
			  	<div class="row">
	  				<div class="small-3 small-centered columns"><input type="radio" name="payment_method" value="money_order" disabled></div>
				</div>
		  	</div>
		  </div>    

    </div>
	
	<h3><?=lang('createbiz.warning')?></h3>
    <div class="row">
      <div class="large-12 columns">
		<div data-alert class="alert-box">
			<?=sprintf(lang('createbiz.payment.confirmation.process'), get_config_val('payment_confirmation_email'))?>
		</div>      
      </div>
    </div>
	
	<h6 class="subheader"><?=lang('createbiz.step4.dataconfirmation')?></h6>
	
    <div class="row">
      <div class="large-3 columns">
        <label><?=lang('createbiz.billname')?></label>
        <input type="text" name="bz-bill-name" value="<?= $user->name ?>"/>
      </div>
      <div class="large-3 columns">
        <label><?=lang('createbiz.billid')?></label>
        <input type="text" name="bz-bill-id" value="<?= $user->identification ?>" />
      </div>
      <div class="large-3 columns">
        <label><?=lang('createbiz.billaddr')?></label>
        <input type="text" name="bz-bill-addr" value="<?= $user->address ?>" />
      </div>
      <div class="large-3 columns">
        <label><?=lang('createbiz.selerid')?></label>
        <input type="text" name="bz-seller-id" value="" />
      </div>
     
    </div>

    <div class="row">
      <div class="large-12 columns">&nbsp;</div>
    </div>
    
    <div class="row">
      <div class="large-12 columns">
      	<a href="javascript:void(0)" id="step4-prev" class="tiny button"><?=lang('createbiz.btn.prev')?></a>
      	<a href="javascript:void(0)" id="step4-pay" class="tiny button"><?=lang('createbiz.btn.pay')?></a>
      </div>
    </div>

  </fieldset>
    
</form>

  <fieldset id="step5-wrapper">
    <legend><?=lang('createbiz.step5')?></legend>
     <h6 class="subheader"><?=lang('createbiz.step5.subheader')?></h6>

    <div class="row" id="email-news">
      <div class="large-12 columns">
      	<?= lang('createbiz.emailnews') ?>
      </div>
    </div>

    <div class="row">
      <div class="large-12 columns">&nbsp;</div>
    </div>

    <div class="row" id="paid-process">
      <div class="large-12 columns">
      	<?= get_confirmation_process() ?>
      </div>
    </div>

    <div class="row" id="free-process">
      <div class="large-12 columns">
      	<?= lang('createbiz.free.confirmation') ?>
      </div>
    </div>

	<? if($post_id): ?>
	    <div class="row">
	      <div class="large-12 columns">
	      	<a href="javascript:void(0)" id="step5-ok" class="tiny button"><?=lang('createbiz.btn.close')?></a>
	      </div>
	    </div>
	<? endif; ?>
	
  </fieldset>
	
	<fieldset id="waiting">
		<legend><?=lang('createbiz.processing')?></legend>
		<div class="row full-width">
			<div class="small-1 small-centered columns"><?= img('assets/images/loading.gif'); ?></div>
		</div>
	</fieldset>

<a class="close-reveal-modal">&#215;</a>

<script>
var addbz_map;
var addbz_marker = null;
var products = [
	<? if(is_array($products)): ?>
		<? foreach($products as $p): ?>
		{
			id: <?= $p->id ?>, 
			name: '<?= $p->name ?>', 
			description: '<?= addslashes($p->description) ?>', 
			type: '<?= $p->type ?>', 
			billing_cycle: <?= $p->billing_cycle ?>, 
			price: <?= $p->price ?>,
			unit: <?= $p->unit ?>,
		},
		<? endforeach; ?>
	<? endif; ?>
];

var product_list = new Array();
var total = 0;
var sub_total = 0;
var iva = 0;
var iva_factor = <?=get_config_val('iva')?> / 100;

var in_billing_cycle = <?= isset($not_in_billing_cycle) ? 0 : 1 ?>; 

<? if(!$biz): ?>
$('#biz-control-panel').html('');
<? endif; ?>

$(document).ready(function(){
	
	$('#bz-phones').keypress(function(event) {
		keysForPhones(event);
	});
	
	<? if($post_id): ?>
	$('#step1-wrapper, #step3-wrapper, #step3-post, #step3-pay ,#step4-wrapper, #step5-wrapper, #step4-pay, #paid-process, #waiting, #free-process').hide();
	<? else: ?>
	$('#step2-wrapper, #step3-wrapper, #step3-post, #step3-pay ,#step4-wrapper, #step5-wrapper, #step4-pay, #paid-process, #waiting, #free-process').hide();
	<? endif; ?>
	
	setTotal();
	
	$('#step1-next').click(function(e){
		e.preventDefault();		
		
		var bz_type = $('select[name="bz-type"]').val();
		var name = $('input[name="bz-name"]').val();
		var phone = $('input[name="bz-phones"]').val();
		var lat = $('input[name="bz-lat"]').val();
		var lng = $('input[name="bz-lng"]').val();
		var phoneReg = /<?=pattern('phone')?>/;
		
		
		if(!name || !lat || !lng || !bz_type || !phone){
			$('#createbiz-error-msg').html('<?=lang('createbiz.error.requiredfields')?>');
			$('#createbiz-error-wrapper').show();
			return false;
		}
		
		if (phone && !phoneReg.test(phone)) {
			$('#createbiz-error-msg').html('<?=lang('createbiz.error.phoneformat')?>');
			$('#createbiz-error-wrapper').show();
				return false;
		}			
		
		$('#step1-wrapper').hide();
		$('#step2-wrapper').show();
	});
	
	$('#step2-prev').click(function(e){
		e.preventDefault();		
		$('#step1-wrapper').show();
		$('#step2-wrapper').hide();
	});

	$('#step2-next').click(function(e){
		e.preventDefault();
		
		<? if($post_id): ?>
		if(!total)
			return false;
		<? endif; ?>
				
		$('#step2-wrapper').hide();
		$('#step3-wrapper').show();
	});
	
	<? if($post_id): ?>
	$('#step2-cancel, #step5-ok').click(function(e){
		$.ajax({
			type : "GET",
		    url : '<?=base_url($this->lang->lang().'/api/open_business_panel')?>',
		    dataType : "html",
		    data : {post_id: <?=$post_id?>}
		}).done(function(response) {
			$('#biz-control-panel').html(response);
			$(document).foundation('section','reflow');
		});			
	});
	<? endif; ?>

	$('#step3-prev').click(function(e){
		e.preventDefault();
		
		$('#bz-accept').attr('checked', false);
		$('#step3-post, #step3-pay').hide();
		
		$('#step2-wrapper').show();
		$('#step3-wrapper').hide();
	});
	
	$('#bz-accept').click(function(e){
		
		if($(this).is(':checked')){
			if(total){
				$('#step3-pay').show();
			}else{
				$('#step3-post').show();
			}			
		}else{
			$('#step3-post, #step3-pay').hide();
		}
		
	});
	
	$('#step3-pay').click(function(e){
		e.preventDefault();
		
		$('#step3-wrapper').hide();
		$('#step4-wrapper').show();

		
		if(total >= <?=get_config_val('money_pickup_value_restriction')?>){
			$('input[value="money_pickup"]').attr('disabled', false);
		}else{
			$('input[value="money_pickup"]').attr('disabled', true);
		}

		if(total >= <?=get_config_val('money_order_value_restriction')?>){
			$('input[value="money_order"]').attr('disabled', false);
		}else{
			$('input[value="money_order"]').attr('disabled', true);
		}
		
	});

	$('#step4-prev').click(function(e){
		e.preventDefault();
		
		$('#step3-wrapper').show();
		$('#step4-wrapper').hide();
					
	});
		
	$('#billing-cycle').change(function(e){
		
		$('#product-list').html('');
		
		var billing_cycle = $(this).val();
		$.each(products, function(index, item) {
 	 		if(billing_cycle == item.billing_cycle){
 	 			$('#product-list').append($('<option>', { 
        			value: item.id,
        			text : item.name + ' $' + item.price  
    			}));
 	 		}
		});
		
		$("#invoice > tbody").html("");
		product_list = new Array();
		total = 0;
		setTotal();
	});
	
	$('#add-product').click(function(e){
		var current = $('#product-list').val();

		$.each(products, function(index, item) {
			//console.log(item);
 	 		if(current == item.id && !isAddedProduct(item.id)){
 	 			$('#invoice > tbody:last').append(getItemRow(item));
 	 			product_list.push(item.id);
 	 			setTotal(sub_total + item.price);
 	 		}
		});		
	});
	
	
	
	$('input[name="payment_method"]').click(function(e){
		$('#step4-pay').show();
	});
	
	$('#step4-pay').click(function(e){
		var payment_method = $('input[name="payment_method"]:radio:checked').val();
		
		var bz_bill_name = $('input[name="bz-bill-name"]').val();
		var bz_bill_id = $('input[name="bz-bill-id"]').val();
		var bz_bill_addr = $('input[name="bz-bill-addr"]').val();
		
		if(!bz_bill_name || !bz_bill_id){
			$('#createbiz-step4-error-msg').html('<?=lang('createbiz.error.billdata.required')?>');
			$('#createbiz-step4-error-wrapper').show();
			return false;			
		}
				
		if(payment_method == 'money_pickup' && !bz_bill_addr){
			$('#createbiz-step4-error-msg').html('<?=lang('createbiz.error.billaddr.required')?>');
			$('#createbiz-step4-error-wrapper').show();
			return false;			
		}
		
		$('#step4-wrapper').hide();
		$('#waiting').show();
		
        $.ajax({
            type : "POST",
            url : '<?=base_url($this->lang->lang().'/account/purchase')?>',
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
				bz_email : '',
				bz_lat : $('input[name="bz-lat"]').val(),
				bz_lng : $('input[name="bz-lng"]').val(),
				bz_bill_name : bz_bill_name,
				bz_bill_id : bz_bill_id,
				bz_bill_addr: bz_bill_addr,
				billing_cycle : $('select[name="billing-cycle"]').val(), 
				payment_method: payment_method,
				hms1 : $('input[name="hms1"]').val(),
				products : product_list,
				total: total,
				sub_total : sub_total,
				iva : iva,
				in_billing_cycle : in_billing_cycle,
				bz_seller_id : $('input[name="bz-seller-id"]').val()
            }
        }).done(function(response) {
        	if(response.status == 'ok'){
        		$('#waiting').hide();
        		$('#step5-wrapper').show();
        		if(payment_method == 'bank_transfer_deposit' || payment_method == 'money_order' || payment_method == 'money_pickup'){
        			$('#paid-process').show();
        		}
        	}
			
						
        });
		
	});
	
	$('#step3-post').click(function(e){
		e.preventDefault();

		$('#step3-wrapper').hide();
		$('#waiting').show();
		
        $.ajax({
            type : "POST",
            url : '<?=base_url($this->lang->lang().'/account/purchase')?>',
            dataType : "json",
            data : {
            	user_id: <?=$user->id?>,
				bz_type_id : $('select[name="bz-type"]').val(),
				bz_name : $('input[name="bz-name"]').val(),
				bz_desc : $('input[name="bz-desc"]').val(),
				bz_addr : $('input[name="bz-addr"]').val(),
				bz_phones : $('input[name="bz-phones"]').val(),
				bz_ceo : $('input[name="bz-ceo"]').val(),
				bz_email : $('input[name="bz-email"]').val(),
				bz_lat : $('input[name="bz-lat"]').val(),
				bz_lng : $('input[name="bz-lng"]').val(),
				hms1 : $('input[name="hms1"]').val(),
				is_free: 1
            }
        }).done(function(response) {
        	if(response.status == 'ok'){
        		$('#waiting').hide();
        		$('#step5-wrapper').show();
        		$('#free-process').show();
				$('#email-news').hide();
        	}
        });
		
	});
	
	
});


function getItemRow(item){
	return '<tr id="' + item.id + '"><td>' + item.name + '</td><td class="hide-for-small">' + item.description + '</td><td>$' + item.price + 
 	 			'</td><td><a href="javascript:delProduct(' + item.id + ')" id="' + item.id + '" del-product="1" class="tiny button alert clear-margin">-</a></td></tr>';
}

function delProduct(id){
	var tmp = new Array();
	
	for(x=0; x < product_list.length; x++){
		if(id != product_list[x])
			tmp.push(product_list[x]);
		else{
			var id = product_list[x];
		}
	}
	
	var item = getProduct(id);
	setTotal(sub_total - item.price);
	product_list = tmp;
	$('#' + id).remove();
	
}

function getProduct(id){
	for(x=0; x < products.length; x++){
		if(id == products[x].id)
			return products[x];
	}
	
}

function isAddedProduct(id){
	for(x=0; x < product_list.length; x++){
		if(id == product_list[x])
			return true;
	}
	
	return false;
}

function setTotal(t){
	sub_total = t ? t : 0;
	sTotal = new Number(sub_total);
	sub_total = parseFloat(sTotal.toFixed(2));
	
	var ivaObj = new Number(sub_total * iva_factor);
	iva = parseFloat(ivaObj.toFixed(2));
	
	total = sub_total + iva;
	
	$('#sub-total').html(sub_total);
	$('#iva').html(iva);
	$('#total, #total-pay').html(total.toFixed(2));
}
</script>