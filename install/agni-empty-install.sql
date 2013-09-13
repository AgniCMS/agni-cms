-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2013 at 10:16 AM
-- Server version: 5.5.24
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `v_agnicms_multisite`
--

-- --------------------------------------------------------

--
-- Table structure for table `an_accounts`
--

CREATE TABLE IF NOT EXISTS `an_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(255) DEFAULT NULL,
  `account_email` varchar(255) DEFAULT NULL,
  `account_salt` varchar(255) DEFAULT NULL COMMENT 'store salt for use when hashing password',
  `account_password` tinytext,
  `account_fullname` varchar(255) DEFAULT NULL,
  `account_birthdate` date DEFAULT NULL,
  `account_avatar` varchar(255) DEFAULT NULL,
  `account_signature` text,
  `account_timezone` varchar(10) NOT NULL DEFAULT 'UP7',
  `account_language` varchar(10) DEFAULT NULL,
  `account_create` datetime DEFAULT NULL COMMENT 'local time',
  `account_create_gmt` datetime DEFAULT NULL COMMENT 'gmt0, utc0',
  `account_last_login` datetime DEFAULT NULL,
  `account_last_login_gmt` datetime DEFAULT NULL,
  `account_online_code` varchar(255) DEFAULT NULL COMMENT 'store session code for check dubplicate log in if enabled. deprecated',
  `account_status` int(1) NOT NULL DEFAULT '0' COMMENT '0=disable, 1=enable',
  `account_status_text` varchar(255) DEFAULT NULL,
  `account_new_email` varchar(255) DEFAULT NULL,
  `account_new_password` varchar(255) DEFAULT NULL,
  `account_confirm_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_accounts`
--

INSERT INTO `an_accounts` (`account_id`, `account_username`, `account_email`, `account_salt`, `account_password`, `account_fullname`, `account_birthdate`, `account_avatar`, `account_signature`, `account_timezone`, `account_language`, `account_create`, `account_create_gmt`, `account_last_login`, `account_last_login_gmt`, `account_online_code`, `account_status`, `account_status_text`, `account_new_email`, `account_new_password`, `account_confirm_code`) VALUES
(0, 'Guest', 'none@localhost', NULL, NULL, 'Guest', NULL, NULL, NULL, 'UP7', NULL, '2012-04-03 19:25:44', '2012-04-03 12:25:44', NULL, NULL, NULL, 0, 'You can''t login with this account.', NULL, NULL, NULL),
(1, 'admin', 'admin@localhost', NULL, '$P$FPnwJAQzX498tYCbbIfYTbdYiOCShE0', NULL, NULL, NULL, NULL, 'UP7', NULL, '2011-04-20 19:20:04', '2011-04-20 12:20:04', '2012-06-16 17:09:17', '2012-06-16 10:09:17', 'e2135bb4faf4fb999e3bbebe86ed1cdf', 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `an_account_fields`
--

CREATE TABLE IF NOT EXISTS `an_account_fields` (
  `account_id` int(11) NOT NULL COMMENT 'refer to accounts.account_id',
  `field_name` varchar(255) DEFAULT NULL,
  `field_value` text,
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `an_account_fields`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_account_level`
--

