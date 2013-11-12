<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	var $reposnse = array();
	var $user = NULL;
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('account_model');
		$this->lang->load('account');
		//$this->load->library('recaptcha');
		$this->load->library('email');
		
		$this->user = $this->session->userdata('user');
	}
	
	function signup(){
		
		$name = $this->input->post('name', TRUE); 
		$email = $this->input->post('email', TRUE); 
		$passwd = $this->input->post('passwd', TRUE);
		
		//Avoid duplicated emails
		if($this->account_model->get_user($email)){
			die(json_encode(array('status' => 'error', 'msg' => lang('signup.error.duplicatedmail'))));
		}
				
		//Generate the activation hash
		$hash = get_hash();
		$date = date('Y-m-d');
     	if($this->account_model->signup($name, $email, $passwd, $date, $hash)){
     		$this->email->send_activation($name, $email, $passwd, $hash);
     		//$this->login($email, $passwd);
     		die(json_encode(array('status' => 'ok', 'msg' => '')));
     	}else{
     		die(json_encode(array('status' => 'error', 'msg' => 'Error Creating User')));
     	}
	}
	
	
	function login($email = null, $passwd = null){

		$email = $email ? $email: $this->input->post('email', TRUE); 
		$passwd = $passwd ? $passwd : $this->input->post('passwd', TRUE);
		
		if(!$user = $this->account_model->auth($email, $passwd)){
			$this->response['status'] = 'error';
			$this->response['msg'] = lang('login.error.noauth');
			die(json_encode($this->response));
		}
		
		if(in_array($user->status, array('I', 'C'))){
			$this->response['status'] = 'error';
			$this->response['msg'] = lang('login.error.inactiveac');
			die(json_encode($this->response));			
		}

		if($user->status == 'O'){
			$this->response['status'] = 'error';
			$this->response['msg'] = lang('login.error.outstanding');
			die(json_encode($this->response));			
		}
		
		//Create the session
		$user->passwd = NULL;
		$this->session->set_userdata('user', $user);
		
		$session_data = $this->session->all_userdata();
		
		$this->response['status'] = 'ok';
		$this->response['session_id'] = $session_data['session_id'];
		die(json_encode($this->response));
	}
	
        function change_password(){

            //Aca se supone que descargamos las tres claves desde el POST
            $oldpass = $this->input->post('oldpass', TRUE);
            $newpasswd = $this->input->post('newpasswd', TRUE);
            $email = $this->input->post('email', TRUE);
                    
            
           //Validamos en email desde $user->passwd ojo que esta en md5
            $user = $this->account_model->get_user($email);      
           if(!$user){            
               die(json_encode(array('status' => 'error', 'msg' => lang('chpwd.account_noexist'))));
           }
           
           if(md5($oldpass) != $user->passwd){            
               die(json_encode(array('status' => 'error', 'msg' => lang('chpwd.passwordwrong'))));
           }
                       
            //Modificar la clave
           if(!$this->account_model->update_account($user->id, array('passwd' => md5($newpasswd)))){
                die(json_encode(array('status' => 'error', 'msg' => lang('chpwd.errorchanging'))));
            }
            
            die(json_encode(array('status' => 'ok', 'msg' => lang('chpwd.successfully'))));
        }
        
	function logout(){
		$this->session->sess_destroy();
		redirect('/'); 
	}
	
	function verify($hash){
		
		$user = $this->account_model->get_user_by_hash($hash);

		if(!$user){
			$this->session->set_flashdata('flash_msg', array('status' => 'error', 'msg' => lang('activation.wornglink')));
			redirect('/');			
		}
		
		if(in_array($user->status, array('I', 'C'))){
			$this->session->set_flashdata('flash_msg', array('status' => 'error', 'msg' => lang('activation.error')));
			redirect('/');		
		}
		
		if($user->status == 'A'){
			$this->session->set_flashdata('flash_msg', array('status' => 'error', 'msg' => lang('activation.alreadyactivated')));
			redirect('/');		
		}
		
		if($this->account_model->activate_account($user->id)){
			$this->session->set_flashdata('flash_msg', array('status' => 'ok', 'msg' => lang('activation.successfully')));
			redirect('/');			
		}
		
	}
	
	function create_enterprise_form(){

		$this->load->model('products_model');
		$this->load->model('business_model');

		$params['biz'] = NULL;
		$params['post_id'] = NULL;
		if($post_id = $this->input->get('bz_id')){
			$params['post_id'] = $post_id;
			$params['biz'] = $this->business_model->get_by_id($post_id);			

			//Get the biz active products
			$products = $this->business_model->get_products($post_id);
			//Get the biz inactivated products by the user
			$inactivated_by_user_products = $this->business_model->get_products($post_id, 0, 1);
			$products = array_merge($products, $inactivated_by_user_products);
			
			if(count($products)){
				//get the billig cycle
				$billing_cycle = $this->business_model->get_billing_cycle($post_id);
				
				//Get the last billing date
				$last_billing_date = $this->business_model->get_last_billing_date($post_id);
				
				//Get the available product list
				$available_products = $this->business_model->get_available_products($post_id);
				
				//Apply the prorate
				foreach($available_products as $i => $ap){
					$available_products[$i]->price = prorate_value($available_products[$i]->price, $last_billing_date, $billing_cycle);
				}
				
				$next_billing_date = $this->business_model->get_next_billing_date($post_id);
				$params['next_billing_date'] = $next_billing_date;
				
				if($billing_cycle)
					$params['billing_cycle'] = $billing_cycle;
				
				$params['products'] = 	$available_products;
				
				$params['not_in_billing_cycle'] = TRUE;
						
			}else{
				$params['products'] = $this->products_model->get_products();
			}
		}else{
			$params['products'] = $this->products_model->get_products();
		}
		
		
		//TODO: Validate an open sesion
		if(!$this->user)
			die('Session Exprired');
		
		$params['user'] = $this->user;
		
		//Load the billing cyclesuser
		$billing = get_config_val('billing_cycles');
		$params['billing_cycles'] = ($billing) ? explode(',', $billing) : NULL;
		
		//Load the products
		//$params['products'] = $this->products_model->get_products();
		
		//Load the bz types
		$params['bz_types'] = $this->business_model->get_types();
		
 		$this->load->view('account/create_enterprise', $params);
	}
	
	function purchase(){
		$this->load->model('business_model');
		$this->load->model('invoice_model');
		$this->load->model('products_model');
		$this->load->model('tasks_model');
		
		$bz_id = $this->input->post('bz_id', TRUE); 
		$user_id = $this->input->post('user_id', TRUE);
		$invoice_activate_biz = 0;
//		$is_billing_cycle = 0;		
		$items = $this->input->post('products', TRUE);
		$payment_method = $this->input->post('payment_method', TRUE);
		$billing_name = $this->input->post('bz_bill_name', TRUE);
		$is_billing_cycle = $this->input->post('in_billing_cycle', TRUE);
		$seller_id = $this->input->post('bz_seller_id', TRUE);
		
		
		//Create the business
		if(!$bz_id){
			$bz_type_id = $this->input->post('bz_type_id', TRUE);
			$type =  $this->business_model->get_type_by_id($bz_type_id);
			$is_free = $this->input->post('is_free', TRUE);
			$bz_name = $this->input->post('bz_name', TRUE);
			$bz_phones = $this->input->post('bz_phones', TRUE);
			$bz_address = $this->input->post('bz_addr', TRUE);
			$bz_lat = $this->input->post('bz_lat', TRUE);
			$bz_lng = $this->input->post('bz_lng', TRUE);
			
			$bz_data = array(
				'user_id' => $user_id,
				'bz_type_id' => $bz_type_id,
				'bz_type_name' => $type->name,
				'name' => $bz_name,
				'description' => $this->input->post('bz_desc', TRUE),
				'address' => $bz_address,
				'phones' => $bz_phones,
				'CEO_name' => $this->input->post('bz_ceo', TRUE),
				'CEO_email' => $this->input->post('bz_email', TRUE),
				'lat' => $bz_lat,
				'lng' => $bz_lng,
			); 
			$bz_id = $this->business_model->create($bz_data);
			$invoice_activate_biz = 1;
			//$is_billing_cycle = 1;
			if($is_free){
				$user = $this->account_model->get_user_by_id($user_id); 
				$content = "Verificación de datos y publicación de negocio. ID: $bz_id, Usuario: {$user->name}, email: {$user->email}, Biz Name: $bz_name, Tels: {$bz_phones}, Dir: {$bz_address}, latlng: {$bz_lat},{$bz_lng}";
				$this->tasks_model->create('business', $bz_id, $content);
				die(json_encode(array('status' => 'ok', 'biz_id' => $bz_id)));
			}else{
				$this->business_model->syncronize($bz_id);
			}
			
		}
		
		//Create the invoice
		//TODO: Proportional rate and discounts calculations here
		$total = $this->input->post('total', TRUE);
		$invoice_data = array(
			'user_id' => $user_id,
			'post_id' => $bz_id,
			'payment_method' => $payment_method,
			'date' => date('Y-m-d'),
			'balance' => $this->input->post('total', TRUE),
			'subtotal' => $this->input->post('sub_total', TRUE),
			'iva' => $this->input->post('iva', TRUE),
			'total' => $total,
			'state' => 'outstanding',
			'activate_biz' => $invoice_activate_biz,
			'billing_name' => $billing_name,
			'billing_identification' =>  $this->input->post('bz_bill_id', TRUE),
			'billing_address' => $this->input->post('bz_bill_addr', TRUE),
			'is_billing_cycle' => $is_billing_cycle,
			'seller_id' => $seller_id
		);
		
		//Add the items
		$products = $this->products_model->get_by_ids($items);	
		if(is_object($products))
			$products = array($products);
				
		$invoice_id = $this->invoice_model->create($invoice_data, $products);
		
		//Send the invoice
		$this->email->send_invoice($invoice_id);
		
		//Create the task
		if($payment_method == 'money_pickup'){			
			$invoice = $this->invoice_model->get($invoice_id);			
			$user = $this->account_model->get_user_by_id($user_id); 
			$content = "Recoger pago. Valor: \$$total, Cliente: $billing_name, email: {$user->email}, Dir: {$invoice->billing_address}, Tels: {$user->phone}, Cel: {$user->cellphone}";
			$this->tasks_model->create('invoice', $invoice_id, $content);
		}
		
		//Update the last date
		$this->business_model->update_last_date($bz_id);
		
		die(json_encode(array('status' => 'ok', 'invoice_id' => $invoice_id, 'biz_id' => $bz_id)));
	}
	
	function update_business(){
		$this->load->model('business_model');
		$this->lang->load('biz_panel');
		
		$user_id = $this->input->post('user_id', TRUE);
		$bz_id = $this->input->post('bz_id', TRUE);
		$bz_type_id = $this->input->post('bz_type_id', TRUE);
		$type =  $this->business_model->get_type_by_id($bz_type_id);
		$is_free = $this->input->post('is_free', TRUE);
		$bz_name = $this->input->post('bz_name', TRUE);
		$bz_phones = $this->input->post('bz_phones', TRUE);
		$bz_address = $this->input->post('bz_addr', TRUE);
		$bz_lat = $this->input->post('bz_lat', TRUE);
		$bz_lng = $this->input->post('bz_lng', TRUE);
		$CEO_email = $this->input->post('bz_email', TRUE);

		$this->load->helper('products/email');
		$extra_email =  getEmail($bz_id);
		echo $CEO_email = $CEO_email . ' ' .$extra_email;
		

		$bz_data = array(
			'user_id' => $user_id,
			'bz_type_id' => $bz_type_id,
			'bz_type_name' => $type->name,
			'name' => $bz_name,
			'description' => $this->input->post('bz_desc', TRUE),
			'address' => $bz_address,
			'phones' => $bz_phones,
			'CEO_name' => $this->input->post('bz_ceo', TRUE),
			'CEO_email' => $CEO_email,
			'lat' => $bz_lat,
			'lng' => $bz_lng,
		);
		
		$result = $this->business_model->update($bz_id, $bz_data);
		die(json_encode(array('status' => 'ok', 'msg' => lang('bizpanel.successfull'))));
	}
	
	function get_biz_products(){
		$this->load->model('business_model');
		$this->lang->load('biz_panel');

		$post_id = $this->input->post('bz_id');
		
		//Get the biz products
		$products = $this->business_model->get_products($post_id);
		$not_active_products = $this->business_model->get_products($post_id, 0);
		
		$params = array(
			'post_id' => $post_id,
			'products' => $products,
			'not_active_products' => $not_active_products
		);
		
		$this->load->view('account/biz_products', $params);
	}

	function get_biz_products_list(){
		$this->load->model('business_model');
		$this->lang->load('biz_panel');

		$post_id = $this->input->post('bz_id');
		
		//Get the biz active products
		$products = $this->business_model->get_products($post_id);
		//Get the biz inactivated products by the user
		$inactivated_by_user_products = $this->business_model->get_products($post_id, 0, 1);
		$products = array_merge($products, $inactivated_by_user_products);
		//Get all non active products
		$not_active_products = $this->business_model->get_products($post_id, 0, 0);
		
		//get the billig cycle
		$billing_cycle = $this->business_model->get_billing_cycle($post_id);
		
		//Get the last billing date
		$last_billing_date = $this->business_model->get_last_billing_date($post_id);
		
		//Get the available product list
		$available_products = $this->business_model->get_available_products($post_id);
		
		//Apply the prorate
		foreach($available_products as $i => $ap){
			$available_products[$i]->price = prorate_value($available_products[$i]->price, $last_billing_date, $billing_cycle);
		}
		
		//Get the next billing date
		$next_billing_date = $this->business_model->get_next_billing_date($post_id);
		
		$params = array(
			'post_id' => $post_id,
			'products' => $products,
			'available_products' => $available_products,
			'not_active_products' => $not_active_products,
			'next_billing_date' => $next_billing_date,
			'billing_cycle' => $billing_cycle,
			'last_billing_date' => $last_billing_date,
			'user' => $this->user
		);
		$this->load->view('account/biz_products_list', $params);
	}
	
	function recovery_passwd(){
		$email = $this->input->post('email', TRUE);
		
		$user = $this->account_model->get_user($email);
		
		if(!$user)
			die(json_encode(array('state' => 'error', 'msg' => lang('pwdrecover.error.nouser'))));
		
		//Reset the passwrod
		$new_pwd = $this->account_model->reset_password($user->id);
		
		//Send the email
		$r = $this->email->send_pwdrecovery($email, $new_pwd, $user->name);
		
		if(!$r)
			die(json_encode(array('state' => 'error', 'msg' => lang('pwdrecover.error.send'))));
		
		die(json_encode(array('state' => 'ok', 'msg' => lang('pwdrecover.success'))));
	}
	
	function get_invoices(){
		$this->load->model('business_model');
		$this->lang->load('biz_panel');

		$post_id = $this->input->post('bz_id');

		//get the billig cycle
		$billing_cycle = $this->business_model->get_billing_cycle($post_id);
		
		//Get the last billing date
		$last_billing_date = $this->business_model->get_last_billing_date($post_id);

		//Get the next billing date
		$next_billing_date = $this->business_model->get_next_billing_date($post_id);
		
		//Get active bills
		$invoices =$this->business_model->get_invoices($post_id);
		
		$params = array(
			'post_id' => $post_id,
			'invoices' => $invoices,
			'next_billing_date' => $next_billing_date,
			'billing_cycle' => $billing_cycle,
			'last_billing_date' => $last_billing_date,
			'user' => $this->user
		);
		
		$this->load->view('account/biz_invoices_list', $params);		
		
	}
	
	function send_invoice(){
		
		$invoice_id = $this->input->post('invoice_id');
		
		$this->email->send_invoice($invoice_id);
		
		$this->lang->load('biz_panel');
		
		die(json_encode(array('state' => 'ok', 'msg' => lang('bizpanel.bills.emailsent'))));
	}
}