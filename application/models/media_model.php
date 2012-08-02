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
		$this->db->set( 'media_name', $data['media_name'] );
		$this->db->set( 'media_description', $data['media_description'] );
		$this->db->set( 'media_keywords', $data['media_keywords'] );
		$this->db->where( 'file_id', $data['file_id'] );
		$this->db->update( 'files' );
		return true;
	}// edit
	
	
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
			$this->db->where( 'file_id', $file_id );
			$query = $this->db->get( 'files' );
			if ( $query->num_rows() > 0 ) {
				$row = $query->row();
				$query->free_result();
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
			$query->free_result();
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
		// query
		$sql = 'select * from '.$this->db->dbprefix( 'files' ).' as f';
		$sql .= ' left join '.$this->db->dbprefix( 'accounts' ).' as a';
		$sql .= ' on f.account_id = a.account_id';
		$sql .= ' where language = '.$this->db->escape( $this->lang->get_current_lang() );
		$q = htmlspecialchars( trim( $this->input->get( 'q' ) ) );
		// search
		if ( $q != null ) {
			$sql .= ' and (';
			$sql .= " file like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or file_name like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or file_original_name like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or file_client_name like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or file_mime_type like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or file_ext like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or file_size like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or media_name like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or media_keywords like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= ')';
		}
		$filter = trim( $this->input->get( 'filter', true ) );
		$filter_val = trim( $this->input->get( 'filter_val', true ) );
		if ( $filter != null && $filter_val != null ) {
			$sql .= ' and (';
			$sql .= ' '.$filter.' = '.$this->db->escape( $filter_val );
			$sql .= ')';
		}
		// order, sort
		$orders = trim( $this->input->get( 'orders', true ) );
			if ( $orders == null ) {$orders = 'file_id';}
		$sort = trim( $this->input->get( 'sort', true ) );
			if ( $sort == null ) {$sort = 'desc';}
		$sql .= ' order by '.$orders.' '.$sort;
		// query for count total
		$query = $this->db->query( $sql );
		$total = $query->num_rows();
		$query->free_result();
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
		$sql .= ' limit '.( $this->input->get( 'per_page' ) == null ? '0' : $this->input->get( 'per_page' ) ).', '.$config['per_page'].';';
		$query = $this->db->query( $sql);
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
			$this->db->set( 'account_id', $account_id );
			$this->db->set( 'language', $this->lang->get_current_lang() );
			$this->db->set( 'file', $config['upload_path'].$filedata['raw_name'].$filedata['file_ext'] );
			$this->db->set( 'file_name', $filedata['file_name'] );
			$this->db->set( 'file_original_name', $filedata['orig_name'] );
			$this->db->set( 'file_client_name', $filedata['client_name'] );
			$this->db->set( 'file_mime_type', $filedata['file_type'] );
			$this->db->set( 'file_ext', $filedata['file_ext'] );
			$this->db->set( 'file_size', $size['size'] );
			$this->db->set( 'media_name', $filedata['file_name'] );
			$this->db->set( 'media_keywords', $filedata['file_name'] );
			$this->db->set( 'file_add', time() );
			$this->db->set( 'file_add_gmt', local_to_gmt( time() ) );
			$this->db->insert( 'files' );
			return true;
		}
		
	}// upload_media
	
	
}

// EOF