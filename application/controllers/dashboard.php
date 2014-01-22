<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	var $title = 'Dashboard';
	var $dasboard_params = array();
		
	function __construct(){
		parent::__construct();
		
		$this->load->model('account_model');
		$this->load->model('business_model');
		$this->load->model('post');
		$this->load->library('user_agent');
		
		$this->load->library('user_agent');
		
		$this->dasboard_params['user'] = $this->session->userdata('user');
	}
	
	function index(){
		
		//Validate Browsers 
		$current_browser = $this->agent->browser().' '.$this->agent->version();
		
		$nab = ci_config('not_allowed_browsers');
		if(in_array($current_browser, $nab)){
			$this->dasboard_params['browser_error'] = TRUE;
		}
		
		//Get the top five of business types
		//$this->dasboard_params['bztop5types'] = $this->get_top5_bztypes();
		
		//Load the post diferamiiiiiiiiii
		$pid = $this->input->get_post('pid', TRUE);
		if($pid){
			$post = $this->business_model->get_by_id($pid);
			if($post)
				$this->dasboard_params['post'] = json_encode($post);
		}
		
		//Load the related user posts
		$uid = $this->input->get_post('uid', TRUE);
		if($uid){
			$uposts = $this->account_model->get_businesses($uid);
			if($uposts){
				foreach($uposts as $i => $p){
					$uposts[$i] = $this->business_model->get_by_id($p->id);
				}
			}
			
			if(isset($post)){
				$uposts[] = $post;
				$this->dasboard_params['post'] = null;
			}
			
			$this->dasboard_params['uposts'] = json_encode($uposts);
		}
		
		
		//Get the follow us urls
		$this->dasboard_params['follow_us_links'] = $this->config_model->get_follow_us_links();
		
		//Load the configuration for logged in users
		if($user = $this->session->userdata('user')){
			$this->lang->switch_uri($user->lang);		
			$this->dasboard_params['user_locations'] = $this->account_model->get_locations($user->id);
			
			//Define the user default location
			if(is_array($this->dasboard_params['user_locations'])){
				foreach($this->dasboard_params['user_locations'] as $l){
					if($l->def == '1'){
						$this->dasboard_params['userDefaultLocation'][0] = $l->lat;
						$this->dasboard_params['userDefaultLocation'][1] = $l->lng;
						break;
					}
				}
			}
		   
		   	//Load the user businesses
		   	if($biz = $this->account_model->get_businesses($user->id))
		   		$this->dasboard_params['businesses'] = $biz;
			
		} 

		//Get the system dafault location
		$sdl = get_config_val('default_latlang');
		$this->dasboard_params['system_location'] = explode(',', $sdl);

		//Get The map zoom
		$this->dasboard_params['map_zoom'] = get_config_val('map_zoom');
				
		//get the nav locatinos
		$str_nav_locations = get_config_val('nav_locations');
		if($str_nav_locations){
			$str = explode(';', $str_nav_locations);
			foreach($str as $row){
				$tmp = explode(',', $row);
				$this->dasboard_params['nav_locations'][] = array('name' => $tmp[0], 'lat' => $tmp[1], 'lng' => $tmp[2]);			
			}

		}
		
		//Get the post_types
		$this->dasboard_params['post_types'] = $this->post->get_posts_types();
		
		$this->render();
	}
	

	private function render(){
		
		$this->template->write('title', $this->title);
		
		//$this->template->add_js('https://www.google.com/jsapi', 'import', FALSE, FALSE);

		$this->template->write_view('search_form', 'templates/search_form', $this->dasboard_params, TRUE);

		$this->template->write_view('menu', 'templates/menu', $this->dasboard_params, TRUE);

		$this->template->write_view('map', 'templates/map', $this->dasboard_params, TRUE);
		
		$this->template->render();
	}
	
	/*Load the top five of the business types*/
	private function get_top5_bztypes(){
		return $this->business_model->get_top_5_biz_types();
	}
	

}