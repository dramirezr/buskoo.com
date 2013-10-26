<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function get_by_id($id){
		$media = $this->db->get_where('media', array('id' => $id))->result();
		if(!count($media))
			return null;
		return $media[0];		
	}
	
	function select($data){
		$media = $this->db->get_where('media', $data)->result();
		if(!count($media))
			return null;
		return $media;
	}
	
	function insert($user_id, $post_id, $hash, $type = 'pic', $desc = null, $custom_id = NULL){
		$data = array(
			'user_id' => $user_id,
			'post_id' => $post_id,
			'hash' => $hash,
			'type' => $type,
			'description' => $desc,
			'upload_date' => date('Y-m-d H:i:s'),
			'last_modification' => date('Y-m-d H:i:s'),
			'custom_id' => $custom_id
		);
		
		if(!$this->db->insert('media', $data))
			return false;
		
		return $this->db->insert_id(); 
	}
	
	function update($id, $hash, $desc = null){
		$data = array(
			'hash' =>  $hash,
			'last_modification' => date('Y-m-d H:i:s'),
			'description' => $desc
		);
		
		return $this->db->update('media', $data, array('id' => $id));
	}
}