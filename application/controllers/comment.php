<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * this controller will be load by module from post_view, page_view. use return view and set third parameter to true.
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class comment extends MY_Controller 
{
	
	
	private $mode = 'thread';// comment mode.
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('comments_model'));
		
		// load helper
		$this->load->helper(array('account', 'date', 'form', 'language'));
		
		// load language
		$this->lang->load('comment');
	}// __construct
	
	
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
	public function comment_view($comments = '', $mode = 'thread') 
	{
		
		if (!isset($comments['items'])) {return '<p class="list-comment-no-comment no-comment">'.$this->lang->line('comment_no_comment').'</p>';}
		
		$cm_account = $this->account_model->getAccountCookie('member');
		$account_id = $cm_account['id'];
		if ($account_id == null) {$account_id = '0';}
		
		//
		$stack = 1;
		$output = '';
		//$output .= '<article>'.$row->comment_body_value.' - id:'.$row->comment_id.' - parent:'.$row->parent_id.' - thread:'.$row->thread.'</article>'."\n";// prototype
		if (is_array($comments['items'])) {
			
			foreach ($comments['items'] as $comment) {
				if ($mode == 'thread') {
					$stack = count(explode('.', $comment->thread));
					if (($stack > $this->comments_model->divs)) {
						for ($i = $this->comments_model->divs; $i < $stack; $i++) {
							$output .= '<div class="indent">'."\n";
							$this->comments_model->divs = ($this->comments_model->divs+1);
						}
					} elseif ($stack < $this->comments_model->divs) {
						$back_stack = (($this->comments_model->divs)-$stack);
						for ($i = 0; $i < $back_stack; $i++) {
							$output .= '</div>'."\n";
							$this->comments_model->divs = ($this->comments_model->divs-1);
						}
					}
				}
				
				// send object to view for use.
				$outval['comment'] = $comment;
				
				// comment_body_value
				$outval['comment_content'] = $this->comments_model->modifyCommentContent($comment->comment_body_value);
				
				// comment class
				$outval['comment_class'] = ($comment->comment_status == '1' ? 'comment-approved' : 'comment-un-approve');
				
				// check edit comment permission.------------------------
				$outval['comment_edit_permission'] = true;
				if ($this->account_model->checkAdminPermission('comment_perm', 'comment_edit_own_perm', $account_id) && $comment->c1_account_id != $account_id) {
					if (!$this->account_model->checkAdminPermission('comment_perm', 'comment_edit_other_perm', $account_id)) {
						$outval['comment_edit_permission'] = false;
					}
				} elseif (!$this->account_model->checkAdminPermission('comment_perm', 'comment_edit_own_perm', $account_id) && $comment->c1_account_id == $account_id) {
					$outval['comment_edit_permission'] = false;
				} elseif (!$this->account_model->checkAdminPermission('comment_perm', 'comment_edit_own_perm', $account_id) && !$this->account_model->checkAdminPermission('comment_perm', 'comment_edit_other_perm', $account_id)) {
					$outval['comment_edit_permission'] = false;
				}
				
				// check delete comment permission.------------------------------
				$outval['comment_delete_permission'] = true;
				if ($this->account_model->checkAdminPermission('comment_perm', 'comment_delete_own_perm', $account_id) && $comment->c1_account_id != $account_id) {
					if (!$this->account_model->checkAdminPermission('comment_perm', 'comment_delete_other_perm', $account_id)) {
						$outval['comment_delete_permission'] = false;
					}
				} elseif (!$this->account_model->checkAdminPermission('comment_perm', 'comment_delete_own_perm', $account_id) && $comment->c1_account_id == $account_id) {
					$outval['comment_delete_permission'] = false;
				} elseif (!$this->account_model->checkAdminPermission('comment_perm', 'comment_delete_own_perm', $account_id) && !$this->account_model->checkAdminPermission('comment_perm', 'comment_delete_other_perm', $account_id)) {
					$outval['comment_delete_permission'] = false;
				}
				
				// check add/reply comment permission-----------------------------
				$outval['comment_postreply_permission'] = false;
				if ($this->account_model->checkAdminPermission('comment_perm', 'comment_allowpost_perm', $account_id)) {
					$outval['comment_postreply_permission'] = true;
				}
				
				//----------------------------------------------------------------------------------------------------
				$output .= '<a id="comment-id-'.$comment->comment_id.'"></a>'."\n";
				$output .= $this->load->view('front/templates/comment/a_comment', $outval, true);
			}// endforeach
			unset($outval);
			
			// clear stack div in thread mode
			if ($mode == 'thread') {
				for ($i = $this->comments_model->divs; $i > 1; $i--) {
					$output .= '</div>'."\n";
					$this->comments_model->divs = ($this->comments_model->divs-1);
				}
			}
			
		}
		return $output;
	}// comment_view
	
	
	public function delete($comment_id = '') 
	{
		if (!is_numeric($comment_id)) {redirect();}
		
		// account id from cookie
		$cm_account = $this->account_model->getAccountCookie('member');
		$account_id = (isset($cm_account['id']) ? $cm_account['id'] : '0');
		unset($cm_account);
		
		// NOT ALLOW GUEST EDIT/DELETE COMMENT.
		if ($account_id == '0') {redirect();}
		
		// check whole permission
		if (!$this->account_model->checkAdminPermission('comment_perm', 'comment_delete_own_perm', $account_id) && !$this->account_model->checkAdminPermission('comment_perm', 'comment_delete_other_perm', $account_id)) {redirect();}
		
		// load user_agent lib for redirect to opened page (referer page)
		$this->load->library('user_agent');
		if ($this->agent->is_referral() && $this->agent->referrer() != current_url()) {
			$output['go_to'] = urlencode($this->agent->referrer());
		}
		if ($this->input->get('rdr') != null) {
			$output['go_to'] = $this->input->get('rdr');
		}
		
		// get comment data
		$data['comment_id'] = $comment_id;
		$row = $this->comments_model->getCommentDataDb($data);
		
		// not found a comment.
		if ($row == null) { redirect(); }
		
		// check permissions for both own and others---------------------------------------------------------------------------------
		if ($this->account_model->checkAdminPermission('comment_perm', 'comment_delete_own_perm') === false && $row->account_id == $account_id) {
			// user has NO permission to edit own and editing own.
			unset($row, $account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('comment_you_have_no_permission_delete_yours')
				)
			);
			redirect();
		} elseif ($this->account_model->checkAdminPermission('comment_perm', 'comment_delete_other_perm') === false && $row->account_id != $account_id) {
			// user has NO permission to edit others and editing others.
			unset($row, $account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('comment_you_have_no_permission_delete_others')
				)
			);
			redirect();
		} 
		// check permissions for both own and others---------------------------------------------------------------------------------
		
		// set value for confirm delete
		$output['post_id'] = $row->post_id;
		$output['account_id'] = $row->account_id;
		$output['subject'] = $row->subject;
		$output['name'] = $row->name;
		$output['comment_body_value'] = $this->comments_model->modifyCommentContent($row->comment_body_value);
		$output['email'] = $row->email;
		$output['homepage'] = $row->homepage;
		$output['row'] = $row;
		
		// delete action
		if ($this->input->post('confirm') == 'yes') {
			// delete comment
			$this->comments_model->delete($comment_id);
			
			// load posts model
			$this->load->model('posts_model');
			
			// update total comment.
			$this->posts_model->updateTotalComment($row->post_id);
			
			// end. go back to post page.
			if (isset($output['go_to'])) {
				redirect($output['go_to']);
			} else {
				redirect('post/'.$row->post_uri_encoded);
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title(lang('comment_delete_comment'));
		// meta tags
		$meta[] = '<meta name="robots" content="noindex, nofollow" />';
		$output['page_meta'] = $this->html_model->gen_tags($meta);
		unset($meta);
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/comment/comment_delete_view', $output);
	}// delete
	
	
	public function edit($comment_id = '') 
	{
		if (!is_numeric($comment_id)) {redirect();}
		
		// account id from cookie
		$cm_account = $this->account_model->getAccountCookie('member');
		$account_id = (isset($cm_account['id']) ? $cm_account['id'] : '0');
		unset($cm_account);
		
		// NOT ALLOW GUEST EDIT/DELETE COMMENT.
		if ($account_id == '0') {redirect();}
		
		// check whole permission
		if (!$this->account_model->checkAdminPermission('comment_perm', 'comment_edit_own_perm', $account_id) && !$this->account_model->checkAdminPermission('comment_perm', 'comment_edit_other_perm', $account_id)) {redirect();}
		
		// load user_agent lib for redirect to opened page
		$this->load->library('user_agent');
		if ($this->agent->is_referral() && $this->agent->referrer() != current_url()) {
			$output['go_to'] = urlencode($this->agent->referrer());
		}
		if ($this->input->get('rdr') != null) {
			$output['go_to'] = $this->input->get('rdr');
		}
		
		// get comment data
		$data['comment_id'] = $comment_id;
		$row = $this->comments_model->getCommentDataDb($data);
		
		// not found a comment.
		if ($row == null) { redirect(); }
		
		// check permissions -------------------------------------------------------------------------------------------------------------------
		if ($this->account_model->checkAdminPermission('comment_perm', 'comment_edit_own_perm') === false && $row->account_id == $account_id) {
			// user has NO permission to edit own and editing own.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('comment_you_have_no_permission_edit_yours')
				)
			);
			redirect();
		} elseif ($this->account_model->checkAdminPermission('comment_perm', 'comment_edit_other_perm') === false && $row->account_id != $account_id) {
			// user has NO permission to edit others and editing others.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('comment_you_have_no_permission_edit_others')
				)
			);
			redirect();
		}
		// check permissions -------------------------------------------------------------------------------------------------------------------
		
		// set values for edit
		$output['post_id'] = $row->post_id;
		$output['account_id'] = $row->account_id;
		$output['subject'] = $row->subject;
		$output['name'] = $row->name;
		$output['comment_body_value'] = $row->comment_body_value;
		$output['email'] = $row->email;
		$output['homepage'] = $row->homepage;
		$output['row'] = $row;
		
		// save action
		if ($this->input->post()) {
			
			$data['comment_id'] = $comment_id;
			$data['name'] = htmlspecialchars(trim($this->input->post('name')), ENT_QUOTES, config_item('charset'));
			$data['subject'] = htmlspecialchars(trim($this->input->post('subject')), ENT_QUOTES, config_item('charset'));
			$data['comment_body_value'] = trim($this->input->post('comment_body_value', true));
				if ($data['subject'] == null) {$data['subject'] = mb_strimwidth(strip_tags($this->input->post('comment_body_value')), 0, 70, '...');}
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'lang:comment_name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('comment_body_value', 'lang:comment_comment', 'trim|required|xss_clean');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->comments_model->edit($data);
				
				if ($result === true) {
					$gotopage = $this->comments_model->getCommentDisplayPage($comment_id, $this->mode);
					
					if (isset($output['go_to'])) {
						redirect($output['go_to']);
					} else {
						redirect('post/'.$row->post_uri_encoded.'?per_page='.$gotopage.'#comment-id-'.$comment_id);
					}
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title(lang('comment_edit_comment'));
		// meta tags
		$meta[] = '<meta name="robots" content="noindex, nofollow" />';
		$output['page_meta'] = $this->html_model->gen_tags($meta);
		unset($meta);
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/comment/comment_edit_view', $output);
	}// edit
	
	
	public function list_comments($comment_allow = '', $post_id = '', $comment_id = '') 
	{
		if (!is_numeric($comment_allow) || !is_numeric($post_id)) {return false;}
		
		// set output post_id and comment_id
		$output['post_id'] = $post_id;
		$output['comment_id'] = $comment_id;
		
		if ($this->input->get('replyto') != null) {
			$output['comment_id'] = strip_tags(trim($this->input->get('replyto')));
		}
		
		// allow new comment?
		$output['comment_allow'] = $comment_allow;
		
		// load config
		$comment_cfg = $this->config_model->load(array('comment_show_notallow', 'comment_perpage'));
		$output['comment_show_notallow'] = $comment_cfg['comment_show_notallow']['value'];
		$output['comment_perpage'] = $comment_cfg['comment_perpage']['value'];
		unset($comment_cfg);
		
		// account id from cookie
		$cm_account = $this->account_model->getAccountCookie('member');
		$output['account_id'] = (isset($cm_account['id']) ? $cm_account['id'] : '0');
		
		// list comments------------------------------------------------------------------------------------------------
		// get comments from db.
		$output['list_item'] = $this->comments_model->listComment($post_id, $this->mode);
		if ($output['list_item'] != null) {
			$output['pagination'] = $this->pagination->create_links();
		}
		// render loop comment by mode
		$output['list_comments'] = $this->comment_view($output['list_item'], $this->mode);
		// end list comments-------------------------------------------------------------------------------------------
		
		// load name from cookie
		$this->load->helper('cookie');
		$output['name'] = htmlspecialchars(trim(get_cookie('comment_name', true)));
		
		$output['comment_add_title'] = lang('comment_post_comment');
		
		if ($this->input->get('replyto') != null) {
			// get comment info from db for reply form.
			$data['comment_id'] = $output['comment_id'];
			$data['posts.post_id'] = $post_id;
			$row = $this->comments_model->getCommentDataDb($data);
			unset($data);
			
			if ($row != null) {
				$output['comment_add_title'] = sprintf(lang('comment_reply_comment'), $row->subject);
				if (strpos($row->subject, sprintf(lang('comment_re'), '')) === false) {
					// prevent re: re: subject (multiple re:)
					$output['subject'] = sprintf(lang('comment_re'), $row->subject);
				} else {
					$output['subject'] = $row->subject;
				}
			}
		}
		
		// post method, new comment posting
		if ($this->input->post()) {
			$post_comment = $this->post_comment();
			if (isset($post_comment['form_status']) && isset($post_comment['form_status_message'])) {
				$output['form_status'] = $post_comment['form_status'];
				$output['form_status_message'] = $post_comment['form_status_message'];
			}
			unset($post_comment);
			
			// re-populate form
			$output['name'] = htmlspecialchars(trim($this->input->post('name')));
			$output['subject'] = htmlspecialchars(trim($this->input->post('subject')));
			$output['comment_body_value'] = htmlspecialchars(trim($this->input->post('comment_body_value')));
		}
		
		// output
		return $this->load->view('front/templates/comment/list_comments', $output, true);
	}// list_comments
	
	
	private function post_comment() 
	{
		$account_id = (int) trim($this->input->post('account_id'));
		if ($account_id == null) {$account_id = '0';}
		
		if (checkAdminPermission('comment_perm', 'comment_allowpost_perm', $account_id)) {
			
			if ($account_id == '0') {
				// flash 'name' into cookie
				$this->load->helper('cookie');
				set_cookie('comment_name', $this->input->post('name'), 1209600);// 2 weeks
			}
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'lang:comment_name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('comment_body_value', 'lang:comment_comment', 'trim|required|xss_clean');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
				return $output;
			} else {
				
				// set data for insert
				$data['parent_id'] = trim($this->input->post('parent_id'));
					if (!is_numeric($data['parent_id'])) {$data['parent_id'] = '0';}
				$data['language'] = $this->lang->get_current_lang();
				$data['post_id'] = (int)trim($this->input->post('post_id'));
				$data['account_id'] = $account_id;
				$data['name'] = htmlspecialchars(trim($this->input->post('name')), ENT_QUOTES, config_item('charset'));
				$data['subject'] = htmlspecialchars(trim($this->input->post('subject')), ENT_QUOTES, config_item('charset'));
				$data['comment_body_value'] = trim($this->input->post('comment_body_value', true));
					if ($data['subject'] == null) {$data['subject'] = mb_strimwidth(strip_tags($this->input->post('comment_body_value')), 0, 70, '...');}
				
				// prepare comment status
				if (checkAdminPermission('comment_perm', 'comment_nomoderation_perm', $account_id)) {
					$data['comment_status'] = (int) 1;
					$data['comment_spam_status'] = 'normal';
				} else {
					$data['comment_status'] = (int) 0;
					
					// module plug check spam. --------------------------------------------------------
					$data['permalink_url'] = urldecode(current_url());
					
					$spam_result = $this->modules_plug->do_action('comment_spam_check', $data);
					
					if (isset($spam_result['comment_spam_check']) && is_array($spam_result['comment_spam_check'])) {
						$data['comment_spam_status'] = array_shift(array_values($spam_result['comment_spam_check']));
					} else {
						$data['comment_spam_status'] = 'normal';
					}
					// module plug check spam. --------------------------------------------------------
					
					unset($data['permalink_url']);
				}
				
				// calculate thread.----------------------------------------------------------------------------
				/**
				 * thanks to drupal thread comment.
				 */
				if ($data['parent_id'] == '0') {
					// this comment has no parent
					$this->db->select_max('thread', 'max');
					$this->db->where('post_id', $data['post_id']);
					$query = $this->db->get('comments');
					$row = $query->row();
					$max = rtrim($row->max, '/');
					$parts = explode('.', $max);
					$firstsegment = $parts[0];
					$thread = $this->comments_model->int2VanCode($this->comments_model->vanCode2Int($firstsegment) + 1) . '/';
					$query->free_result();
				} else {
					// this comment has parent
					// get parent comment
					$this->db->where('post_id', $data['post_id']);
					$this->db->where('comment_id', $data['parent_id']);
					$query = $this->db->get('comments');
					$row = $query->row();
					$parent_thread = (string) rtrim((string) $row->thread, '/');
					$query->free_result();
					
					// get max value in this thread
					$this->db->select_max('thread', 'max');
					$this->db->like('thread', $parent_thread.'.');
					$this->db->where('post_id', $data['post_id']);
					$query = $this->db->get('comments');
					$row = $query->row();
					if ($row->max == '') {
						// first child of parent
						$thread = $parent_thread . '.' . $this->comments_model->int2VanCode(0) . '/';
					} else {
						$max = rtrim($row->max, '/');
						$parts = explode('.', $max);
						$parent_depth = count(explode('.', $parent_thread));
						$last = $parts[$parent_depth];
						$thread = $parent_thread . '.' . $this->comments_model->int2VanCode($this->comments_model->vanCode2Int($last) + 1) . '/';
					}
					$query->free_result();
					unset($row, $query, $max, $parts, $parent_depth, $last);
				}
				$data['thread'] = $thread;
				// end calculate thread---------------------------------------------------------------------------
				
				// insert comment to db-------------------------------------------------------------------------
				$result = $this->comments_model->add($data);
				
				if (isset($result['result']) && $result['result'] === true) {
					if ($data['comment_status'] == '1') {
						$gotopage = $this->comments_model->getCommentDisplayPage($result['id'], $this->mode);
						redirect(current_url().'?per_page='.$gotopage.'#comment-id-'.$result['id']);
					} else {
						$output['form_status'] = 'success';
						$output['form_status_message'] = $this->lang->line('comment_user_wait_approve');
						return $output;
					}
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
					return $output;
				}
				
			}// endif form validation
		}/* else {
			redirect(current_url());
		}*/// do not redirect. leave method post to other modules to work.
	}// post
	
	
}

// EOF