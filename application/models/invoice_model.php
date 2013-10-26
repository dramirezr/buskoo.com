<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class invoice_model extends CI_Model {
		
	function __construct(){
		parent::__construct();
	}
		
	function create($data, $products){
				
		if(!$this->db->insert('invoice', $data)){
			return false;
		}
		
		$invoice_id = $this->db->insert_id();
		
		//Create the invoice items
		$this->add_items($invoice_id, $data['post_id'], $products);
				
		return $invoice_id;
	}
	
	function add_bz_product($biz_id, $product){
		
		$data = array(
			'post_id' => $biz_id,
			'product_id' => $product->id,
			'quantity' => 1,
			'price' => $product->price,
			'purchase' => date('Y-m-d'),
			'billing_cycle' => $product->billing_cycle,
			'last_billing' => date('Y-m-d'),
			'implementation_data' => serialize(array('unit' => $product->unit)),
		);
		
		$this->db->insert('bz_products', $data);
		return $this->db->insert_id();
	}
	
	function add_items($invoice_id, $biz_id, $products){
		
		foreach($products as $product){
			
			//Add the biz_product
			$bz_product_id =  $this->add_bz_product($biz_id, $product);
			
			//Add the invoice item
			$data = array(
				'invoice_id' => $invoice_id,
				'product_id' => $product->id,
				'bz_products_id' => $bz_product_id,
				'list_price' => $product->price,
				'total_price' => $product->price
			);
			
			$this->db->insert('invoice_items', $data);
		}
		
		return true;	
	}
	
	function get_items($invoice_id){
		$sql = "SELECT * FROM invoice_items WHERE invoice_id=$invoice_id";
		$items = $this->db->query($sql)->result();
		if(!count($items))
			return null;
		return $items;		
	}
	
	function get($invoice_id){

		$sql = "SELECT * FROM invoice WHERE id=$invoice_id";
		$invoice = $this->db->query($sql)->result();
		if(!count($invoice))
			return null;
		
		$invoice = $invoice[0];
		$invoice->items = $this->get_items($invoice_id);
			
		return $invoice;		
		
	}
}