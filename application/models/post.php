<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function get_posts_types(){
		$this->db->order_by('id');
		return $this->db->get('post_type')->result();
	}
	
	function get_by_id($post_id){
		$post = $this->db->get_where('post', array('id' => $post_id))->result();
		if(count($post)){
			$post = $post[0];
			$post = $this->get_metas($post_id, $post);
			return $post;
		}
			
		return null;
	}
	
	function get_metas($post_id, $post=null){
		$result = $this->db->get_where('postmeta', array('post_id' => $post_id))->result();
		if(!count($result))
			return null;
			
		if(!$post)
			$post =  new stdClass();
			
		foreach($result as $row){
			$attr = $row->meta_key;
			$post->$attr = $row->meta_value;
		}
		
		return $post;
	}
	
	function add_qualification($post_id, $data){
		
		$send_web_master_msg = false;
		if(!isset($data['content']) || (isset($data['content']) && !$data['content'])){
			if(isset($data['content']))
				unset($data['content']);
			
			$data['approved'] = 'yes';
		}else{
			//Only the reviews with content need to be verified by the webmasters
			$send_web_master_msg = true;
		}
		
		//Verify if the user has been qualified to this post before
		$earlier_qualify = $this->db->get_where('reviews', array('author_id' => $data['author_id'], 'post_id' => $post_id))->result();
		if(count($earlier_qualify)){
			return 'qualify.earlierqualify';
		}
		
		//Inserts the review
		$data['date'] = date('Y-m-d H:i:s');
		if(!$this->db->insert('reviews', $data)){
			return 'qualify.dberror';
		}
		
		$review_id = $this->db->insert_id();
		
		//Add the webmaster message
		if($send_web_master_msg){
			$CI = & get_instance();
			$CI->load->model('tasks_model');
			$content = "Aprobaci&oacute;n de rese&ntilde;a: Texto: {$data['content']}";
			$CI->tasks_model->create('review', $review_id, $content);
			return 'qualify.sucess.outstanding';
		}
		
		//Update the post score
		$this->update_score($post_id);
		return 'qualify.sucessfully';
	}
	
	function update_score($post_id){
		//Load the amount of active and approved reviews
		$sql = "SELECT count(*) as q FROM reviews WHERE post_id=$post_id AND approved = 'yes' AND state='active'";
		$q = $this->db->query($sql)->result();
		$q = (int) $q[0]->q;
		
		//Load the sumatory of scores
		$sql = "SELECT sum(score) as s FROM reviews WHERE post_id=$post_id AND approved = 'yes' AND state='active' GROUP BY post_id";
		$s = $this->db->query($sql)->result();
		$s = ($s[0]->s) ? (int) $s[0]->s : 0;
		
		$avg = $q ? ($s / $q) : 0;
		$avg = number_format($avg, 1);
		
		$this->db->update('post', array('score_avg' => $avg), array('id' => $post_id));
		
		//Syncronize with solr, according to the post type in order to call the correct model
		$post = $this->get_by_id($post_id);
		$factory = ci_config('post_type_vs_model');
		$model = $factory[$post->post_type_id];
		$CI = & get_instance();
		$CI->load->model($model);
		$CI->$model->syncronize($post_id);
	}
	
	function increase_views($post_id){
		$post = $this->get_by_id($post_id);
		$visits = $post->visits + 1;
		$this->db->update('post', array('visits' => $visits), array('id' => $post_id));
	}
	
}