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
 
class comments_model extends CI_Model 
{
	
	
	public $divs = 1;// for use with comment threaded
	
	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	/**
	 * add
	 * @param array $data
	 * @return mixed 
	 */
	public function add($data = array()) 
	{
		// load posts model
		$this->load->model('posts_model');
		
		// get post data and check post exists.
		$data_post['post_id'] = $data['post_id'];
		$row = $this->posts_model->getPostData($data_post);
		unset($data_post);
		
		// post not exists
		if ($row == null) {
			return $this->lang->line('comment_post_not_exists');
		}
		
		// additional data for insert to db
		$data['ip_address'] = $this->input->ip_address();
		$data['user_agent'] = $this->input->user_agent();
		$data['comment_add'] = time();
		$data['comment_add_gmt'] = local_to_gmt(time());
		$data['comment_update'] = time();
		$data['comment_update_gmt'] = local_to_gmt(time());
		
		$this->db->insert('comments', $data);
		
		// get insert id
		$data['comment_id'] = $this->db->insert_id();
		
		// update post table -> total comments.
		$this->load->model('posts_model');
		$this->posts_model->updateTotalComment($data['post_id']);
		
		// email notify admin new comment
		$cfg_val = $this->config_model->load(array('comment_new_notify_admin', 'comment_admin_notify_emails', 'mail_sender_email'));
		$user_email = '';
		
		if ($data['account_id'] != '0') {
			$user_email = $this->account_model->show_accounts_info($data['account_id'], 'account_id', 'account_email');
		}
		
		if (($cfg_val['comment_new_notify_admin']['value'] == '2' && mb_stripos($cfg_val['comment_admin_notify_emails']['value'], $user_email) === false) 
		|| ($cfg_val['comment_new_notify_admin']['value'] == '1' && $data['comment_status'] == '0') 
		&& ($data['comment_spam_status'] == 'normal')) {
			// load email library
			$this->load->library(array('email', 'email_template'));
			$email_content = $this->email_template->read_template('new_comment.html');
			$email_content = str_replace("%comment_onpage%", anchor('post/'.$row->post_uri_encoded, $row->post_name), $email_content);
			$email_content = str_replace("%comment_name%", $data['name'], $email_content);
			$email_content = str_replace("%comment%", $data['comment_body_value'], $email_content);
			$email_content = str_replace("%ip_address%", $this->input->ip_address(), $email_content);
			$email_content = str_replace("%user_agent%", $this->input->user_agent(), $email_content);
			$email_content = str_replace("%comment_status%", ($data['comment_status'] == '1' ? lang('comment_approved') : lang('comment_notyet_approve')), $email_content);
			$email_content = str_replace("%site_url%", site_url(), $email_content);
			
			$this->email->from($cfg_val['mail_sender_email']['value']);
			$this->email->to($cfg_val['comment_admin_notify_emails']['value']);
			$this->email->subject($this->lang->line('comment_new_comment_notify'));
			$this->email->message($email_content);
			$this->email->set_alt_message(str_replace("\t", '', strip_tags($email_content)));
			
			if ($this->email->send() == false) {
				log_message('error', 'Could not send email to user.');
			}
			
			unset($email_content, $user_email);
		}
		
		unset($query, $row, $cfg_val, $user_email);
		
		// comment's module plug
		$this->modules_plug->do_action('comment_after_newcomment', $data);
		
		// done
		$output['id'] = $data['comment_id'];
		$output['result'] = true;
		return $output;
	}// add
	
	
	/**
	 * comment_view
	 * get array from db and loop generate nested comment.
	 * 
	 * thanks to drupal comment system, for idea of thread and sorting.
	 * @link http://www.drupal.org
	 * 
	 * @logic by PJGUNNER www.pjgunner.com
	 * 
	 * @param array $comments
	 * @param string $mode
	 * @return string 
	 */
	/*function comment_view($comments = '', $mode = 'thread') {
		if (!isset($comments['items'])) {return '<p class="list-comment-no-comment no-comment">'.$this->lang->line('comment_no_comment').'</p>';}
		$stack = 1;
		$output = '';
		//$output .= '<article>'.$row->comment_body_value.' - id:'.$row->comment_id.' - parent:'.$row->parent_id.' - thread:'.$row->thread.'</article>'."\n";// prototype
		if (is_array($comments['items'])) {
			if ($mode == 'thread') {
				foreach ($comments['items'] as $row) {
					$stack = count(explode('.', $row->thread));
					if (($stack > $this->divs)) {
						for ($i = $this->divs; $i < $stack; $i++) {
							$output .= '<div class="indent">'."\n";
							$this->divs = ($this->divs+1);
						}
					} elseif ($stack < $this->divs) {
						$back_stack = (($this->divs)-$stack);
						for ($i = 0; $i < $back_stack; $i++) {
							$output .= '</div>'."\n";
							$this->divs = ($this->divs-1);
						}
					}
					$output .= '<a id="comment-id-'.$row->comment_id.'"></a>';
					$output .= '<article>'.$row->comment_body_value.' - id:'.$row->comment_id.' - parent:'.$row->parent_id.' - thread:'.$row->thread.' - stack:'.$stack.' - divs:'.$this->divs
							.' '.anchor(current_url().'?replyto='.$row->comment_id.'#addcomment', 'reply')
							.'</article>'."\n";
				}
				for ($i = $this->divs; $i > 1; $i--) {
					$output .= '</div>'."\n";
					$this->divs = ($this->divs-1);
				}
			} else {
				foreach ($comments['items'] as $row) {
					$output .= '<a id="comment-id-'.$row->comment_id.'"></a>';
					$output .= '<article>'.$row->comment_body_value.' - id:'.$row->comment_id.' - parent:'.$row->parent_id.' - thread:'.$row->thread.' - stack:'.$stack.' - divs:'.$this->divs
							.' '.anchor(current_url().'?replyto='.$row->comment_id.'#addcomment', 'reply')
							.'</article>'."\n";
				}
			}
		}
		return $output;
	}// comment_view*/ // use in controller for 'load->view'
	
	
	/**
	 * delete
	 * @param integer $comment_id
	 * @return boolean 
	 */
	public function delete($comment_id = '') 
	{
		if (!is_numeric($comment_id)) {return false;}
		
		// delete all comment children from comments table
		$this->db->where('parent_id', $comment_id);
		$query = $this->db->get('comments');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$this->delete($row->comment_id);
			}
		}
		$query->free_result();
		
		// delete now
		$this->db->where('comment_id', $comment_id);
		$this->db->delete('comments');
		
		// modules plug here
		$this->modules_plug->do_action('comment_after_delete', $comment_id);
		
		// done
		return true;
	}// delete
	
	
	/**
	 * edit
	 * @param array $data
	 * @return boolean 
	 */
	public function edit($data = array()) 
	{
		// additional data for comments table
		$data['comment_update'] = time();
		$data['comment_update_gmt'] = local_to_gmt(time());
		
		$this->db->where('comment_id', $data['comment_id']);
		$this->db->update('comments', $data);
		
		// comment's module plug
		$this->modules_plug->do_action('comment_after_updatecomment', $data);
		
		return true;
	}// edit
	
	
	/**
	 *  get comments data from db.
	 * @param array $data
	 * @return mixed
	 */
	public function getCommentDataDb($data = array()) 
	{
		$this->db->join('posts', 'comments.post_id = posts.post_id', 'left');
		$this->db->join('accounts', 'comments.account_id = accounts.account_id', 'left outer');
		
		if (!empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('comments');
		
		return $query->row();
	}// getCommentDataDb
	
	
	/**
	 * get comment display page
	 * @param integer $comment_id
	 * @param string $mode
	 * @return integer 
	 */
	public function getCommentDisplayPage($comment_id = '', $mode = 'thread') 
	{
		// account id from cookie
		$cm_account = $this->account_model->getAccountCookie('admin');
		$account_id = $cm_account['id'];
		if ($account_id == null) {$account_id = '0';}
		
		// query db to get current page that this comment will display in.
		// this step cannot use active record because it has JOIN ... ON ... AND comes together.
		$sql = 'SELECT *, count(*) AS count FROM '.$this->db->dbprefix('comments').' as c1';
		$sql .= ' INNER JOIN '.$this->db->dbprefix('comments').' AS c2 ON c1.post_id = c2.post_id';
		$sql .= ' AND c2.comment_id = '.$comment_id;
		if ($this->account_model->checkAdminPermission('comment_perm', 'comment_viewall_perm', $account_id)) {
			$sql .= ' and c1.comment_status = 1';
		}
		if ($mode == 'thread') {
			$sql .= ' WHERE SUBSTRING(c1.thread, 1, (LENGTH(c1.thread) -1)) < SUBSTRING(c2.thread, 1, (LENGTH(c2.thread) -1))';
		} else {
			$sql .= ' AND c1.comment_id < '.$comment_id;
		}
		$sql .= ' GROUP BY c1.comment_id';
		
		$query = $this->db->query($sql);
		
		$row = $query->row();
		$query->free_result();
		
		//
		$num_per_page = $this->config_model->loadSingle('comment_perpage');
		return (floor(($row->count+1)/$num_per_page)*$num_per_page);
	}// getCommentDisplayPage
	
	
	/**
	 * get comment fields from db
	 * @param integer $comment_id
	 * @param array $data
	 * @return mixed
	 */
	public function getCommentFields($comment_id = '', $data = array()) 
	{
		if (!is_numeric($comment_id)) {
			return null;
		}
		
		$this->db->from('comment_fields')
				->where('comment_id', $comment_id);
		
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		
		$query->free_result();
		
		return null;
	}// getCommentFields
	
	
	/**
	 * Generate vancode.
	 *
	 * Consists of a leading character indicating length, followed by N digits
	 * with a numerical value in base 36. Vancodes can be sorted as strings
	 * without messing up numerical order.
	 *
	 * It goes:
	 * 00, 01, 02, ..., 0y, 0z,
	 * 110, 111, ... , 1zy, 1zz,
	 * 2100, 2101, ..., 2zzy, 2zzz,
	 * 31000, 31001, ...
	 * 
	 * by drupal
	 * 
	 * @param integer $i
	 */
	public function int2VanCode($i = 0) 
	{
		$num = base_convert((int) $i, 10, 36);
		$length = strlen($num);

		return chr($length + ord('0') - 1) . $num;
	}// int2VanCode
	
	
	/**
	 * list comments
	 * @param integer $post_id
	 * @param string $mode
	 * @param admin|front $list_for
	 * @return mixed 
	 */
	public function listComment($post_id = '',$mode = 'thread', $list_for = 'front') 
	{
		// comment view permission
		$comment_view_permission = $this->account_model->checkAdminPermission('comment_perm', 'comment_viewall_perm');
		
		$this->db->select('*, comments.account_id AS c1_account_id');
		$this->db->join('accounts', 'accounts.account_id = comments.account_id', 'left outer');
		$this->db->join('posts', 'posts.post_id = comments.post_id', 'left outer');
		
		$this->db->where('comments.language', $this->lang->get_current_lang());
		
		// sql filter
		$filter = strip_tags(trim($this->input->get('filter')));
		$filter_val = strip_tags(trim($this->input->get('filter_val')));
		if ($list_for == 'front' && ! $comment_view_permission) {
			$this->db->where('comment_status', '1');
		}
		if ($post_id != null) {
			$this->db->where('comments.post_id', $post_id);
		}
		if ($filter != null && $filter_val != null && $list_for == 'admin') {
			$this->db->where($filter, $filter_val);
		}
		// filter out spam
		if ($filter == null || $filter != 'comment_spam_status') {
			$this->db->where('comment_spam_status', 'normal');
		}
		$q = trim($this->input->get('q'));
		if ($q != null) {
			$like_data[0]['field'] = 'comments.subject';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'comments.name';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'comments.comment_body_value';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'comments.email';
			$like_data[3]['match'] = $q;
			$like_data[4]['field'] = 'comments.homepage';
			$like_data[4]['match'] = $q;
			$like_data[5]['field'] = 'comments.ip_address';
			$like_data[5]['match'] = $q;
			$like_data[6]['field'] = 'comments.user_agent';
			$like_data[6]['match'] = $q;
			$this->db->like_group($like_data);
			unset($like_data);
		}
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$sort = strip_tags(trim($this->input->get('sort')));
		if ($orders != null && $sort != null) {
			//$sql .= ' order by '.$orders.' '.$sort.'';
			$this->db->order_by($orders, $sort);
		} else {
			if ($mode == 'thread') {
				if ($orders == 'thread' && $sort == 'desc') {
					$this->db->order_by('thread', 'desc');
				} else {
					$this->db->ar_orderby = array('SUBSTRING(thread, 1, (LENGTH(thread) - 1))');// use this line to fix the problem below until we can find the way out.
					//$this->db->order_by('SUBSTRING(thread, 1, (LENGTH(thread) - 1))');// this does not work because CI add backticks to 1 and mysql understand that it is column name (field name).
				}
			} else {
				$this->db->order_by('comment_id', 'asc');
			}
		}
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get('comments');
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset($this_db);
		
		//html encode search keyword for create links
		$q = urlencode(htmlspecialchars($q));
		
		// pagination-----------------------------
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->uri->uri_string()).'?' . generate_querystring_except(array('per_page'));
		if (isset($data['per_page']) && is_numeric($data['per_page'])) {
			$config['per_page'] = $data['per_page'];
		} else {
			$config['per_page'] = ($list_for == 'admin' ? 20 : $this->config_model->loadSingle('comment_perpage'));
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
		
		// limit query
		$this->db->limit($config['per_page'], ($this->input->get('per_page') == null ? '0' : $this->input->get('per_page')));
		
		$query = $this->db->get('comments');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listComment
	
	
	/**
	 * modify comment content
	 * @param string $content
	 * @return string 
	 */
	public function modifyCommentContent($content = '') 
	{
		if ($this->modules_plug->has_filter('comment_modifybody_value')) {
			// modify content by plugin
			$content = $this->modules_plug->do_filter('comment_modifybody_value', $content);
		} else {
			// modify content.
			$content = htmlspecialchars($content, ENT_QUOTES, config_item('charset'));
			$content = nl2br($content);
		}
		
		return $content;
	}// modifyCommentContent
	
	
	/**
	 * Decode vancode back to an integer.
	 * 
	 * by drupal
	 * 
	 * @param integer $c
	 */
	public function vanCode2Int($c = '00') 
	{
		return base_convert(substr($c, 1), 36, 10);
	}// vanCode2Int
	
	
}

// EOF