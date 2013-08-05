<?php
/**
 * @author mr.v
 * @copyright http://okvee.net
 */

class blog_admin extends MX_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('url'));
		
		// load langauge (for use in permission setting page)
		$this->lang->load('blog/blog');
	}// __construct
	
	
	/**
	 * _define_permission
	 * กำหนด permission ที่ method นี้ภายใน controller นี้ (ชื่อโมดูล_admin) สำหรับการทำงานแบบ module
	 * @return array
	 */
	public function _define_permission() 
	{
		return array('blog_admin' => array('blog_all_post', 'blog_add_post', 'blog_edit_post', 'blog_delete_post'));
	}// _define_permission
	
	
	public function admin_nav() 
	{
		return '<li>' . anchor('#', lang('blog_blog'), array('onclick' => 'return false')) . '
				<ul>
					<li>' . anchor('blog/site-admin/blog', lang('blog_manage_posts')) . '</li>
					<li>' . anchor('blog/site-admin/blog/add', lang('blog_new_post')) . '</li>
				</ul>
			</li>';
	}// admin_nav
	
	
}