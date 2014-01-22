<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->lang->load('api');
		$this->load->model('business_model');
	}
	
	function search(){
		
		//Get the search params
		$text			= $this->input->post('text', TRUE);
		$pid 			= $this->input->post('pid', TRUE);
		$email 			= $this->input->post('email', TRUE);
		$distance		= $this->input->post('distance', TRUE);
	
		if(is_quoted($text))
			$distance = 15;
			
		$start 			= $this->input->post('start', TRUE);
		$start 			= $start ? $start : 0;
		$rows 			= $this->input->post('rows', TRUE);
		$post_type 		= $this->input->post('post_type', TRUE);
		$post_type 		= ($post_type != 'all') ? $post_type : NULL;
		$lat 			= $this->input->post('lat', TRUE);
		$lng 			= $this->input->post('lng', TRUE);
		$sort_field 	= $this->input->post('sort', TRUE);
		$exact_match	= $this->input->post('exact_match', TRUE);

		$options 		= ci_config('solr_options');
		$max_results 	= ci_config('max_solr_results');
		
		extract($options);
		
		//Setup the sort
		switch($sort_field){
			case 'score':
				$sort = 'score desc,score_avg desc,geodist() asc';
				break;
			case 'score_avg':
				$sort = 'score_avg desc, score desc,geodist() asc';
				break;
			case 'geodist':
				$sort = 'geodist() asc, score desc,score_avg desc';
				break;
			default:
				$sort = 'score desc,score_avg desc,geodist() asc';
		}
		
		$q = search_query($text, $post_type, $exact_match, $pid);
		$query = array(
			'q' 		=> $q,
			'fq' 		=> '{!geofilt}',
			'sort' 		=> $sort, 
			'start' 	=> $start, 
			'rows' 		=> $rows,
			'fl' 		=> '*,score,_dist_:geodist()', 
			'df' 		=> 'tags',
			'wt' 		=> 'json', 
			'indent' 	=> 'true', 
			'spatial' 	=> 'true', 
			'pt' 		=> $lat.','.$lng, 
			'sfield' 	=> 'location', 
			'd' 		=> $distance, 
		);
		
		$url 		= $protocol.'://'.$hostname.':'.$port.'/'.$path.'/select?'.http_build_query($query);
		$json 		= $this->curl->simple_get($url);

		$results 	= json_decode($json);
		//echo '<pre>',print_r($json),'</pre>';
		$docs 		= $results->response->docs;
		
		if($results->response->numFound > $max_results)
			$results->response->numFound = $max_results;
		
		$this->load->helper('pagination');
		
		if($pid){
			$text = $docs[0]->name;
		}
		
		$params = array(
			'text' 		=> $text,
			'distance' 	=> $distance,
		    'results' 	=> $results,
		    'docs' 		=> $docs,
 		    'start' 	=> $start,
		    'rows' 		=> $rows,
		    'numFound' 	=> $results->response->numFound,
		    'sort' 		=> $sort_field
		);
		
		$this->load->helper('products/logo');
		$this->load->helper('products/extrainfo');
		$this->load->helper('products/phones');
		$this->load->helper('products/promo');
		$this->load->helper('products/fbpage');
		$this->load->helper('products/website');
		$this->load->helper('products/email');
		
		$this->load->view('api/search', $params);		
	}
	
	function ajax_get_all_types(){
		die(json_encode($this->get_all_types()));
	}
	
	/* get all primary types (without parent) exucluding the top five*/
	private function get_all_types(){
		return $this->business_model->get_types(false);
	}
	
	function add_location(){
		
		$lat = $this->input->post('lat', TRUE); 
		$lng = $this->input->post('lng', TRUE);
		$name = $this->input->post('name', TRUE);
		$def = $this->input->post('def', TRUE);
				
		if(!$lat || !$lng || !$name || $def === null){ 
			$result = array('status' => 'error', 'msg' => 'Missing input params lat, lng, name or def');
			die(json_encode($result));
		}
		
		$this->load->model('account_model');
		$user = $this->session->userdata('user');

		if($this->account_model->add_location($user->id, $name, $lat, $lng, $def)){
			die(json_encode(array('status' => 'ok')));
		}

		$result = array('status' => 'error', 'msg' => 'Something went wrong');
		die(json_encode($result));
	}
	
	function set_default_location(){
		$name = $this->input->post('name', TRUE);		

		if(!$name){ 
			$result = array('status' => 'error', 'msg' => 'Missing input params name');
			die(json_encode($result));
		}
		
		$this->load->model('account_model');
		$user = $this->session->userdata('user');

		if($this->account_model->set_default_location($user->id, $name)){
			die(json_encode(array('status' => 'ok')));
		}

		$result = array('status' => 'error', 'msg' => 'Something went wrong');
		die(json_encode($result));
	}
	
	function delete_location(){
		$name = $this->input->post('name', TRUE);

		if(!$name){ 
			$result = array('status' => 'error', 'msg' => 'Missing input params name');
			die(json_encode($result));
		}

		$this->load->model('account_model');
		$user = $this->session->userdata('user');
		
		if($this->account_model->delete_location($user->id, $name)){
			die(json_encode(array('status' => 'ok')));
		}
		
		$result = array('status' => 'error', 'msg' => 'Something went wrong');
		die(json_encode($result));
		
	}
	
	function qualification(){
		$this->load->model('post');
		
		$post_id = $this->input->post('post_id', TRUE);
		
		$data = array(
			'author_id' => $this->input->post('user_id', TRUE),
			'post_id' => $this->input->post('post_id', TRUE),
			'author_ip' => $this->input->post('user_ip', TRUE),
			'author_agent' => $this->input->post('user_agent', TRUE),
			'score' => $this->input->post('q', TRUE),
			'content' => $this->input->post('review', TRUE),
		);
		
		$resutl = $this->post->add_qualification($post_id, $data);
		if(in_array($resutl, array('earlierqualify', 'dberror'))){
			$result = array('status' => 'error', 'msg' => lang($resutl));
			die(json_encode($result));			
		}
		
		die(json_encode(array('status' => 'ok', 'msg' => lang($resutl))));
	}
	
	function test_syncronize($id){
		$this->business_model->syncronize($id);
	}
	
	function open_business_panel(){
		$this->load->model('business_model');
		$this->lang->load('biz_panel');
		$this->lang->load('account');
		
		$post_id = $this->input->get('post_id');
		
		//Load the biz basic
		$biz = $this->business_model->get_by_id($post_id);
		
		$params = array(
			'biz' => $biz,
			'user' => $this->session->userdata('user')
		);
		
		$this->business_model->update_last_date($post_id);
		
		$this->load->view('api/biz_panel', $params);	
	}
	
	function disable_product(){
		$this->load->model('business_model');
		
		$bz_product_id = $this->input->post('bz_product_id', TRUE);
		
		if($this->business_model->disable_product($bz_product_id)){
			die(json_encode(array('status' => 'ok')));
		}
		
		die(json_encode(array('status' => 'error')));
	}
	
	function enable_product(){
		$this->load->model('business_model');
		
		$bz_product_id = $this->input->post('bz_product_id', TRUE);
		
		if($this->business_model->enable_product($bz_product_id)){
			die(json_encode(array('status' => 'ok')));
		}
		
		die(json_encode(array('status' => 'error')));
		
	}
	
	function show_products(){
		
		$post_id = $this->input->get('post_id');
		
		//Get the bought products 
		$products = $this->business_model->get_products($post_id);
		
		if(!count($products)){
			die();
		}
		
		$executed = array('logo', 'extrainfo', 'phones', 'ytvideo', 'tags', 'promo', 'fbpage', 'website', 'email'); //Excluded the logo and others
		$params = array();
		foreach($products as $p){
			if(!in_array($p->helper_file, $executed)){
				$executed[] = $p->helper_file;
				$this->load->helper('products/' . $p->helper_file);
				$func_name = $p->helper_file."_show";
				$view = $func_name($post_id);
				if($view)
					$params['views'][] = $view;
			}
		}
		
		$this->load->view('api/show_products', $params);
	}
	
	function contact(){
		
		$this->load->model('tasks_model');
		
		$email = $this->input->post('email', TRUE);
		$subject = $this->input->post('subject', TRUE);
		$msg = $this->input->post('msg', TRUE);
		$bzid = $this->input->post('bzid', TRUE);
		
		$content = "Asunto: $subject, local ID: $bzid, $msg";
		$this->tasks_model->create('pqr', $email, $content);
		
		die(json_encode(array('status' => 'ok')));
	} 
	
	function update_views(){
		$this->load->model('post');
		
		$pid = $this->input->get_post('post_id');
		
		$this->post->increase_views($pid);
		die();
	}
	
	function show_info(){
		$post_id = $this->input->get('post_id');

		$this->load->helper('products/logo');
		$this->load->helper('products/extrainfo');
		$this->load->helper('products/phones');
		$this->load->helper('products/promo');
		$this->load->helper('products/fbpage');
		$this->load->helper('products/website');
		$this->load->helper('products/email');
		

		$extrainfo 	= show_extrainfo($post_id);
		$email 		= email_show($post_id);
		$website 	= website_show($post_id);
		$fbpage 	= fbpage_show($post_id);
		$logo		= get_logo_url($post_id);
		//echo '-->'.$logo.'<--';
		//$logo = '';
		$this->load->model('post');
		$result_icon = $this->post->get_icon_post($post_id);
		//die(json_encode(array('state' => 'ok', 'extrainfo' => $extrainfo, 'email' => $email, 'website' => $website, 'fbpage' => $fbpage, 'icono_post' => $result_icon ) ));

		die(json_encode(array('state' => 'ok', 'extrainfo' => $extrainfo, 'email' => $email, 'website' => $website, 'fbpage' => $fbpage, 'icono_post' => $result_icon, 'logo' => $logo) ));
	}

	
	function show_banner(){
		
		$this->load->model('banner');
		$result = $this->banner->get_RamdomBanner();
		$html = '<table>';
		$html .= '<tr>';
		$state = 'false';
		foreach($result as $r){
			$html .= '<td>';
			$html .= 	'<nav><a href="'.$r->url.'"  TARGET="_blank" >'.$r->description.'</a> </nav>'.
            		' <aside>' .
               		'		<div id="logotipo"><img src="'.ci_config('banner_server_show_url').$r->image.'"  width="80" height="80"></div>' .
            		'	</aside>' .
            		'	<section id="centro">' .
                	'		'.$r->content.' ' .
                	'	</section>';
            $html .= '</td>';
            $state = 'ok';
		}
		$html .= '</tr>';
		$html .= '</table>';
		die(json_encode(array('state' => $state, 'html' => $html) ));
	}

}