CREATE TABLE IF NOT EXISTS `an_account_level` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_group_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`level_id`),
  KEY `level_group_id` (`level_group_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `an_account_level`
--

INSERT INTO `an_account_level` (`level_id`, `level_group_id`, `account_id`) VALUES
(1, 4, 0),
(2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `an_account_level_group`
--

CREATE TABLE IF NOT EXISTS `an_account_level_group` (
  `level_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(255) DEFAULT NULL,
  `level_description` text,
  `level_priority` int(5) NOT NULL DEFAULT '1' COMMENT 'lower is more higher priority',
  PRIMARY KEY (`level_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `an_account_level_group`
--

INSERT INTO `an_account_level_group` (`level_group_id`, `level_name`, `level_description`, `level_priority`) VALUES
(1, 'Super administrator', 'Site owner.', 1),
(2, 'Administrator', NULL, 2),
(3, 'Member', 'For registered user.', 999),
(4, 'Guest', 'For non register user.', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `an_account_level_permission`
--

CREATE TABLE IF NOT EXISTS `an_account_level_permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_group_id` int(11) NOT NULL,
  `permission_page` varchar(255) NOT NULL,
  `permission_action` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  KEY `level_group_id` (`level_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_account_level_permission`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_account_logins`
--

CREATE TABLE IF NOT EXISTS `an_account_logins` (
  `account_login_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `login_ua` varchar(255) DEFAULT NULL,
  `login_os` varchar(255) DEFAULT NULL,
  `login_browser` varchar(255) DEFAULT NULL,
  `login_ip` varchar(50) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `login_time_gmt` datetime DEFAULT NULL,
  `login_attempt` int(1) NOT NULL DEFAULT '0' COMMENT '0=fail, 1=success',
  `login_attempt_text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`account_login_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_account_logins`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_account_sites`
--

CREATE TABLE IF NOT EXISTS `an_account_sites` (
  `account_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL COMMENT 'refer to accounts.account_id',
  `site_id` int(11) DEFAULT NULL COMMENT 'refer to sites.site_id',
  `account_last_login` bigint(20) DEFAULT NULL,
  `account_last_login_gmt` bigint(20) DEFAULT NULL,
  `account_online_code` varchar(255) DEFAULT NULL COMMENT 'store session code for check dubplicate log in if enabled.',
  PRIMARY KEY (`account_site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_account_sites`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_blocks`
--

CREATE TABLE IF NOT EXISTS `an_blocks` (
  `block_id` int(11) NOT NULL AUTO_INCREMENT,
  `theme_system_name` varchar(255) DEFAULT NULL,
  `area_name` varchar(255) DEFAULT NULL,
  `position` int(5) NOT NULL DEFAULT '1',
  `language` varchar(5) DEFAULT NULL,
  `block_name` varchar(255) DEFAULT NULL,
  `block_file` varchar(255) DEFAULT NULL,
  `block_values` text,
  `block_status` int(1) NOT NULL DEFAULT '0' COMMENT '0=disable, 1=enable',
  `block_except_uri` text,
  `block_only_uri` text,
  PRIMARY KEY (`block_id`),
  KEY `theme_system_name` (`theme_system_name`),
  KEY `area_name` (`area_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_blocks`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_ci_sessions`
--

CREATE TABLE IF NOT EXISTS `an_ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(50) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `an_ci_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_comments`
--

CREATE TABLE IF NOT EXISTS `an_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(5) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'comment author''s name',
  `subject` varchar(255) DEFAULT NULL,
  `comment_body_value` longtext,
  `email` varchar(255) DEFAULT NULL COMMENT 'comment author''s email',
  `homepage` varchar(255) DEFAULT NULL COMMENT 'comment author''s homepage',
  `comment_status` int(1) NOT NULL DEFAULT '0' COMMENT '0=not publish, 1=published',
  `comment_spam_status` varchar(100) NOT NULL DEFAULT 'normal' COMMENT 'comment spam status (normal, spam, ham, what ever)',
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `comment_add` bigint(20) DEFAULT NULL,
  `comment_add_gmt` bigint(20) DEFAULT NULL,
  `comment_update` bigint(20) DEFAULT NULL,
  `comment_update_gmt` bigint(20) DEFAULT NULL,
  `thread` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `account_id` (`account_id`),
  KEY `post_id` (`post_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_comment_fields`
--

CREATE TABLE IF NOT EXISTS `an_comment_fields` (
  `comment_id` int(11) NOT NULL,
  `field_name` varchar(255) DEFAULT NULL,
  `field_value` text,
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `an_comment_fields`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_config`
--

CREATE TABLE IF NOT EXISTS `an_config` (
  `config_name` varchar(255) DEFAULT NULL,
  `config_value` varchar(255) DEFAULT NULL,
  `config_core` int(1) DEFAULT '0' COMMENT '0=no, 1=yes. if config core then please do not delete from db.',
  `config_description` text,
  KEY `config_name` (`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `an_config`
--

INSERT INTO `an_config` (`config_name`, `config_value`, `config_core`, `config_description`) VALUES
('site_name', 'Agni CMS', 1, 'website name'),
('page_title_separator', ' &rsaquo; ', 1, 'page title separator. eg. site name | page'),
('site_timezone', 'UP7', 1, 'website default timezone'),
('duplicate_login', '0', 1, 'allow log in more than 1 place, session? set to 1/0 to allow/disallow.'),
('allow_avatar', '1', 1, 'set to 1 if use avatar or set to 0 if not use it.'),
('avatar_size', '200', 1, 'set file size in Kilobyte.'),
('avatar_allowed_types', 'gif|jpg|png', 1, 'avatar allowe file types (see reference from codeigniter)\r\neg. gif|jpg|png'),
('avatar_path', 'public/upload/avatar/', 1, 'path to directory for upload avatar'),
('member_allow_register', '1', 1, 'allow users to register'),
('member_register_notify_admin', '0', 1, 'send email to notify admin when new member register?'),
('member_verification', '1', 1, 'member verification method.\r\n1 = verify by email\r\n2 = wait for admin verify'),
('member_admin_verify_emails', 'admin@localhost', 1, 'emails of administrators to notice them when new member registration'),
('mail_protocol', 'mail', 1, 'The mail sending protocol.\r\nmail, sendmail, smtp'),
('mail_mailpath', '/usr/sbin/sendmail', 1, 'The server path to Sendmail.'),
('mail_smtp_host', 'localhost', 1, 'SMTP Server Address.'),
('mail_smtp_user', 'no-reply@localhost', 1, 'SMTP Username.'),
('mail_smtp_pass', '', 1, 'SMTP Password.'),
('mail_smtp_port', '25', 1, 'SMTP Port.'),
('mail_sender_email', 'no-reply@localhost', 1, 'Email for ''sender'''),
('content_show_title', '1', 1, 'show h1 content title'),
('content_show_time', '1', 1, 'show content time. (publish, update, ...)'),
('content_show_author', '1', 1, 'show content author.'),
('content_items_perpage', '10', 1, 'number of posts per page.'),
('comment_allow', NULL, 1, 'allow site-wide new comment?\r\n0=no, 1=yes, null=up to each post''s setting'),
('comment_show_notallow', '0', 1, 'list old comments even if comment setting change to not allow new comment?\r\n0=not show, 1=show\r\nif 0 the system will not show comments when setting to not allow new comment.'),
('comment_perpage', '40', 1, 'number of comments per page'),
('comment_new_notify_admin', '1', 1, 'notify admin when new comment?\r\n0=no, 1=yes(require moderation only), 2=yes(all)'),
('comment_admin_notify_emails', 'admin@localhost', 1, 'emails of administrators to notify when new comment or moderation required ?'),
('media_allowed_types', '7z|aac|ace|ai|aif|aifc|aiff|avi|bmp|css|csv|doc|docx|eml|flv|gif|gz|h264|h.264|htm|html|jpeg|jpg|js|json|log|mid|midi|mov|mp3|mpeg|mpg|pdf|png|ppt|psd|swf|tar|text|tgz|tif|tiff|txt|wav|webm|word|xls|xlsx|xml|xsl|zip', 1, 'media upload allowed file types.\r\nthese types must specified mime-type in config/mimes.php'),
('agni_version', '1.4', 1, 'current Agni CMS version. use for compare with auto update.'),
('angi_auto_update', '1', 1, 'enable auto update. recommended setting to \'true\' (1 = true, 0 = false) for use auto update, but if you want manual update (core hacking or custom modification through core files) set to false.'),
('agni_auto_update_url', 'http://agnicms.org/modules/updateservice/update.xml', 1, 'url of auto update.'),
('agni_system_cron', '1', 1, 'agni system cron. set to true (1) if you want to run cron from system or set to false (0) if you already have real cron job call to http://yourdomain.tld/path-installed/cron .'),
('ftp_host', '', 1, 'FTP host name. ftp is very useful in update/download files from remote host to current host.'),
('ftp_username', '', 1, 'FTP username'),
('ftp_password', '', 1, 'FTP password'),
('ftp_port', '21', 1, 'FTP port. usually is 21'),
('ftp_passive', 'true', 1, 'FTP passive mode'),
('ftp_basepath', '/public_html/', 1, 'FTP base path. store path to public html (web root)');

-- --------------------------------------------------------

--
-- Table structure for table `an_files`
--

CREATE TABLE IF NOT EXISTS `an_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `folder` text COMMENT 'contain path to folder that store this file.',
  `file` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_original_name` varchar(255) DEFAULT NULL,
  `file_client_name` varchar(255) DEFAULT NULL,
  `file_mime_type` varchar(255) DEFAULT NULL,
  `file_ext` varchar(50) DEFAULT NULL,
  `file_size` int(11) NOT NULL DEFAULT '0',
  `media_name` varchar(255) DEFAULT NULL COMMENT 'name this file for use in frontend.',
  `media_description` text,
  `media_keywords` varchar(255) DEFAULT NULL,
  `file_add` bigint(20) DEFAULT NULL,
  `file_add_gmt` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_frontpage_category`
--

CREATE TABLE IF NOT EXISTS `an_frontpage_category` (
  `tid` int(11) DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `an_frontpage_category`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_menu_groups`
--

CREATE TABLE IF NOT EXISTS `an_menu_groups` (
  `mg_id` int(11) NOT NULL AUTO_INCREMENT,
  `mg_name` varchar(255) DEFAULT NULL,
  `mg_description` varchar(255) DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`mg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_menu_groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_menu_items`
--

CREATE TABLE IF NOT EXISTS `an_menu_items` (
  `mi_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `mg_id` int(11) DEFAULT NULL COMMENT 'menu group id',
  `position` int(5) NOT NULL DEFAULT '1',
  `language` varchar(5) DEFAULT NULL,
  `mi_type` varchar(255) DEFAULT NULL COMMENT 'refer to post_type, tax_type, link, custom_link',
  `type_id` int(11) DEFAULT NULL,
  `link_url` text,
  `link_text` varchar(255) DEFAULT NULL,
  `custom_link` text COMMENT 'when normal link field doesn''t fullfill your need',
  `nlevel` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`mi_id`),
  KEY `mg_id` (`mg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_menu_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_modules`
--

CREATE TABLE IF NOT EXISTS `an_modules` (
  `module_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module_system_name` varchar(255) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `module_url` varchar(255) DEFAULT NULL,
  `module_version` varchar(30) DEFAULT NULL,
  `module_description` text,
  `module_author` varchar(255) DEFAULT NULL,
  `module_author_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `module_system_name` (`module_system_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_modules`
--

INSERT INTO `an_modules` (`module_id`, `module_system_name`, `module_name`, `module_url`, `module_version`, `module_description`, `module_author`, `module_author_url`) VALUES
(1, 'core', 'Agni core module.', 'http://www.agnicms.org', NULL, 'Agni cms core module.', 'vee w.', 'http://okvee.net');

-- --------------------------------------------------------

--
-- Table structure for table `an_module_sites`
--

CREATE TABLE IF NOT EXISTS `an_module_sites` (
  `module_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `module_enable` int(1) NOT NULL DEFAULT '0',
  `module_install` int(1) NOT NULL DEFAULT '0' COMMENT 'use when the module want to install db, script or anything.',
  PRIMARY KEY (`module_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_module_sites`
--

INSERT INTO `an_module_sites` (`module_site_id`, `module_id`, `site_id`, `module_enable`, `module_install`) VALUES
(1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `an_posts`
--

CREATE TABLE IF NOT EXISTS `an_posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `revision_id` int(11) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `post_type` varchar(255) DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  `theme_system_name` varchar(255) DEFAULT NULL,
  `post_name` varchar(255) DEFAULT NULL,
  `post_uri` tinytext,
  `post_uri_encoded` text,
  `post_feature_image` int(11) DEFAULT NULL COMMENT 'refer to file id',
  `post_comment` int(1) NOT NULL DEFAULT '0' COMMENT 'allow comment? 0=no, 1=yes',
  `post_status` int(1) NOT NULL DEFAULT '1' COMMENT 'published? 0=no, 1=yes',
  `post_add` bigint(20) DEFAULT NULL,
  `post_add_gmt` bigint(20) DEFAULT NULL,
  `post_update` bigint(20) DEFAULT NULL,
  `post_update_gmt` bigint(20) DEFAULT NULL,
  `post_publish_date` bigint(20) DEFAULT NULL,
  `post_publish_date_gmt` bigint(20) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `content_settings` text COMMENT 'store serialize array of settings',
  `comment_count` int(9) NOT NULL DEFAULT '0',
  `view_count` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='for content-type article, pages, static content.' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_posts`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_post_fields`
--

CREATE TABLE IF NOT EXISTS `an_post_fields` (
  `post_id` int(11) NOT NULL,
  `field_name` varchar(255) DEFAULT NULL,
  `field_value` text,
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='store each field of posts';

--
-- Dumping data for table `an_post_fields`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_post_revision`
--

CREATE TABLE IF NOT EXISTS `an_post_revision` (
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `header_value` text,
  `body_value` longtext,
  `body_summary` text,
  `log` text COMMENT 'explain that what was changed',
  `revision_date` bigint(20) DEFAULT NULL,
  `revision_date_gmt` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_post_revision`
--



-- --------------------------------------------------------

--
-- Table structure for table `an_queue`
--

CREATE TABLE IF NOT EXISTS `an_queue` (
  `queue_id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_name` varchar(255) DEFAULT NULL,
  `queue_data` longtext,
  `queue_create` bigint(20) DEFAULT NULL,
  `queue_update` bigint(20) DEFAULT NULL,
  `queue_expire` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='store ''to do'' job queue.' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_queue`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_sites`
--

CREATE TABLE IF NOT EXISTS `an_sites` (
  `site_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) DEFAULT NULL,
  `site_domain` varchar(255) DEFAULT NULL COMMENT 'ex. domain.com, sub.domain.com with out http://',
  `site_status` int(1) NOT NULL DEFAULT '0' COMMENT '0=disable, 1=enable',
  `site_create` bigint(20) DEFAULT NULL,
  `site_create_gmt` bigint(20) DEFAULT NULL,
  `site_update` bigint(20) DEFAULT NULL,
  `site_update_gmt` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_sites`
--

INSERT INTO `an_sites` (`site_id`, `site_name`, `site_domain`, `site_status`, `site_create`, `site_create_gmt`, `site_update`, `site_update_gmt`) VALUES
(1, 'Agni CMS', NULL, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `an_syslog`
--

CREATE TABLE IF NOT EXISTS `an_syslog` (
  `sl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'system log id',
  `account_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `sl_type` varchar(100) DEFAULT NULL COMMENT 'log type. example system, user action',
  `sl_message` text,
  `sl_variables` longtext,
  `sl_url` tinytext COMMENT 'url of event.',
  `sl_referer` tinytext COMMENT 'url referer of event',
  `sl_ipaddress` varchar(50) DEFAULT NULL,
  `sl_datetime` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`sl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain system log.' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_syslog`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_taxonomy_fields`
--

CREATE TABLE IF NOT EXISTS `an_taxonomy_fields` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(255) DEFAULT NULL,
  `field_value` text ,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_taxonomy_fields`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_taxonomy_index`
--

CREATE TABLE IF NOT EXISTS `an_taxonomy_index` (
  `index_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL DEFAULT '0' COMMENT 'post id',
  `tid` int(11) NOT NULL DEFAULT '0' COMMENT 'term id',
  `position` int(9) NOT NULL DEFAULT '1',
  `create` bigint(20) DEFAULT NULL COMMENT 'local date time',
  PRIMARY KEY (`index_id`),
  KEY `post_id` (`post_id`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='store id between taxonomy/posts' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_taxonomy_index`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_taxonomy_term_data`
--

CREATE TABLE IF NOT EXISTS `an_taxonomy_term_data` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(5) DEFAULT NULL,
  `t_type` varchar(255) DEFAULT NULL COMMENT 'type of taxonomy. eg. tag, category',
  `t_total` int(11) NOT NULL DEFAULT '0' COMMENT 'total posts relate to this.',
  `t_name` varchar(255) DEFAULT NULL,
  `t_description` longtext,
  `t_uri` tinytext,
  `t_uri_encoded` text,
  `t_uris` longtext COMMENT 'full path of uri, eg. animal/4legs/cat (no end slash and must uri encoded)',
  `t_position` int(9) NOT NULL DEFAULT '0' COMMENT 'for use as position order when some module need it.',
  `t_status` int(1) NOT NULL DEFAULT '1' COMMENT '0=not publish, 1=publish',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `theme_system_name` varchar(255) DEFAULT NULL,
  `nlevel` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_taxonomy_term_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `an_themes`
--

CREATE TABLE IF NOT EXISTS `an_themes` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `theme_system_name` varchar(255) NOT NULL,
  `theme_name` varchar(255) NOT NULL,
  `theme_url` varchar(255) DEFAULT NULL,
  `theme_version` varchar(30) DEFAULT NULL,
  `theme_description` text,
  PRIMARY KEY (`theme_id`),
  UNIQUE KEY `theme_system_name` (`theme_system_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_themes`
--

INSERT INTO `an_themes` (`theme_id`, `theme_system_name`, `theme_name`, `theme_url`, `theme_version`, `theme_description`) VALUES
(1, 'system', 'System', 'http://www.agnicms.org', '1.0', 'Agni system theme.');

-- --------------------------------------------------------

--
-- Table structure for table `an_theme_sites`
--

CREATE TABLE IF NOT EXISTS `an_theme_sites` (
  `theme_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `theme_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `theme_enable` int(1) NOT NULL DEFAULT '0',
  `theme_default` int(1) NOT NULL DEFAULT '0',
  `theme_default_admin` int(11) NOT NULL DEFAULT '0',
  `theme_settings` text,
  PRIMARY KEY (`theme_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_theme_sites`
--

INSERT INTO `an_theme_sites` (`theme_site_id`, `theme_id`, `site_id`, `theme_enable`, `theme_default`, `theme_default_admin`, `theme_settings`) VALUES
(1, 1, 1, 1, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `an_url_alias`
--

CREATE TABLE IF NOT EXISTS `an_url_alias` (
  `alias_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_type` varchar(255) DEFAULT NULL COMMENT 'content type eg. article, page, category, tag, ...etc...',
  `c_id` int(11) DEFAULT NULL COMMENT 'those content id',
  `uri` tinytext,
  `uri_encoded` text,
  `redirect_to` tinytext COMMENT 'for use in url redirect',
  `redirect_to_encoded` text,
  `redirect_code` int(5) DEFAULT NULL COMMENT '301 permanent, 302 temporarily',
  `language` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`alias_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_url_alias`
--

