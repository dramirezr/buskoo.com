<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_model extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function auth($email, $passwd){

		if(!$email || !$passwd)
			return false;
		
		$passwd = md5($passwd);
		
		$sql = "SELECT * FROM user WHERE email='$email' AND passwd='$passwd'";
		$user = $this->db->query($sql)->result();
		if(count($user))
			return $user[0];
			
		return false; 
	}
	
	function get_usermeta($user_id, $meta_key){
		$sql = "SELECT * FROM usermeta WHERE user_id=$user_id AND meta_key='$meta_key'";
		$meta = $this->db->query($sql)->result();

		if(count($meta))
			return $meta[0]->meta_value;
		
		return false;
	}
	
	function set_usermeta($user_id, $meta_key, $meta_value){
		
		//Define if insert or update
		$usermeta = $this->db->get_where('usermeta', array('user_id' => $user_id, 'meta_key' => $meta_key))->result();
		
		if(count($usermeta)){
			$this->db->set('meta_value', $meta_value);
			$this->db->where('user_id', $user_id);
			$this->db->where('meta_key', $meta_key);
			return $this->db->update('usermeta');			
		}
		
		$data = array(
   			'user_id' => $user_id ,
   			'meta_key' => $meta_key ,
   			'meta_value' => $meta_value
		);

		return $this->db->insert('usermeta', $data);
	}
	
	function get_locations($user_id){
		$json = $this->get_usermeta($user_id, 'locations');
		
		if(!$json)
			return null;
		
		return json_decode($json);
	}
	
	function add_location($user_id, $name, $lat, $lng, $def){
		
		$locations = $this->get_locations($user_id);

		//Clear default if come in one		
		if($def == '1'){
			for($x=0; $x<count($locations); $x++)
				$locations[$x]->def = '0';
		}
		
		$locations[] = array('name' => $name, 'lat' => $lat, 'lng' => $lng, 'def' => $def);
		
		$this->set_usermeta($user_id, 'locations', json_encode($locations));
		
		return true;
	}
	
	function set_default_location($user_id, $location_name){

		$locations = $this->get_locations($user_id);

		//Clear default if come in one		
		for($x=0; $x<count($locations); $x++){
			if($locations[$x]->name == $location_name)
				$locations[$x]->def = '1';
			else
				$locations[$x]->def = '0';
		}
				
		$this->set_usermeta($user_id, 'locations', json_encode($locations));
		
		return true;		
	}
	
	function delete_location($user_id, $location_name){
		$locations = $this->get_locations($user_id);
				
		//Clear default if come in one		
		for($x=0; $x<count($locations); $x++){
			if($locations[$x]->name != $location_name)
				$new_locations[] = $locations[$x];
		}
		
		$this->set_usermeta($user_id, 'locations', json_encode($new_locations));
		return true;		
	}
	
	
	function signup($name, $email, $passwd, $creation, $hash){
		
		//Md5 has for password
     	$passwd = md5($passwd);
     	
		$data = array(
   			'email' => $email,
   			'passwd' => $passwd,
   			'name' => $name,
   			'creation' => $creation,
   			'hash' => $hash
		);

		return $this->db->insert('user', $data);     	
		
	}
	
	function get_user($email){
		$sql = "SELECT * FROM user WHERE email='$email'";
		$user = $this->db->query($sql)->result();
		if(count($user))
		   return $user[0];			
		return null;		
	}
	
	function get_user_by_id($id){
		$sql = "SELECT * FROM user WHERE id=$id";
		$user = $this->db->query($sql)->result();
		if(count($user))
			return $user[0];
		return null;		
	}
        
        
	function get_user_by_hash($hash){
		$sql = "SELECT * FROM user WHERE hash='$hash'";
		$user = $this->db->query($sql)->result();
		if(count($user))
			return $user[0];
			
		return null;		
	}
	
	function activate_account($account_id){
		return $this->update_account($account_id, array('status' => 'A'));
	}
	
	function inactive_account($account_id){
		return $this->update_account($account_id, array('status' => 'I'));
	}

	function cancel_account($account_id){
		return $this->update_account($account_id, array('status' => 'C'));
	}
	
	function update_account($account_id, $data){
		
		foreach($data as $field => $value){
			$this->db->set($field, $value);	
		}
		
		$this->db->where('id', $account_id);
		return $this->db->update('user');			
	}
	
	function get_businesses($account_id){
		$this->db->order_by('name', 'desc');
		$this->db->where_in('state', array('A', 'P'));
		$biz = $this->db->get_where('post', array('user_id' => $account_id, 'post_type_id' => 1, ))->result();
		if(!count($biz))
			return null;
		return $biz;
	}
	
	function reset_password($account_id){
		$new_pass = uniqid();
		
		$this->update_account($account_id, array('passwd' => md5($new_pass)));
		
		return $new_pass;
	}
      
}