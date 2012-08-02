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
 
class comments_model extends CI_Model {
	
	
	public $divs = 1;// for use with comment threaded
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	/**
	 * add
	 * @param array $data
	 * @return mixed 
	 */
	function add( $data = array() ) {
		// get post name and url and check post exists.
		$this->db->where( 'post_id', $data['post_id'] );
		$query = $this->db->get( 'posts' );
		if ( $query->num_rows() <= 0 ) {$query->free_result(); return $this->lang->line( 'comment_post_not_exists' );}
		$row = $query->row();
		$query->free_result();
		$this->db->set( 'parent_id', $data['parent_id'] );
		$this->db->set( 'post_id', $data['post_id'] );
		$this->db->set( 'account_id', $data['account_id'] );
		$this->db->set( 'name', $data['name'] );
		$this->db->set( 'subject', $data['subject'] );
		$this->db->set( 'comment_body_value', $data['comment_body_value'] );
		if ( isset( $data['email'] ) ) {
			$this->db->set( 'email', $data['email'] );
		}
		if ( isset( $data['homepage'] ) ) {
			$this->db->set( 'homepage', $data['homepage'] );
		}
		$this->db->set( 'comment_status', $data['comment_status'] );
		$this->db->set( 'comment_spam_status', $data['comment_spam_status'] );
		$this->db->set( 'ip_address', $this->input->ip_address() );
		$this->db->set( 'user_agent', $this->input->user_agent() );
		$this->db->set( 'comment_add', time() );
		$this->db->set( 'comment_add_gmt', local_to_gmt( time() ) );
		$this->db->set( 'comment_update', time() );
		$this->db->set( 'comment_update_gmt', local_to_gmt( time() ) );
		$this->db->set( 'thread', $data['thread'] );
		$this->db->insert( 'comments' );
		// get insert id
		$data['comment_id'] = $this->db->insert_id();
		// update post table -> total comments.
		$this->load->model( 'posts_model' );
		$this->posts_model->update_total_comment( $data['post_id'] );
		// comment's module plug
		$this->modules_plug->do_action( 'comment_after_newcomment', $data );
		// email notify admin new comment
		$cfg_val = $this->config_model->load( array( 'comment_new_notify_admin', 'comment_admin_notify_emails', 'mail_sender_email' ) );
		$user_email = '';
		if ( $data['account_id'] != '0' ) {
			$user_email = $this->account_model->show_accounts_info( $data['account_id'], 'account_id', 'account_email' );
		}
		if ( ( $cfg_val['comment_new_notify_admin']['value'] == '2' && mb_stripos( $cfg_val['comment_admin_notify_emails']['value'], $user_email ) === false ) 
		|| ( $cfg_val['comment_new_notify_admin']['value'] == '1' && $data['comment_status'] == '0' ) 
		&& ($data['comment_spam_status'] == 'normal') ) {
			// load email library
			$this->load->library( array( 'email', 'email_template' ) );
			$email_content = $this->email_template->read_template( 'new_comment.html' );
			$email_content = str_replace( "%comment_onpage%", anchor( 'post/'.$row->post_uri_encoded, $row->post_name ), $email_content );
			$email_content = str_replace( "%comment_name%", $data['name'], $email_content );
			$email_content = str_replace( "%comment%", $data['comment_body_value'], $email_content );
			$email_content = str_replace( "%ip_address%", $this->input->ip_address(), $email_content );
			$email_content = str_replace( "%user_agent%", $this->input->user_agent(), $email_content );
			$email_content = str_replace( "%comment_status%", ($data['comment_status'] == '1' ? lang( 'comment_approved' ) : lang( 'comment_notyet_approve' )), $email_content );
			$email_content = str_replace( "%site_url%", site_url(), $email_content );
			$this->email->from( $cfg_val['mail_sender_email']['value'] );
			$this->email->to( $cfg_val['comment_admin_notify_emails']['value'] );
			$this->email->subject( $this->lang->line( 'comment_new_comment_notify' ) );
			$this->email->message( $email_content );
			$this->email->set_alt_message( str_replace( "\t", '', strip_tags( $email_content) ) );
			if ( $this->email->send() == false ) {
				log_message( 'error', 'Could not send email to user.' );
			}
			unset( $email_content, $user_email );
		}
		unset( $query, $row, $cfg_val, $user_email );
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
	/*function comment_view( $comments = '', $mode = 'thread' ) {
		if ( !isset( $comments['items'] ) ) {return '<p class="list-comment-no-comment no-comment">'.$this->lang->line( 'comment_no_comment' ).'</p>';}
		$stack = 1;
		$output = '';
		//$output .= '<article>'.$row->comment_body_value.' - id:'.$row->comment_id.' - parent:'.$row->parent_id.' - thread:'.$row->thread.'</article>'."\n";// prototype
		if ( is_array( $comments['items'] ) ) {
			if ( $mode == 'thread' ) {
				foreach ( $comments['items'] as $row ) {
					$stack = count( explode( '.', $row->thread ) );
					if ( ( $stack > $this->divs ) ) {
						for ( $i = $this->divs; $i < $stack; $i++ ) {
							$output .= '<div class="indent">'."\n";
							$this->divs = ($this->divs+1);
						}
					} elseif ( $stack < $this->divs ) {
						$back_stack = (($this->divs)-$stack);
						for ( $i = 0; $i < $back_stack; $i++ ) {
							$output .= '</div>'."\n";
							$this->divs = ($this->divs-1);
						}
					}
					$output .= '<a id="comment-id-'.$row->comment_id.'"></a>';
					$output .= '<article>'.$row->comment_body_value.' - id:'.$row->comment_id.' - parent:'.$row->parent_id.' - thread:'.$row->thread.' - stack:'.$stack.' - divs:'.$this->divs
							.' '.anchor( current_url().'?replyto='.$row->comment_id.'#addcomment', 'reply' )
							.'</article>'."\n";
				}
				for ( $i = $this->divs; $i > 1; $i-- ) {
					$output .= '</div>'."\n";
					$this->divs = ($this->divs-1);
				}
			} else {
				foreach ( $comments['items'] as $row ) {
					$output .= '<a id="comment-id-'.$row->comment_id.'"></a>';
					$output .= '<article>'.$row->comment_body_value.' - id:'.$row->comment_id.' - parent:'.$row->parent_id.' - thread:'.$row->thread.' - stack:'.$stack.' - divs:'.$this->divs
							.' '.anchor( current_url().'?replyto='.$row->comment_id.'#addcomment', 'reply' )
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
	function delete( $comment_id = '' ) {
		if ( !is_numeric( $comment_id ) ) {return false;}
		// delete from comments table
		$this->db->where( 'parent_id', $comment_id );
		$query = $this->db->get( 'comments' );
		if ( $query->num_rows() > 0 ) {
			foreach ( $query->result() as $row ) {
				$this->delete( $row->comment_id );
			}
		}
		$query->free_result();
		// delete now
		$this->db->where( 'comment_id', $comment_id );
		$this->db->delete( 'comments' );
		// modules plug here
		$this->modules_plug->do_action( 'comment_after_delete', $comment_id );
		// done
		return true;
	}// delete
	
	
	/**
	 * edit
	 * @param array $data
	 * @return boolean 
	 */
	function edit( $data = array() ) {
		$this->db->set( 'name', $data['name'] );
		$this->db->set( 'subject', $data['subject'] );
		$this->db->set( 'comment_body_value', $data['comment_body_value'] );
		if ( isset( $data['email'] ) ) {
			$this->db->set( 'email', $data['email'] );
		}
		if ( isset( $data['homepage'] ) ) {
			$this->db->set( 'homepage', $data['homepage'] );
		}
		$this->db->set( 'comment_update', time() );
		$this->db->set( 'comment_update_gmt', local_to_gmt( time() ) );
		$this->db->where( 'comment_id', $data['comment_id'] );
		$this->db->update( 'comments' );
		// comment's module plug
		$this->modules_plug->do_action( 'comment_after_updatecomment', $data );
		return true;
	}// edit
	
	
	/**
	 * get_comment_display_page
	 * @param integer $comment_id
	 * @param string $mode
	 * @return integer 
	 */
	function get_comment_display_page( $comment_id = '', $mode = 'thread' ) {
		// account id from cookie
		$cm_account = $this->account_model->get_account_cookie( 'admin' );
		$account_id = $cm_account['id'];
		if ( $account_id == null ) {$account_id = '0';}
		//
		$sql = 'select *, count(*) as count from '.$this->db->dbprefix( 'comments' ).' as c1';
		$sql .= ' inner join '.$this->db->dbprefix( 'comments' ).' as c2 on c1.post_id = c2.post_id';
		//$sql .= ' ';
		$sql .= ' and c2.comment_id = '.$comment_id;
		if ( $this->account_model->check_admin_permission( 'comment_perm', 'comment_viewall_perm', $account_id ) ) {
			$sql .= ' and c1.comment_status = 1';
		}
		if ( $mode == 'thread' ) {
			$sql .= ' where SUBSTRING(c1.thread, 1, (LENGTH(c1.thread) -1)) < SUBSTRING(c2.thread, 1, (LENGTH(c2.thread) -1))';
		} else {
			$sql .= ' and c1.comment_id < '.$comment_id;
		}
		$query = $this->db->query( $sql );
		$row = $query->row();
		$query->free_result();
		//
		$num_per_page = $this->config_model->load_single( 'comment_perpage' );
		return ( floor( ( $row->count+1 )/$num_per_page )*$num_per_page );
	}// get_comment_display_page
	
	
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
	function int2vancode($i = 0) {
		$num = base_convert((int) $i, 10, 36);
		$length = strlen($num);

		return chr($length + ord('0') - 1) . $num;
	}// int2vancode
	
	
	/**
	 * list comments
	 * @param integer $post_id
	 * @param string $mode
	 * @param admin|front $list_for
	 * @return mixed 
	 */
	function list_item( $post_id = '',$mode = 'thread', $list_for = 'front' ) {
		// sql query
		$sql = 'select *, c1.account_id as c1_account_id from ' . $this->db->dbprefix( 'comments' ) .' as c1';
		$sql .= ' left outer join ' . $this->db->dbprefix( 'accounts' ) . ' as ac on c1.account_id = ac.account_id';
		$sql .= ' left outer join ' . $this->db->dbprefix( 'posts' ) . ' as p on c1.post_id = p.post_id';
		$sql .= ' where 1';
		$filter = strip_tags( trim( $this->input->get( 'filter' ) ) );
		$filter_val = strip_tags( trim( $this->input->get( 'filter_val' ) ) );
		if ( $list_for == 'front' && !$this->account_model->check_admin_permission( 'comment_perm', 'comment_viewall_perm' ) ) {
			$sql .= ' and comment_status = 1';
		}
		if ( $post_id != null ) {
			$sql .= ' and c1.post_id = ' . $this->db->escape( $post_id );
		}
		if ( $filter != null && $filter_val != null && $list_for == 'admin' ) {
			$sql .= ' and ' . $filter . ' = ' . $this->db->escape( $filter_val );
		}
		// filter out spam
		if ( $filter == null || $filter != 'comment_spam_status' ) {
			$sql .= ' and comment_spam_status = \'normal\'';
		}
		$q = htmlspecialchars( trim( $this->input->get( 'q' ) ) );
		if ( $q != null ) {
			$sql .= ' and (';
			$sql .= " subject like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or name like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or comment_body_value like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or email like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or homepage like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or ip_address like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or user_agent like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= ')';
		}
		// order and sort
		$orders = strip_tags( trim( $this->input->get( 'orders' ) ) );
		$sort = strip_tags( trim( $this->input->get( 'sort' ) ) );
		if ( $orders != null && $sort != null ) {
			$sql .= ' order by '.$orders.' '.$sort.'';
		} else {
			if ( $mode == 'thread' ) {
				if ( $orders == 'thread' && $sort == 'desc' ) {
					$sql .= ' order by thread desc';
				} else {
					$sql .= ' order by SUBSTRING(thread, 1, (LENGTH(thread) - 1))';
				}
			} else {
				$sql .= ' order by comment_id asc';
			}
		}
		// query for count total
		$query = $this->db->query( $sql );
		$total = $query->num_rows();
		$query->free_result();
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		if ( $list_for == 'admin' ) {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?orders='.htmlspecialchars( $orders ).'&amp;sort='.htmlspecialchars( $sort ).( $q != null ?'&amp;q='.$q : '' ).( $filter != null && $filter_val != null ? '&amp;filter='.$filter.'&amp;filter_val='.$filter_val : '' );
			$config['per_page'] = 20;
		} else {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?';
			$config['per_page'] = $this->config_model->load_single( 'comment_perpage' );
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
			return $output;
		}
		$query->free_result();
		return null;
	}// list_item
	
	
	/**
	 * modify_content
	 * @param string $content
	 * @return string 
	 */
	function modify_content( $content = '' ) {
		$original_content = $content;
		// modify content by plugin
		$content = $this->modules_plug->do_action( 'comment_modifybody_value', $content );
		if ( $content == $original_content ) {
			// modify content by core here.
			$content = htmlspecialchars( $content, ENT_QUOTES, config_item( 'charset' ) );
			$content = nl2br( $content );
		}
		return $content;
	}// modify_content
	
	
	/**
	 * Decode vancode back to an integer.
	 * 
	 * by drupal
	 * 
	 * @param integer $c
	 */
	function vancode2int($c = '00') {
		return base_convert(substr($c, 1), 36, 10);
	}// vancode2int
	
	
}

// EOF