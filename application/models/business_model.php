<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business_model extends CI_Model {

	
	private $post_type = 1;
	
	function __construct(){
		parent::__construct();
	}
	
	function get_types($get_all = true){
		
		$exclude_top5 = '';
		if(!$get_all){
			$top5 = $this->get_top_5_biz_types();
			foreach($top5 as $t){
				$tp[] = $t->id;
			}
			$exclude_top5 = " WHERE id NOT IN (".implode(',', $tp).") ";
		}
			
		$sql = "SELECT * FROM biz_type $exclude_top5 ORDER BY name";
			
			
		$types = $this->db->query($sql)->result();

		if(count($types))
			return $types;
		
		return null;
	}
	function get_type_by_id($id){
		$sql = "SELECT * FROM biz_type WHERE id=$id";
		$types = $this->db->query($sql)->result();

		if(count($types))
			return $types[0];
		
		return null;		
	}
	
	function create($data, $is_paid=false){
		
		//Create the post
		$bz = array(
			'post_type_id' => $this->post_type,
			'user_id' => $data['user_id'],
			'name' => $data['name'],
			'content' => $data['description'],
			'creation' => date('Y-m-d'),
			'last_update' => date('Y-m-d'),
			'tags' => $data['bz_type_name'],
			'state' => 'P'
		);
		
		if(!$this->db->insert('post', $bz)){
			return false;
		}
		
		$bz_id = $this->db->insert_id();
		
		//Add metafields
		$metas = array(
			'address' => $data['address'],
			'lat' => $data['lat'],
			'lng' => $data['lng'],
			'phones' => $data['phones'],
			'CEO_name' => $data['CEO_name'],
			'CEO_email' => $data['CEO_email'],
		);
		
		foreach($metas as $index => $value){
			$this->db->insert('postmeta', array('post_id' => $bz_id, 'meta_key' => $index,'meta_value' => $value));
		}
		
		//Create the bz_type relationship
		$bz_type_rel = array(
			'post_id' => $bz_id,
			'biz_type_id' => $data['bz_type_id']
		);
		if(!$this->db->insert('post_biz_types', $bz_type_rel)){
			return false;
		}
		
		//Syncronize in solr
		//$this->syncronize($bz_id);
		
		return $bz_id;
	}
	
	function update($bz_id, $data){
		$bz = array(
			'post_type_id' => $this->post_type,
			'user_id' => $data['user_id'],
			'name' => $data['name'],
			'content' => $data['description'],
			'last_update' => date('Y-m-d')
		);
		
		if(!$this->db->update('post', $bz, "id = $bz_id")){
			return false;
		}
		
		//update metafields
		$metas = array(
			'address' => $data['address'],
			'lat' => $data['lat'],
			'lng' => $data['lng'],
			'phones' => $data['phones'],
			'CEO_name' => $data['CEO_name'],
			'CEO_email' => $data['CEO_email'],
		);

		foreach($metas as $index => $value){
			$meta = $this->db->get_where('postmeta', array('post_id' => $bz_id, 'meta_key' => $index))->result();
			if(count($meta)){
				$meta = $meta[0];
				$this->db->update('postmeta', array('meta_value' => $value), array('id' => $meta->id));
			}else{
				$this->db->insert('postmeta', array('post_id' => $bz_id, 'meta_key' => $index, 'meta_value' => $value));
			}
		}
		
		//Create the bz_type relationship
		$bz_type_rel = array(
			'post_id' => $bz_id,
			'biz_type_id' => $data['bz_type_id']
		);
		
		$this->db->delete('post_biz_types', array('post_id' => $bz_id)); 
		
		if(!$this->db->insert('post_biz_types', $bz_type_rel)){
			return false;
		}
		
		//Syncronize in solr
		$this->syncronize($bz_id);
		
		return true;
	}
	function get_top_5_biz_types(){
		$sql = "SELECT * FROM biz_type ORDER BY hits LIMIT 5";
		$types = $this->db->query($sql)->result();

		if(count($types))
			return $types;
		
		return null;			
	}
	
	/*Sync the biz to solr*/
	function syncronize($id){
		//Get the post
		$post = $this->db->get_where('post', array('id' => $id))->result();
		if(!count($post))
			return false;
		
		$post = $post[0];
		
		//Get the metas
		$metas_obj = $this->db->get_where('postmeta', array('post_id' => $id))->result();
		if(!count($metas_obj))
			return false;
		
		foreach($metas_obj as $m){
			$metas[$m->meta_key] = $m->meta_value;
		}
		
		//Set the text fields
		$phones = isset($metas['phones']) ? ' '.$metas['phones'] : '';
		$this->load->helper('products/phones');
		$extra_phoones = phones_show($id);
		$phones = $phones.' '.$extra_phoones;
		
		$address = isset($metas['address']) ? ' '.$metas['address'] : '';
		//$CEO = isset($metas['CEO_name']) ? ' '.$metas['CEO_name'] : '';
		//habilitar CEO  cuando el cliente lo pida.
		$CEO = '';
		$email = isset($metas['CEO_email']) ? ' '.$metas['CEO_email'] : '';

		//Get the post type name
		$post_type = $this->db->get_where('post_type', array('id' => $post->post_type_id))->result();

		//get biz type
		$sql = "SELECT b.* FROM post_biz_types p, biz_type b WHERE p.post_id=$id AND p.biz_type_id = b.id";
		$biz_type = $this->db->query($sql)->result();
		$biz_type = $biz_type[0]; 
		
		$tags = trim(trim(strtolower($biz_type->name)).' '.trim(strtolower($biz_type->tag)).' '.trim(mb_strtolower($post->tags)));
		
		//Get the bought business tags
		$this->load->helper('products/tags');
		$custom_tags = get_tags($id);
		$tags .= ' '.$custom_tags;
		
		//Solr data
		$data = array(
			'id' => $id,
			'name' => mb_strtolower($post->name),
			'tags' => $tags,
			'content' => $post->content,
			'post_type_es' => $post_type[0]->name_es,
			'post_type_en' => $post_type[0]->name_en,
			'location' => "{$metas['lat']},{$metas['lng']}",
			'phones' => $phones,
			'address' => $address,
			'ceo' => $CEO,
			'email' => $email,
			'score_avg' => $post->score_avg
		);
		
		solr_syncronize($data);
		return true;	
	}
	
	function get_biz_types($id){
		$sql = "SELECT bt.* FROM post_biz_types pbt, biz_type bt WHERE pbt.post_id=$id AND pbt.biz_type_id=bt.id ORDER BY name";
		return $this->db->query($sql)->result();
	}
	
	function get_by_id($id){
		$CI = & get_instance();
		$CI->load->model('post');
		return $CI->post->get_by_id($id);
	}
	
	function get_products($post_id, $active = 1, $inactivated_by_user = NULL){
		
		if($inactivated_by_user !== NULL)
			$inactivated_by_user = " AND inactivated_by_user = $inactivated_by_user";
			
		$sql = "SELECT " .
					"b.*," .
					"p.id as product_id," .
					"p.name," .
					"p.description," .
					"p.helper_file," .
					"p.unit " .
				"FROM bz_products b, product p WHERE b.post_id = $post_id AND b.product_id = p.id AND b.active = $active $inactivated_by_user ORDER BY p.name";
				
		return $this->db->query($sql)->result();
	}
	
	function get_available_products($post_id){
		$billing_cycle = $this->get_billing_cycle($post_id);
		$billing_cycle = $billing_cycle ? "billing_cycle=$billing_cycle AND " : '';
		
		$sql = "SELECT * " .
				"FROM product " .
				"WHERE $billing_cycle " .
				"id NOT IN (SELECT b.product_id FROM bz_products b, product p WHERE b.post_id=$post_id AND b.product_id=p.id AND p.allow_duplicated=0) ORDER BY name";
		return $this->db->query($sql)->result();
	}
	
	function get_billing_cycle($post_id){
		
		$bz_products = $this->db->get_where('bz_products', array('post_id' => $post_id, 'active' => 1))->result();
		
		if(!count($bz_products)){
			$bz_products = $this->db->get_where('bz_products', array('post_id' => $post_id, 'active' => 0, 'inactivated_by_user' => 1))->result();
		}
		
		if(!count($bz_products))
			return NULL;
		
		return (int) $bz_products[0]->billing_cycle;
	
	}
	
	function get_last_billing_date($post_id){
		
		$sql = "SELECT * FROM invoice WHERE post_id=$post_id AND state = 'paid' AND is_billing_cycle = 1 ORDER BY date DESC";
		$result = $this->db->query($sql)->result();

		if(!count($result))
			return null;
		
		return $result[0]->date;
	}
	
	function get_next_billing_date($post_id){
		$billing_cycle = $this->get_billing_cycle($post_id);
		$last_billing_date = $this->get_last_billing_date($post_id);
		if(!$last_billing_date)
			return false;
		
		$date = new DateTime($last_billing_date);
		$date->add(new DateInterval('P'.$billing_cycle.'M'));
		return $date->format('Y-m-d');
	}
	
	function disable_product($bz_products_id){
		$bz = array(
			'active' => 0,
			'inactivated_by_user' => 1
		);
		
		if(!$this->db->update('bz_products', $bz, "id = $bz_products_id")){
			return false;
		}
		
		//Update the Solr
		$bz_product = $this->db->get_where('bz_products', array('id' => $bz_products_id))->result();
		$this->syncronize($bz_product[0]->post_id);
		
		return true;	
	}

	function enable_product($bz_products_id){
		$bz = array(
			'active' => 1,
			'inactivated_by_user' => 0
		);
		
		if(!$this->db->update('bz_products', $bz, "id = $bz_products_id")){
			return false;
		}
		
		//Update the Solr
		$bz_product = $this->db->get_where('bz_products', array('id' => $bz_products_id))->result();
		$this->syncronize($bz_product[0]->post_id);
		return true;	
	}
	
	function update_bz_product($bz_product_id, $data){
		return $this->db->update('bz_products', $data, "id = $bz_product_id");
	}
	
	function update_last_date($post_id){
		return $this->db->update('post', array('last_update' => date('Y-m-d')), "id = $post_id");
	}
	
	function get_invoices($post_id){
		return $this->db->get_where('invoice', array('post_id' => $post_id))->result();
		
	}
}