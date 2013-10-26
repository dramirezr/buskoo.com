<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tasks_model extends CI_Model {
		
	function __construct(){
		parent::__construct();
	}
		
	function create($ref_name, $ref_id, $content){
		
		$data = array(
			'ref_name' => $ref_name,
			'ref_id' => $ref_id,
			'open_date' => date('Y-m-d H:i:s'),
			'content' => $content,
			'state' => 'open',
		);
		if(!$this->db->insert('tasks', $data)){
			return false;
		}
		
		return $this->db->insert_id();
	}
}