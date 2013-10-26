<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pictures extends CI_Controller {
	
	var $params = array();
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('post');
		$this->load->model('business_model');
		$this->load->model('media');
		$this->load->model('products_model');
		$this->lang->load('products/picture');
	}
	
	function index(){
		$post_id = $this->input->get('post_id');
		$post_product_id = $this->input->get('post_product_id');
		
		//bz_product_id
		$this->params['bz_product_id'] = $post_product_id ? $post_product_id : NULL;
		
		//Load the current post
		$this->params['post'] = $this->post->get_by_id($post_id);
		
		//Load the product specs
		$prodcts = $this->business_model->get_products($post_id);
		$product = null;
		foreach($prodcts  as $p){
			if($post_product_id == $p->id){
				$product = $p;
				break;
			}
		}
		$this->params['product'] = $product;
		
		$i_data = unserialize($product->implementation_data);
		
		$this->params['unit'] = $i_data['unit'];
		
		//Load the pictures
		$this->params['pics'] = array();
		if($pics = $this->media->select(array('post_id' => $post_id, 'type' => 'pic', 'state' => 1, 'custom_id' => $post_product_id))){
			$this->params['pics'] = $pics;		
		}

		$this->params['user'] = $this->session->userdata('user');
		
		$this->load->view('products/pictures/index', $this->params);
	}
	
	function upload(){
		
		$post_id = $this->input->post('post_id', TRUE); 
		$user_id = $this->input->post('user_id', TRUE);
		$media_id = $this->input->post('media_id', TRUE);
		$bz_product_id = $this->input->post('bz_product_id', TRUE);
		$file_element_name = 'picture';

	    $this->load->library('upload');
	    	 
	   	if (!$this->upload->do_upload($file_element_name)){
	        $msg = $this->upload->display_errors('', '');
	        die(json_encode(array('status' => 'error', 'msg' => $msg)));
	    }    

		$data = $this->upload->data();
		
		//Resize the image
		$config['image_library'] = 'gd2';
		$config['maintain_ratio'] = TRUE;
		$config['width']	 = 600;
		$config['height']	= 600;	    
		$config['quality']	= '70%';
		//$config['create_thumb'] = TRUE; 
		$config['source_image'] = $data['full_path'];
		
		$this->load->library('image_lib', $config); 		
		if(!$this->image_lib->resize()){
			die(json_encode(array('status' => 'error', 'msg' => $this->image_lib->display_errors())));
		}
		
		$post = array('app_id' => ci_config('media_server_app_id'), 'file_contents'=>'@'.$data['full_path']);	 

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
	    	$this->media->update($media_id, $response->file, 'Picture');
	    	$id = $media_id;
	    }else{
		    //Save the response in media
		    $id = $this->media->insert($user_id, $post_id, $response->file, 'pic', 'Picture', $bz_product_id);	    	
	    }
	     
	   	@unlink($_FILES[$file_element_name]);
	   	@unlink($data['full_path']);
	   	
	   	$url = ci_config('media_server_show_url').'/'.$response->file;
	   
	   	die( json_encode(array('status' => 'ok', 'msg' => lang('picture.upload.success'), 'media_id' => $id, 'url' => $url)));
	}
}	