<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logo extends CI_Controller {
	
	var $params = array();
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('post');
		$this->load->model('media');
		$this->lang->load('products/logo');
	}
	
	function deactivate(){
		//TODO
	}
	
	function index(){
		$post_id = $this->input->get('post_id');
		$post_product_id = $this->input->get('post_product_id');
		
		//Load the current post
		$this->params['post'] = $this->post->get_by_id($post_id);
		
		//Load the current active logo
		if($logo = $this->media->select(array('post_id' => $post_id, 'type' => 'logo', 'state' => 1))){
			$logo = $logo[0];
			$this->params['logo_url'] = ci_config('media_server_show_url').'/'.$logo->hash;
			$this->params['logo_id'] = $logo->id;			
		}
			
		
		$this->params['user'] = $this->session->userdata('user');
		
		$this->load->view('products/logo/index', $this->params);
	}
	
	function upload(){

		$post_id = $this->input->post('post_id', TRUE); 
		$user_id = $this->input->post('user_id', TRUE);
		$media_id = $this->input->post('media_id', TRUE);
	   	$file_element_name = 'logo';
	 
	    $this->load->library('upload');
	 
	   	if (!$this->upload->do_upload($file_element_name)){
	        $msg = $this->upload->display_errors('', '');
	        die(json_encode(array('status' => 'error', 'msg' => $msg)));
	    }    
	   
	    $data = $this->upload->data();
	    	    
		$post = array('app_id' => ci_config('media_server_app_id'),'file_contents'=>'@'.$data['full_path']);	 

	    //Need an update
	    if($media_id){
	    	$media = $this->media->get_by_id($media_id);
	    	$post['hash'] = $media->hash;
	    }
	 
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, ci_config('media_server_upload_url'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		$response = json_decode($result);
		
		if($response->status == 'error'){
		   	@unlink($_FILES[$file_element_name]);
		   	@unlink($data['full_path']);
			die(json_encode(array('status' => 'error', 'msg' => $response->msg)));		
		}
	    
	    if($media_id){
	    	$this->media->update($media_id, $response->file, 'Logo');
	    	$id = $media_id;
	    }else{
		    //Save the response in media
		    $id = $this->media->insert($user_id, $post_id, $response->file, 'logo', 'Logo');	    	
	    }
	     
	   	@unlink($_FILES[$file_element_name]);
	   	@unlink($data['full_path']);
	   	
	   	$url = ci_config('media_server_show_url').'/'.$response->file;
	   
	   	die( json_encode(array('status' => 'success', 'msg' => lang('logo.upload.success'), 'media_id' => $id, 'url' => $url)));
	}
}