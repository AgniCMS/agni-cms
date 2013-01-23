<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class media_model extends CI_Model {
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	/**
	 * add file data only (to db)
	 * @param array $data
	 * @return array
	 */
	function add_data_only( $data = array() ) {
		// set additional data for insert.
		$data['file_add'] = time();
		$data['file_add_gmt'] = local_to_gmt( time() );
		
		// insert into db
		$this->db->insert( 'files', $data );
		
		// done
		$output['result'] = true;
		$output['file_id'] = $this->db->insert_id();
		return $output;
	}// add_data_only
	
	
	/**
	 * checkMemAvailbleForResize
	 * @author Klinky
	 * @link http://stackoverflow.com/a/4163548/128761, http://stackoverflow.com/questions/4162789/php-handle-memory-code-low-memory-usage
	 * @param string $filename
	 * @param integer $targetX
	 * @param integer $targetY
	 * @param boolean $returnRequiredMem
	 * @param float $gdBloat
	 * @return mixed 
	 */
	function checkMemAvailbleForResize($filename, $targetX, $targetY, $returnRequiredMem = false, $gdBloat = 1.68) {
		$maxMem = ((int) ini_get('memory_limit') * 1024) * 1024;
		$imageSizeInfo = getimagesize($filename);
		$srcGDBytes = ceil((($imageSizeInfo[0] * $imageSizeInfo[1]) * 3) * $gdBloat);
		$targetGDBytes = ceil((($targetX * $targetY) * 3) * $gdBloat);
		$totalMemRequired = $srcGDBytes + $targetGDBytes + memory_get_usage();
		log_message( 'debug', 'File: '.$filename.'; MemLimit: '.$maxMem.'; MemRequired: '.$totalMemRequired.';' );
		if ($returnRequiredMem)
			return $srcGDBytes + $targetGDBytes;
		if ($totalMemRequired > $maxMem)
			return false;
		return true;
	}// checkMemAvailbleForResize
	
	
	/**
	 * delete
	 * @param integer $file_id
	 * @return boolean 
	 */
	function delete( $file_id = '' ) {
		// remove feature image from posts
		$this->db->set( 'post_feature_image', null );
		$this->db->where( 'post_feature_image', $file_id );
		$this->db->update( 'posts' );
		
		// open db for get file path
		$this->db->where( 'file_id', $file_id );
		$query = $this->db->get( 'files' );
		if ( $query->num_rows() <= 0 ) {
			$query->free_result();
			return false;
		}
		$row = $query->row();
		$query->free_result();
		
		// delete file
		if ( file_exists( $row->file ) ) {
			unlink( $row->file );
		}
		
		// delete from db
		$this->db->where( 'file_id', $file_id );
		$this->db->delete( 'files' );
		
		return true;
	}// delete
	
	
	/**
	 * edit
	 * @param array $data
	 * @return boolean 
	 */
	function edit( $data = array() ){
		if ( !is_array( $data ) ) {return false;}
		
		$this->db->where( 'file_id', $data['file_id'] );
		$this->db->update( 'files', $data );
		
		return true;
	}// edit
	
	
	/**
	 * get file data from db.
	 * @param array $data
	 * @return mixed
	 */
	function get_file_data_db( $data = array() ) {
		$this->db->join( 'accounts', 'files.account_id = accounts.account_id', 'left' );
		if ( !empty( $data ) ) {
			$this->db->where( $data );
		}
		$this->db->where( 'language', $this->lang->get_current_lang() );
		
		$query = $this->db->get( 'files' );
		
		if ( $query->num_rows() > 0 ) {
			return $query->row();
		}
		
		$query->free_result();
		return null;
	}// get_file_data_db
	
	
	/**
	 * get_img
	 * get file_id and return img url or <img>
	 * @param integer $file_id
	 * @param img|null $return_element
	 * @return string 
	 */
	function get_img( $file_id = '', $return_element = 'img' ) {
		if ( !is_numeric( $file_id ) ) {return null;}
		
		// check cached
		if ( false === $get_img = $this->cache->get( 'media-get_img_'.$file_id.'_'.$return_element ) ) {
			$data['file_id'] = $file_id;
			$row = $this->get_file_data_db( $data );
			
			if ( $row != null ) {
				if ( $return_element == 'img' ) {
					$output = '<img src="'.base_url().$row->file.'" alt="" />';
					$this->cache->save( 'media-get_img_'.$file_id.'_'.$return_element, $output, 3600 );
					return $output;
				} else {
					$output = base_url().$row->file;
					$this->cache->save( 'media-get_img_'.$file_id.'_'.$return_element, $output, 3600 );
					return $output;
				}
			}
			
			return null;
		}
		
		return $get_img;
	}// get_img
	
	
	/**
	 * list_item
	 * @param admin|front $list_for
	 * @return mixed 
	 */
	function list_item( $list_for = 'front' ) {
		$this->db->join( 'accounts', 'accounts.account_id = files.account_id', 'left' );
		$this->db->where( 'language', $this->lang->get_current_lang() );
		$q = trim( $this->input->get( 'q' ) );
		// search
		if ( $q != null ) {
			$like_data[0]['field'] = 'files.file';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'files.file_name';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'files.file_original_name';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'files.file_client_name';
			$like_data[3]['match'] = $q;
			$like_data[4]['field'] = 'files.file_mime_type';
			$like_data[4]['match'] = $q;
			$like_data[5]['field'] = 'files.file_size';
			$like_data[5]['match'] = $q;
			$like_data[6]['field'] = 'files.media_name';
			$like_data[6]['match'] = $q;
			$like_data[7]['field'] = 'files.media_keywords';
			$like_data[7]['match'] = $q;
			$this->db->like_group( $like_data );
			unset( $like_data );
		}
		$filter = trim( $this->input->get( 'filter', true ) );
		$filter_val = trim( $this->input->get( 'filter_val', true ) );
		if ( $filter != null && $filter_val != null ) {
			$this->db->where( $filter, $filter_val );
		}
		
		// order, sort
		$orders = trim( $this->input->get( 'orders', true ) );
			if ( $orders == null ) {$orders = 'file_id';}
		$sort = trim( $this->input->get( 'sort', true ) );
			if ( $sort == null ) {$sort = 'desc';}
		$this->db->order_by( $orders, $sort );
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get( 'files' );
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset( $this_db );
		
		// html encode for links.
		$q = urlencode( htmlspecialchars( $q ) );
		
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		if ( $list_for == 'admin' ) {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?orders='.htmlspecialchars( $orders ).'&amp;sort='.htmlspecialchars( $sort ).'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.( $q != null ?'&amp;q='.$q : '' );
			$config['per_page'] = 20;
		} else {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?'.( $q != null ?'q='.$q : '' );
			$config['per_page'] = $this->config_model->load_single( 'content_items_perpage' );
		}
		$config['total_rows'] = $total;
		// pagination tags customize for bootstrap css framework
		$config['num_links'] = 3;
		$config['page_query_string'] = true;
		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = "</ul></div>\n";
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		// end customize for bootstrap
		$config['first_link'] = '|&lt;';
		$config['last_link'] = '&gt;|';
		$this->pagination->initialize( $config );
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		$this->db->limit( $config['per_page'], ( $this->input->get( 'per_page' ) == null ? '0' : $this->input->get( 'per_page' ) ) );
		
		$query = $this->db->get( 'files' );
		
		if ( $query->num_rows() > 0 ) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// list_item
	
	
	/**
	 * upload_media
	 * @return mixed 
	 */
	function upload_media() {
		
		// get account id from cookie
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$account_id = $ca_account['id'];
		unset( $ca_account );
		
		if ( isset( $_FILES['file']['name'] ) && $_FILES['file']['name'] != null ) {
			
			if ( !file_exists( $this->config->item( 'agni_upload_path' ).'media/'.$this->lang->get_current_lang().'/' ) ) {
				// directory not exists? create one.
				mkdir( $this->config->item( 'agni_upload_path' ).'media/'.$this->lang->get_current_lang().'/', 0777, true );
			}
			
			// config
			$config['upload_path'] = $this->config->item( 'agni_upload_path' ).'media/'.$this->lang->get_current_lang().'/';
			$config['allowed_types'] = $this->config_model->load_single( 'media_allowed_types' );
			
			if ( !preg_match( "/^[A-Za-z 0-9~_\-.+={}\"'()]+$/", $_FILES['file']['name'] ) ) {
				// this file has not safe file name. encrypt it.
				$config['encrypt_name'] = true;
			}
			
			$this->load->library('upload', $config);
			
			if ( ! $this->upload->do_upload("file") ) {
				return $this->upload->display_errors( '<div>', '</div>' );
			} else {
				$filedata = $this->upload->data();
			}
			
			$fileext = strtolower( $filedata['file_ext'] );
			
			if ($fileext == ".jpg" || $fileext == ".jpeg" || $fileext == ".gif" || $fileext == ".png") {
				// resize images?
				// leave this space for future use.
			}
			
			// get file size
			$size = get_file_info( $config['upload_path'].$filedata['raw_name'].$filedata['file_ext'], 'size' );
			
			// insert into db
			$data['account_id'] = $account_id;
			$data['language'] = $this->lang->get_current_lang();
			$data['file'] = $config['upload_path'].$filedata['raw_name'].$filedata['file_ext'];
			$data['file_name'] = $filedata['file_name'];
			$data['file_original_name'] = $filedata['orig_name'];
			$data['file_client_name'] = $filedata['client_name'];
			$data['file_mime_type'] = $filedata['file_type'];
			$data['file_ext'] = $filedata['file_ext'];
			$data['file_size'] = $size['size'];
			$data['media_name'] = $filedata['file_name'];
			$data['media_keywords'] = $filedata['file_name'];
			$this->add_data_only( $data );
			unset( $data );
			
			// done.
			return true;
			
		}
		
	}// upload_media
	
	
}

// EOF