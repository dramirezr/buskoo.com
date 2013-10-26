<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Email extends CI_Email {

	var $from_email = '';
	var $from_name = '';
	var $to = '';
	var $message = '';
	var $subject = '';
	
    public function __construct()
    {
        parent::__construct();
        $this->from_name = get_config_val('business_name');
        $this->from_email = get_config_val('default_email_from');
    }
    
    function send_activation($name, $email, $passwd, $hash){
    	
    	$lang = current_lang();
    	$bz_name = get_config_val('business_name');
    	$slogan = get_config_val("business_slogan_$lang");
    	$this->subject = get_config_val("activation_email_subject_$lang").' '.$bz_name;
    	$msg = get_config_val("activation_email_$lang");
    	$this->to = $email;

    	$act_days = get_config_val('account_activation_days');
    	
    	$tags = array(
			'[USER_NAME]',
			'[EMAIL]',
			'[PASSWD]',
			'[SERVER]',
			'[HASH]',
			'[ACTIVATION_DAYS]',
			'[BIZ_NAME]',
			'[SLOGAN]',
			'[LANG]'
		);
		
		$data = array(
			$name,
			$email,
			$passwd,
			$_SERVER['HTTP_HOST'],
			$hash,
			$act_days,
			$bz_name,
			$slogan,
			$lang
		);
    	
    	$this->message = str_replace($tags, $data, $msg);
    	return $this->send_email();
    }
    
    function send_invoice($invoice_id){
    	$lang = current_lang();
    	$bz_name = get_config_val('business_name');
    	$slogan = get_config_val("business_slogan_$lang");
    	$this->subject = get_config_val("invoice_email_subject_$lang").' '.$bz_name;
    	$msg = get_config_val("invoice_email_$lang");
    	
    	$payment_confirmation_process = get_config_val("payment_confirmation_process_$lang");
    	
    	$msg = str_replace('[PAYMENT_CONFIRMATION_PROCESS]', $payment_confirmation_process, $msg);
    	
    	$CI =& get_instance();
    	$CI->load->model('invoice_model');
    	$CI->load->model('account_model');
    	$CI->load->model('products_model');

    	//Load the invoice
    	$invoice = $CI->invoice_model->get($invoice_id);
    	
    	//Load the user
    	$user = $CI->account_model->get_user_by_id($invoice->user_id); 	
    	
    	$this->to = $user->email;
    	
    	foreach($invoice->items as $i){
    		$product = $CI->products_model->get_by_ids($i->product_id);
    		$html_products[] = '<tr>' .
    				'<td>'.$product->name.'</td>' .
    				'<td>$'.$i->total_price.'</td>' .
    				'</tr>';
    	}
    	
    	$tags = array(
    		'[USER_NAME]',
    		'[RUC_NAME]',
    		'[RUC_ID]',
    		'[RUC_ADDR]',
    		'[INVOICE_ID]',
    		'[INVOICE_DATE]',
    		'[USER_IDENTIFICATION]',
    		'[PRODUCTS_LIST]',
    		'[PAYMENT_CONFIRMATION_EMAIL]',
    		'[BANK_NAME]',
    		'[BANK_ACC_NUMBER]',
    		'[BANK_ACC_OWNER]',
    		'[MONEY_ORDER_RECIPIENT]',
    		'[MONEY_ORDER_IDENTIFICATION]',
    		'[BIZ_NAME]',
    		'[SERVER]',
    		'[SLOGAN]',
    		'[SUB_TOTAL]',
    		'[DISCOUNTS]',
    		'[PRORATE]',
    		'[IVA]',
    		'[IVA_VALUE]',
    		'[TOTAL]'
    	);
    	
    	$data = array(
    		$invoice->billing_name,
    		get_config_val('ruc_company_name'),
    		get_config_val('ruc_company_id'),
    		get_config_val('ruc_company_address'),
    		$invoice->id,
    		$invoice->date,
    		$invoice->billing_identification,
    		implode("\n", $html_products),
    		get_config_val('payment_confirmation_email'),
    		get_config_val('bank_name'),
    		get_config_val('bank_account_number'),
    		get_config_val('bank_account_name'),
    		get_config_val('money_order_name'),
    		get_config_val('money_order_identification'),
    		$bz_name,
    		$_SERVER['HTTP_HOST'],
    		$slogan,
    		$invoice->subtotal,
    		(isset($invoice->discount) && $invoice->discount) ? $invoice->discount : '0.00',
    		$invoice->proportional_rate,
    		get_config_val('iva'),
    		$invoice->iva,
    		$invoice->total
    	);
    	
    	$this->message = str_replace($tags, $data, $msg);
    	
 		return $this->send_email();
    	
    }
    
    function send_pwdrecovery($email, $new_pwd, $name){

    	$lang = current_lang();
    	$bz_name = get_config_val('business_name');
    	$slogan = get_config_val("business_slogan_$lang");
    	$this->subject = get_config_val("pwdrecovery_subject_$lang").' '.$bz_name;
    	$msg = get_config_val("pwdrecovery_email_$lang");
    	$this->to = $email;
    	
    	$tags = array(
    		'[USER_NAME]',
    		'[PWD]',
    		'[BIZ_NAME]',
    		'[SERVER]',
    		'[SLOGAN]'
    	);
		
		$data = array(
			$name,
			$new_pwd,
			$bz_name,
			$_SERVER['HTTP_HOST'],
			$slogan
		);
		
		$this->message = str_replace($tags, $data, $msg);
		
		return $this->send_email();
    }
    
    function send_email(){

		$this->from($this->from_email, $this->from_name);
		$this->to($this->to); 
		
		$this->subject($this->subject);
		$this->message($this->message);	
		
		$r = $this->send();    
		//$this->print_debugger();
		return $r;	
    }
}