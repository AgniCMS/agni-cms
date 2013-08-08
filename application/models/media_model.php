<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class media_model extends CI_Model 
{
	
	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	/**
	 * add file data only (to db)
	 * @param array $data
	 * @return array
	 */
	public function addDataOnly($data = array()) 
	{
		// set additional data for insert.
		$data['file_add'] = time();
		$data['file_add_gmt'] = local_to_gmt(time());
		
		// insert into db
		$this->db->insert('files', $data);
		
		// done
		$output['result'] = true;
		$output['file_id'] = $this->db->insert_id();
		return $output;
	}// addDataOnly
	
	
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
	public function checkMemAvailbleForResize($filename, $targetX, $targetY, $returnRequiredMem = false, $gdBloat = 1.68) 
	{
		$maxMem = ((int) ini_get('memory_limit') * 1024) * 1024;
		$imageSizeInfo = getimagesize($filename);
		$srcGDBytes = ceil((($imageSizeInfo[0] * $imageSizeInfo[1]) * 3) * $gdBloat);
		$targetGDBytes = ceil((($targetX * $targetY) * 3) * $gdBloat);
		$totalMemRequired = $srcGDBytes + $targetGDBytes + memory_get_usage();
		log_message('debug', 'File: '.$filename.'; MemLimit: '.$maxMem.'; MemRequired: '.$totalMemRequired.';');
		
		if ($returnRequiredMem) {
			return $srcGDBytes + $targetGDBytes;
		}
		
		if ($totalMemRequired > $maxMem) {
			return false;
		}
		
		return true;
	}// checkMemAvailbleForResize
	
	
	/**
	 * delete
	 * @param integer $file_id
	 * @return boolean 
	 */
	public function delete($file_id = '') 
	{
		// remove feature image from posts
		$this->db->set('post_feature_image', null);
		$this->db->where('post_feature_image', $file_id);
		$this->db->update('posts');
		
		// open db for get file path
		$this->db->where('file_id', $file_id);
		$query = $this->db->get('files');
		if ($query->num_rows() <= 0) {
			$query->free_result();
			return false;
		}
		$row = $query->row();
		$query->free_result();
		
		// delete file
		if (file_exists($row->file)) {
			unlink($row->file);
		}
		
		// delete from db
		$this->db->where('file_id', $file_id);
		$this->db->delete('files');
		
		return true;
	}// delete
	
	
	/**
	 * delete media folder
	 * @param string $folder
	 * @return boolean
	 */
	public function deleteMediaFolder($folder = '') 
	{
		$this->load->helper(array('directory', 'file'));
		
		// delete files in db that folder field contain folder value
		$this->db->like('folder', $folder, 'after');
		$query = $this->db->delete('files');
		
		if (file_exists($folder)) {
			delete_files($folder, true);
			
			if (is_dir($folder)) {
				rmdir($folder);
			}
		}
		
		return true;
	}// deleteMediaFolder
	
	
	/**
	 * edit
	 * @param array $data
	 * @return boolean 
	 */
	public function edit($data = array()){
		if (!is_array($data)) {return false;}
		
		$this->db->where('file_id', $data['file_id']);
		$this->db->update('files', $data);
		
		return true;
	}// edit
	
	
	/**
	 * get file data from db.
	 * @param array $data
	 * @return mixed
	 */
	public function getFileDataDb($data = array()) 
	{
		$this->db->join('accounts', 'files.account_id = accounts.account_id', 'left');
		if (!empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('files');
		
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		
		$query->free_result();
		return null;
	}// getFileDataDb
	
	
	/**
	 * get image
	 * get file_id and return img url or <img>
	 * @param integer $file_id
	 * @param img|null $return_element
	 * @return string 
	 */
	public function getImg($file_id = '', $return_element = 'img') 
	{if (!is_numeric($file_id)) {return null;}
		
		// check cached
		if (false === $get_img = $this->cache->get('media-get_img_'.$file_id.'_'.$return_element)) {
			$data['file_id'] = $file_id;
			$row = $this->getFileDataDb($data);
			
			if ($row != null) {
				if ($return_element == 'img') {
					$output = '<img src="'.base_url().$row->file.'" alt="" />';
					$this->cache->save('media-get_img_'.$file_id.'_'.$return_element, $output, 3600);
					return $output;
				} else {
					$output = base_url().$row->file;
					$this->cache->save('media-get_img_'.$file_id.'_'.$return_element, $output, 3600);
					return $output;
				}
			}
			
			return null;
		}
		
		return $get_img;
		
	}// getImg
	
	
	/**
	 * alias of method getImg
	 */
	public function get_img($file_id = '', $return_element = 'img') 
	{
		return $this->getImg($file_id, $return_element);
	}// get_img
	
	
	/**
	 * list media
	 * @param array $data
	 * @return mixed 
	 */
	public function listMedia($data = array()) 
	{
		$this->db->join('accounts', 'accounts.account_id = files.account_id', 'left');
		
		if (isset($data['folder'])) {
			$this->db->where('folder', $data['folder']);
		}
		
		$q = trim($this->input->get('q'));
		// search
		if ($q != null) {
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
			$this->db->like_group($like_data);
			unset($like_data);
		}
		$filter = trim($this->input->get('filter', true));
		$filter_val = trim($this->input->get('filter_val', true));
		if ($filter != null && $filter_val != null) {
			$this->db->where($filter, $filter_val);
		}
		
		// order, sort
		$orders = trim($this->input->get('orders', true));
			if ($orders == null) {$orders = 'file_id';}
		$sort = trim($this->input->get('sort', true));
			if ($sort == null) {$sort = 'desc';}
		$this->db->order_by($orders, $sort);
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get('files');
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset($this_db);
		
		// html encode for links.
		$q = urlencode(htmlspecialchars($q));
		
		// pagination-----------------------------
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->uri->uri_string()).'?' . generate_querystring_except(array('per_page'));
		if (isset($data['per_page']) && is_numeric($data['per_page'])) {
			$config['per_page'] = $data['per_page'];
		} else {
			$config['per_page'] = (isset($data['list_for']) && $data['list_for'] == 'admin' ? 20 : $this->config_model->loadSingle('content_items_perpage'));
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
		$this->pagination->initialize($config);
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		$this->db->limit($config['per_page'], ($this->input->get('per_page') == null ? '0' : $this->input->get('per_page')));
		
		$query = $this->db->get('files');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listMedia
	
	
	/**
	 * move media
	 * @param array $data
	 * @return mixed
	 */
	public function moveMedia($data = array()) 
	{
		$this->load->library('media_filesys');
		
		foreach ($data['ids'] as $id) {
			$query = $this->db->where('file_id', $id)->get('files');
			
			if ($query->num_rows() > 0) {
				$row = $query->row();
				
				$result = $this->media_filesys->moveMediaFile($row->file, str_replace($row->folder, $data['target_folder'].'/', $row->file));
				
				if ($result == true) {
					$data_update['file_id'] = $id;
					$data_update['folder'] = str_replace($row->folder, $data['target_folder'].'/', $row->folder);
					$data_update['file'] = str_replace($row->folder, $data['target_folder'].'/', $row->file);
					$this->edit($data_update);
					unset($data_update);
				}
			}
			
			$query->free_result();
			unset($query, $row, $id);
		}
		
		return true;
	}// moveMedia
	
	
	/**
	 * rename folder in db
	 * @param string $current_path
	 * @param string $current_folder
	 * @param string $new_name
	 * @return boolean
	 */
	public function renameFolderDb($current_path = '', $current_folder = '', $new_name = '') 
	{
		// prevent double slash
		$current_path = rtrim($current_path, '/');
		
		$query = $this->db->where('folder', $current_path.'/')
					->get('files');
		
		foreach ($query->result() as $row) {
			// loop cut current folder and set new one. ---------------------------------------------
			// eg: public/upload/image/ -> public/upload/images/
			$current_path_exp = explode('/', $current_path);
			$new_folder_name_path = '';
			foreach ($current_path_exp as $path) {
				if ($path != $current_folder) {
					$new_folder_name_path .= $path.'/';
				}
			}
			$new_folder_name_path .= $new_name.'/';

			unset($current_path_exp, $path);
			// loop cut current folder and set new one. ---------------------------------------------
			
			$data['file_id'] = $row->file_id;
			$data['folder'] = $new_folder_name_path;
			$data['file'] = str_replace($row->folder, $new_folder_name_path, $row->file);
			
			$this->edit($data);
			
			unset($new_folder_name_path, $data);
			
		}
		
		$query->free_result();
		
		// loop rename files & folders in all sub. ---------------------------------------------------------------------------------------------
		$query = $this->db->like('folder', $current_path.'/', 'after')
					->get('files');
		
		foreach ($query->result() as $row2) {
			// loop cut current folder and set new one. ---------------------------------------------
			// eg: public/upload/image/ -> public/upload/images/
			$current_path_exp = explode('/', $current_path);
			$new_folder_name_path = '';
			foreach ($current_path_exp as $path) {
				if ($path != $current_folder) {
					$new_folder_name_path .= $path.'/';
				}
			}
			$new_folder_name_path .= $new_name.'/';

			unset($current_path_exp, $path);
			// loop cut current folder and set new one. ---------------------------------------------
			
			$data['file_id'] = $row2->file_id;
			$data['folder'] = str_replace($current_path.'/', $new_folder_name_path, $row2->folder);
			$data['file'] = str_replace($current_path.'/', $new_folder_name_path, $row2->file);
			
			$this->edit($data);
			
			unset($new_folder_name_path, $data);
		}
		
		$query->free_result();
		// loop rename files & folders in all sub. ---------------------------------------------------------------------------------------------
		
		return true;
	}// renameFolderDb
	
	
	/**
	 * upload media
	 * @return mixed 
	 */
	public function uploadMedia() 
	{
		
		// get account id from cookie
		$ca_account = $this->account_model->getAccountCookie('admin');
		$account_id = $ca_account['id'];
		unset($ca_account);
		
		$current_path = trim($this->input->post('current_path')).'/';
		
		if ($this->media_filesys->is_over_limit_base($this->media_filesys->base_dir, $current_path)) {
			unset($ca_account, $account_id, $current_path);
			return 'Hack Attempt!';
		}
		
		if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != null) {
			
			if (!file_exists($current_path)) {
				// directory not exists? create one.
				mkdir($current_path, 0777, true);
			}
			
			// config
			$config['upload_path'] = $current_path;
			$config['allowed_types'] = $this->config_model->loadSingle('media_allowed_types');
			
			if (!preg_match("/^[A-Za-z 0-9~_\-.+={}\"'()]+$/", $_FILES['file']['name'])) {
				// this file has not safe file name. encrypt it.
				$config['encrypt_name'] = true;
			}
			
			$this->load->library('upload', $config);
			
			if (! $this->upload->do_upload("file")) {
				return $this->upload->display_errors('<div>', '</div>');
			} else {
				$filedata = $this->upload->data();
			}
			
			$fileext = strtolower($filedata['file_ext']);
			
			if ($fileext == ".jpg" || $fileext == ".jpeg" || $fileext == ".gif" || $fileext == ".png") {
				// resize images?
				// leave this space for future use.
			}
			
			// get file size
			$size = get_file_info($config['upload_path'].$filedata['raw_name'].$filedata['file_ext'], 'size');
			
			// insert into db
			$data['account_id'] = $account_id;
			$data['folder'] = $current_path;
			$data['file'] = $config['upload_path'].$filedata['raw_name'].$filedata['file_ext'];
			$data['file_name'] = $filedata['file_name'];
			$data['file_original_name'] = $filedata['orig_name'];
			$data['file_client_name'] = $filedata['client_name'];
			$data['file_mime_type'] = $filedata['file_type'];
			$data['file_ext'] = $filedata['file_ext'];
			$data['file_size'] = $size['size'];
			$data['media_name'] = $filedata['file_name'];
			$data['media_keywords'] = $filedata['file_name'];
			$this->addDataOnly($data);
			unset($data);
			
			// done.
			return true;
			
		}
		
	}// uploadMedia
	
	
}

// EOF