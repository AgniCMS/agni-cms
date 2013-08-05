<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Email extends CI_Email 
{
	
	
	public function __construct($config = array()) 
	{
		parent::__construct($config);
		$this->load_config();
	}
	
	
	public function load_config() 
	{
		$this->ci =& get_instance();
		$this->ci->load->model('config_model');
		$config_email = $this->ci->config_model->load(array('mail_protocol', 'mail_mailpath', 'mail_smtp_host', 'mail_smtp_user', 'mail_smtp_pass', 'mail_smtp_port'));
		
		foreach ($config_email as $key => $item) {
			$config[str_replace('mail_', '', $key)] = $item['value'];
		}
		
		$config['mailtype'] = 'html';
		$config['newline'] = "\r\n";
		
		if ((strpos($config_email['mail_smtp_host']['value'], 'gmail.com') !== false || strpos($config_email['mail_smtp_host']['value'], 'googlemail.com') !== false) && $config_email['mail_protocol']['value'] == 'smtp') {
			// send with gmail
			$config['starttls'] = true;
		}
		
		$this->initialize($config);
	}// load_config
	

	// --------------------------------------------------------------------
	
	/**
	 * Set Email Subject
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function subject($subject)
	{
		$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
		//$subject = $this->_prep_q_encoding($subject);
		$this->_set_header('Subject', $subject);
		return $this;
	}// subject
	
}

/* End of file MY_Email.php */
/* Location: ./system/libraries/MY_Email.php */