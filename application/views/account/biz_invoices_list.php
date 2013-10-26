<? header('Content-Type: text/html'); ?>

<?= form_open('api', array('id' => 'bz-invoices-list-form', 'class' => '')) ?>
</form>

<div class="row">
	<div class="large-12 columns">
		<div class="panel callout radius">
			<h5><?=lang('bizpanel.bills.billinginfo')?></h5>
			<p><?=lang('bizpanel.bills.cycle')?>: <?= ($billing_cycle == 12) ? lang('bizpanel.bills.yearly') : lang('bizpanel.bills.monthly') ?></p>
			<p><?=lang('bizpanel.bills.nextbill')?>: <?= $next_billing_date ?></p>
			<p><?=lang('bizpanel.bills.lastbill')?>: <?= $last_billing_date ?></p>
			</div>

	</div>
</div>

<div class="row">
	<div class="large-12 columns">&nbsp;</div>
</div>

<div class="row">
	<div class="large-12 columns">
		<h5><?=lang('bizpanel.bills.listtitle')?></h5>
		<small><?=lang('bizpanel.bills.listsexpl')?></small>
	</div>
</div>

<div class="row">
	<div class="large-12 columns">&nbsp;</div>
</div>

<div class="row">
	<div class="large-12 columns">
	<table>
	  <thead>
	    <tr>
	      <th><?=lang('bizpanel.bills.id')?></th>
	      <th><?=lang('bizpanel.bills.date')?></th>
	      <th><?=lang('bizpanel.bills.balance')?></th>
	      <th><?=lang('bizpanel.bills.total')?></th>
	      <th><?=lang('bizpanel.bills.state')?></th>
	      <th>&nbsp;</th>
	    </tr>
	  </thead>
	  <? foreach($invoices as $index => $i): ?>
	  	<tr>
	  		<td><?= $i->id ?></td>
	  		<td><?= $i->date ?></td>
	  		<td>$<?= $i->balance ?></td>
	  		<td>$<?= $i->total ?></td>
	  		<td><?= lang('bizpanel.bills.state.'.$i->state) ?></td>
	  		<td><a href="javascript:void()" class="send-invioce-email" id="<?= $i->id ?>"><?=lang('bizpanel.bills.sendemail')?></a></td>
	  	</tr>
	  <? endforeach; ?>
	  <tbody>

	  </tbody>
	</table>
	
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.send-invioce-email').click(function (e){
			e.preventDefault();
			
			var invoice = $(this).attr('id');

			$.ajax({
				type : "POST",
				url : '<?=base_url($this->lang->lang().'/account/send_invoice')?>',
		            dataType : "json",
		            data : {
		            	hms1: $('input[name="hms1"]').val(),
		            	invoice_id : invoice
		            }
		        }).done(function(response) {
		        	alert(response.msg);
		        });
			
			
		})
	})
</script>