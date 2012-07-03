<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 * ------------------------------------------------------------------
 * this model works with url. eg: check allowed and disallowed uri in category, tag, article, page.
 */
 
class url_model extends CI_Model {
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	function validate_allow_url( $uri = '' ) {
		if ( $uri == null ) {return null;}
		// any disallowed uri list here as array
		$disallowed_url = array(
			'account',
			'account/changeemail2',
			'account/confirm-register',
			'account/edit-profile',
			'account/forgotpw',
			'account/login',
			'account/logout',
			'account/register',
			'account/resend-activate',
			'account/resetpw2',
			'account/view-logins',
			
			'site-admin',
			'site-admin/',
			
			'area',
			'area/demo',
			
			'author',
			
			'category',
			
			'comment',
			'comment/comment_view',
			'comment/delete',
			'comment/edit',
			'comment/list_comments',
			'comment/post_comment',
			
			'index',
			
			'post',
			'post/preview',
			'post/revision',
			'post/view',
			
			'search',
			
			'tag',
			
			'modules',
			'modules/core',
			
			'public',
			'public/css-fw',
			'public/images',
			'public/js',
			'public/themes',
			'public/upload',
			
			'index.php'
		);
		// start to check
		if ( in_array( $uri, $disallowed_url ) ) {
			return 'disallowed-uri';
		}
		// not found in disallowed uri
		return $uri;
	}// validate_allow_url
	
	
}

// EOF