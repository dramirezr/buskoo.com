<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products_model extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function get_products(){
		$sql = "SELECT * FROM product WHERE active=1 and internal=0";
		$products = $this->db->query($sql)->result();

		if(count($products))
			return $products;
		
		return null;
	}
	
	function get_by_ids($ids){
		if(is_array($ids))
			$q = "IN (". implode(',', $ids) .")";
		else
			$q = "= $ids";
			
		$sql = "SELECT * FROM product WHERE id  $q";
		$products = $this->db->query($sql)->result();

		if($num = count($products)){
			if($num == 1)
				return $products[0];
			return $products;
		}

		return null;
		
	}
}