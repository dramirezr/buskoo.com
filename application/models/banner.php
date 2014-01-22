<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banner extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function get_RamdomBanner(){
		$sql = " SELECT * FROM banner WHERE state='A' ORDER BY RAND() LIMIT 3 ";
		$banner = $this->db->query($sql)->result();

		if(count($banner))
			return $banner;
		
		return null;
	}
	
}