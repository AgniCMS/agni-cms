-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 16, 2012 at 10:15 PM
-- Server version: 5.5.23
-- PHP Version: 5.3.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `v_agni`
--

-- --------------------------------------------------------

--
-- Table structure for table `an_accounts`
--

CREATE TABLE IF NOT EXISTS `an_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(255) DEFAULT NULL,
  `account_email` varchar(255) DEFAULT NULL,
  `account_password` varchar(255) DEFAULT NULL,
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
  `account_online_code` varchar(255) DEFAULT NULL COMMENT 'store session code for check dubplicate log in if enabled.',
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

INSERT INTO `an_accounts` (`account_id`, `account_username`, `account_email`, `account_password`, `account_fullname`, `account_birthdate`, `account_avatar`, `account_signature`, `account_timezone`, `account_language`, `account_create`, `account_create_gmt`, `account_last_login`, `account_last_login_gmt`, `account_online_code`, `account_status`, `account_status_text`, `account_new_email`, `account_new_password`, `account_confirm_code`) VALUES
(0, 'Guest', 'none@localhost', NULL, 'Guest', NULL, NULL, NULL, 'UP7', NULL, '2012-04-03 19:25:44', '2012-04-03 12:25:44', NULL, NULL, NULL, 0, 'You can''t login with this account.', NULL, NULL, NULL),
(1, 'admin', 'admin@localhost.com', '6e6f59d20ef87183781895cb20d13c6663f3890c', NULL, NULL, NULL, NULL, 'UP7', NULL, '2011-04-20 19:20:04', '2011-04-20 12:20:04', '2012-06-16 21:33:06', '2012-06-16 14:33:06', '55ca0d35eced54479685815c25f09d6f', 1, NULL, NULL, NULL, NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `an_account_logins`
--

INSERT INTO `an_account_logins` (`account_login_id`, `account_id`, `login_ua`, `login_os`, `login_browser`, `login_ip`, `login_time`, `login_time_gmt`, `login_attempt`, `login_attempt_text`) VALUES
(1, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:28:40', '2012-06-16 14:28:40', 1, 'Success'),
(2, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:29:12', '2012-06-16 14:29:12', 1, 'Success'),
(3, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:29:19', '2012-06-16 14:29:19', 1, 'Success'),
(4, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:29:25', '2012-06-16 14:29:25', 1, 'Success'),
(5, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:32:28', '2012-06-16 14:32:28', 1, 'Success'),
(6, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:32:33', '2012-06-16 14:32:33', 1, 'Success'),
(7, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:32:37', '2012-06-16 14:32:37', 1, 'Success'),
(8, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:32:42', '2012-06-16 14:32:42', 1, 'Success'),
(9, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:32:59', '2012-06-16 14:32:59', 1, 'Success'),
(10, 1, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 'Windows', 'Firefox 13.0', '::1', '2012-06-16 09:33:06', '2012-06-16 14:33:06', 1, 'Success');

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
  PRIMARY KEY (`block_id`),
  KEY `theme_system_name` (`theme_system_name`),
  KEY `area_name` (`area_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `an_blocks`
--

INSERT INTO `an_blocks` (`block_id`, `theme_system_name`, `area_name`, `position`, `language`, `block_name`, `block_file`, `block_values`, `block_status`, `block_except_uri`) VALUES
(1, 'system', 'sidebar', 2, 'th', 'corelogin', 'core/widgets/corelogin/corelogin.php', 'a:2:{s:11:"block_title";s:18:"สมาชิก";s:15:"show_admin_link";s:1:"1";}', 1, NULL),
(2, 'system', 'navigation', 1, 'th', 'corelinks', 'core/widgets/corelinks/corelinks.php', 'a:2:{s:11:"block_title";s:0:"";s:5:"mg_id";s:1:"1";}', 1, NULL),
(3, 'system', 'sidebar', 1, 'th', 'corelangswitch', 'core/widgets/corelangswitch/corelangswitch.php', 'a:1:{s:11:"block_title";s:12:"ภาษา";}', 1, NULL),
(4, 'system', 'sidebar', 3, 'th', 'coresearch', 'core/widgets/coresearch/coresearch.php', 'a:1:{s:11:"block_title";s:15:"ค้นหา";}', 1, 'search'),
(5, 'system', 'breadcrumb', 1, 'th', 'corebreadcrumb', 'core/widgets/corebreadcrumb/corebreadcrumb.php', NULL, 1, NULL),
(6, 'system', 'navigation', 1, 'en', 'corelinks', 'core/widgets/corelinks/corelinks.php', 'a:2:{s:11:"block_title";s:0:"";s:5:"mg_id";s:1:"2";}', 1, NULL),
(7, 'system', 'breadcrumb', 1, 'en', 'corebreadcrumb', 'core/widgets/corebreadcrumb/corebreadcrumb.php', NULL, 1, NULL),
(8, 'system', 'sidebar', 1, 'en', 'corelangswitch', 'core/widgets/corelangswitch/corelangswitch.php', 'a:1:{s:11:"block_title";s:9:"Languages";}', 1, NULL),
(9, 'system', 'sidebar', 2, 'en', 'corelogin', 'core/widgets/corelogin/corelogin.php', 'a:2:{s:11:"block_title";s:6:"Member";s:15:"show_admin_link";s:1:"1";}', 1, NULL),
(10, 'system', 'sidebar', 3, 'en', 'coresearch', 'core/widgets/coresearch/coresearch.php', 'a:1:{s:11:"block_title";s:6:"Search";}', 1, 'search');

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

INSERT INTO `an_ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('00c984e21a18d109509b4a722ba59e1e', '::1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 1339859174, ''),
('00f91f0a97736233a8acecee9308820b', '::1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 1339859479, 'a:1:{s:9:"user_data";s:0:"";}'),
('57142e7800ddc7aa73c54db2b1a6ebb2', '::1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 1339855628, 'a:1:{s:9:"user_data";s:0:"";}');

-- --------------------------------------------------------

--
-- Table structure for table `an_comments`
--

CREATE TABLE IF NOT EXISTS `an_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_comments`
--

INSERT INTO `an_comments` (`comment_id`, `parent_id`, `post_id`, `account_id`, `name`, `subject`, `comment_body_value`, `email`, `homepage`, `comment_status`, `comment_spam_status`, `ip_address`, `user_agent`, `comment_add`, `comment_add_gmt`, `comment_update`, `comment_update_gmt`, `thread`) VALUES
(1, 0, 7, 1, 'admin', 'ความคิดเห็นแรก', 'สวัสดี\r\nนี่คือความคิดเห็น. คุณสามารถเข้ามาจัดการลบ, แก้, เผยแพร่, เลิกเผยแพร่ ความคิดเห็นได้ผ่านทางหน้าผู้ดูแล.', NULL, NULL, 1, 'normal', '::1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0', 1339858841, 1339833641, 1339858859, 1339833659, '01/');

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
('member_admin_verify_emails', 'admin@localhost.com', 1, 'emails of administrators to notice them when new member registration'),
('mail_protocol', 'mail', 1, 'The mail sending protocol.\r\nmail, sendmail, smtp'),
('mail_mailpath', '/usr/sbin/sendmail', 1, 'The server path to Sendmail.'),
('mail_smtp_host', 'localhost', 1, 'SMTP Server Address.'),
('mail_smtp_user', 'no-reply@localhost.com', 1, 'SMTP Username.'),
('mail_smtp_pass', '', 1, 'SMTP Password.'),
('mail_smtp_port', '25', 1, 'SMTP Port.'),
('mail_sender_email', 'no-reply@localhost.com', 1, 'Email for ''sender'''),
('content_show_title', '1', 1, 'show h1 content title'),
('content_show_time', '1', 1, 'show content time. (publish, update, ...)'),
('content_show_author', '1', 1, 'show content author.'),
('content_items_perpage', '10', 1, 'number of posts per page.'),
('comment_allow', NULL, 1, 'allow site-wide new comment?\r\n0=no, 1=yes, null=up to each post''s setting'),
('comment_show_notallow', '0', 1, 'list old comments even if comment setting change to not allow new comment?\r\n0=not show, 1=show\r\nif 0 the system will not show comments when setting to not allow new comment.'),
('comment_perpage', '40', 1, 'number of comments per page'),
('comment_new_notify_admin', '1', 1, 'notify admin when new comment?\r\n0=no, 1=yes(require moderation only), 2=yes(all)'),
('comment_admin_notify_emails', 'admin@localhost.com', 1, 'emails of administrators to notify when new comment or moderation required ?'),
('media_allowed_types', '7z|aac|ace|ai|aif|aifc|aiff|avi|bmp|css|csv|doc|docx|eml|flv|gif|gz|h264|h.264|htm|html|jpeg|jpg|js|json|log|mid|midi|mov|mp3|mpeg|mpg|pdf|png|ppt|psd|swf|tar|text|tgz|tif|tiff|txt|wav|webm|word|xls|xlsx|xml|xsl|zip', 1, 'media upload allowed file types.\r\nthese types must specified mime-type in config/mimes.php');

-- --------------------------------------------------------

--
-- Table structure for table `an_files`
--

CREATE TABLE IF NOT EXISTS `an_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `language` varchar(5) DEFAULT NULL,
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

INSERT INTO `an_frontpage_category` (`tid`, `language`) VALUES
(1, 'th');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `an_menu_groups`
--

INSERT INTO `an_menu_groups` (`mg_id`, `mg_name`, `mg_description`, `language`) VALUES
(1, 'นำทาง', 'navigation menu', 'th'),
(2, 'Navigation', 'navigation menu', 'en');

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
  `link_url` varchar(255) DEFAULT NULL,
  `link_text` varchar(255) DEFAULT NULL,
  `custom_link` text COMMENT 'when normal link field doesn''t fullfill your need',
  `nlevel` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`mi_id`),
  KEY `mg_id` (`mg_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `an_menu_items`
--

INSERT INTO `an_menu_items` (`mi_id`, `parent_id`, `mg_id`, `position`, `language`, `mi_type`, `type_id`, `link_url`, `link_text`, `custom_link`, `nlevel`) VALUES
(1, 0, 1, 1, 'th', 'link', NULL, '/', 'หน้าแรก', '', 1),
(2, 0, 1, 2, 'th', 'category', 5, '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87', 'การปรับแต่ง', '', 1),
(3, 2, 1, 3, 'th', 'category', 6, '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87/%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B8%98%E0%B8%B5%E0%B8%A1', 'ธีม', '', 2),
(4, 2, 1, 4, 'th', 'category', 7, '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87/%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', 'โมดูล', '', 2),
(5, 0, 1, 5, 'th', 'category', 2, '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99', 'การใช้งาน', '', 1),
(6, 5, 1, 6, 'th', 'category', 4, '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99/%E0%B8%98%E0%B8%B5%E0%B8%A1', 'ธีม', '', 2),
(7, 5, 1, 7, 'th', 'category', 3, '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99/%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', 'โมดูล', '', 2),
(8, 0, 1, 8, 'th', 'page', 6, '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AD%E0%B8%99%E0%B8%B8%E0%B8%8D%E0%B8%B2%E0%B8%95', 'การอนุญาต', '', 1),
(9, 0, 1, 9, 'th', 'page', 5, '%E0%B9%80%E0%B8%81%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%81%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%A3%E0%B8%B2', 'เกี่ยวกับเรา', '', 1),
(10, 0, 2, 1, 'en', 'link', NULL, '/', 'Home', '', 1),
(11, 0, 2, 2, 'en', 'category', 14, 'To-modify', 'To modify', '', 1),
(12, 11, 2, 4, 'en', 'category', 16, 'To-modify/modify-module', 'Module', '', 2),
(13, 11, 2, 3, 'en', 'category', 15, 'To-modify/modify-theme', 'Theme', '', 2),
(14, 0, 2, 5, 'en', 'category', 11, 'To-use', 'To use', '', 1),
(15, 14, 2, 7, 'en', 'category', 13, 'To-use/Module', 'Module', '', 2),
(16, 14, 2, 6, 'en', 'category', 12, 'To-use/Theme', 'Theme', '', 2),
(17, 0, 2, 8, 'en', 'page', 14, 'License', 'License', '', 1),
(18, 0, 2, 9, 'en', 'page', 13, 'About-us', 'About us', '', 1);

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
  `module_enable` int(1) NOT NULL DEFAULT '0',
  `module_install` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `module_system_name` (`module_system_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `an_modules`
--

INSERT INTO `an_modules` (`module_id`, `module_system_name`, `module_name`, `module_url`, `module_version`, `module_description`, `module_author`, `module_author_url`, `module_enable`, `module_install`) VALUES
(1, 'core', 'Agni core module.', 'http://www.agnicms.org', NULL, 'Agni cms core module.', 'vee w.', 'http://okvee.net', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `an_plugins`
--

CREATE TABLE IF NOT EXISTS `an_plugins` (
  `plugin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_system_name` varchar(255) NOT NULL,
  `plugin_name` varchar(255) NOT NULL,
  `plugin_url` varchar(255) DEFAULT NULL,
  `plugin_version` varchar(30) DEFAULT NULL,
  `plugin_description` text,
  `plugin_author` varchar(255) DEFAULT NULL,
  `plugin_author_url` varchar(255) DEFAULT NULL,
  `plugin_active` int(1) DEFAULT '0',
  PRIMARY KEY (`plugin_id`),
  UNIQUE KEY `plugin_system_name` (`plugin_system_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Deprecated, use modules plug instead' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `an_plugins`
--


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
  `post_uri` varchar(255) NOT NULL,
  `post_uri_encoded` tinytext,
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
  PRIMARY KEY (`post_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='for content-type article, pages, static content.' AUTO_INCREMENT=15 ;

--
-- Dumping data for table `an_posts`
--

INSERT INTO `an_posts` (`post_id`, `revision_id`, `account_id`, `post_type`, `language`, `theme_system_name`, `post_name`, `post_uri`, `post_uri_encoded`, `post_feature_image`, `post_comment`, `post_status`, `post_add`, `post_add_gmt`, `post_update`, `post_update_gmt`, `post_publish_date`, `post_publish_date_gmt`, `meta_title`, `meta_description`, `meta_keywords`, `content_settings`, `comment_count`) VALUES
(1, 1, 1, 'article', 'th', NULL, 'การเลือกใช้ธีม', 'การเลือกใช้ธีม', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%80%E0%B8%A5%E0%B8%B7%E0%B8%AD%E0%B8%81%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%98%E0%B8%B5%E0%B8%A1', NULL, 1, 1, 1339857793, 1339832593, 1339857793, 1339832593, 1339857793, 1339832593, NULL, NULL, NULL, NULL, 0),
(2, 2, 1, 'article', 'th', NULL, 'การใช้งานโมดูล', 'การใช้งานโมดูล', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', NULL, 1, 1, 1339857850, 1339832650, 1339857850, 1339832650, 1339857850, 1339832650, NULL, NULL, NULL, NULL, 0),
(3, 3, 1, 'article', 'th', 'quick-start', 'การสร้างธีม', 'การสร้างธีม', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AA%E0%B8%A3%E0%B9%89%E0%B8%B2%E0%B8%87%E0%B8%98%E0%B8%B5%E0%B8%A1', NULL, 1, 1, 1339857882, 1339832682, 1339857882, 1339832682, 1339857882, 1339832682, NULL, NULL, NULL, NULL, 0),
(4, 4, 1, 'article', 'th', NULL, 'การสร้างโมดูล', 'การสร้างโมดูล', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AA%E0%B8%A3%E0%B9%89%E0%B8%B2%E0%B8%87%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', NULL, 1, 1, 1339857908, 1339832708, 1339857908, 1339832708, 1339857908, 1339832708, NULL, NULL, NULL, NULL, 0),
(5, 5, 1, 'page', 'th', NULL, 'เกี่ยวกับเรา', 'เกี่ยวกับเรา', '%E0%B9%80%E0%B8%81%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%81%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%A3%E0%B8%B2', NULL, 0, 1, 1339858312, 1339833112, 1339858312, 1339833112, 1339858312, 1339833112, NULL, NULL, NULL, NULL, 0),
(6, 6, 1, 'page', 'th', NULL, 'การอนุญาต', 'การอนุญาต', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AD%E0%B8%99%E0%B8%B8%E0%B8%8D%E0%B8%B2%E0%B8%95', NULL, 0, 1, 1339858346, 1339833146, 1339858346, 1339833146, 1339858346, 1339833146, NULL, NULL, NULL, NULL, 0),
(7, 7, 1, 'article', 'th', NULL, 'ยินดีต้อนรับ', 'ยินดีต้อนรับ', '%E0%B8%A2%E0%B8%B4%E0%B8%99%E0%B8%94%E0%B8%B5%E0%B8%95%E0%B9%89%E0%B8%AD%E0%B8%99%E0%B8%A3%E0%B8%B1%E0%B8%9A', NULL, 1, 1, 1339858746, 1339833546, 1339859452, 1339834252, 1339858746, 1339833546, 'ยินดีต้อนรับ', NULL, 'อัคนี CMS, Agni CMS, Codeigniter CMS', 'a:3:{s:18:"content_show_title";s:0:"";s:17:"content_show_time";s:1:"0";s:19:"content_show_author";s:1:"0";}', 1),
(8, 8, 1, 'article', 'en', NULL, 'Choose theme', 'Choose-theme', 'Choose-theme', NULL, 1, 1, 1339859139, 1339833939, 1339859156, 1339833956, 1339859139, 1339833939, NULL, NULL, NULL, NULL, 0),
(9, 9, 1, 'article', 'en', NULL, 'How to use module', 'How-to-use-module', 'How-to-use-module', NULL, 1, 1, 1339859266, 1339834066, 1339859266, 1339834066, 1339859266, 1339834066, NULL, NULL, NULL, NULL, 0),
(10, 10, 1, 'article', 'en', 'quick-start', 'Create a theme', 'Create-a-theme', 'Create-a-theme', NULL, 1, 1, 1339859301, 1339834101, 1339859301, 1339834101, 1339859301, 1339834101, NULL, NULL, NULL, NULL, 0),
(11, 11, 1, 'article', 'en', NULL, 'Create a module', 'Create-a-module', 'Create-a-module', NULL, 1, 1, 1339859342, 1339834142, 1339859342, 1339834142, 1339859342, 1339834142, NULL, NULL, NULL, NULL, 0),
(12, 12, 1, 'article', 'en', NULL, 'Welcome', 'Welcome', 'Welcome', NULL, 1, 1, 1339859471, 1339834271, 1339859471, 1339834271, 1339859471, 1339834271, 'Welcome', NULL, 'Agni CMS, Codeigniter CMS', 'a:3:{s:18:"content_show_title";s:0:"";s:17:"content_show_time";s:1:"0";s:19:"content_show_author";s:1:"0";}', 0),
(13, 13, 1, 'page', 'en', NULL, 'About us', 'About-us', 'About-us', NULL, 0, 1, 1339859518, 1339834318, 1339859518, 1339834318, 1339859518, 1339834318, NULL, NULL, NULL, NULL, 0),
(14, 14, 1, 'page', 'en', NULL, 'License', 'License', 'License', NULL, 0, 1, 1339859534, 1339834334, 1339859534, 1339834334, 1339859534, 1339834334, NULL, NULL, NULL, NULL, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `an_post_revision`
--

INSERT INTO `an_post_revision` (`revision_id`, `post_id`, `account_id`, `header_value`, `body_value`, `body_summary`, `log`, `revision_date`, `revision_date_gmt`) VALUES
(1, 1, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339857793, 1339832593),
(2, 2, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339857850, 1339832650),
(3, 3, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339857882, 1339832682),
(4, 4, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339857908, 1339832708),
(5, 5, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339858312, 1339833112),
(6, 6, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339858346, 1339833146),
(7, 7, 1, NULL, '<p>คุณกำลังเริ่มใช้งานอัคนี CMS คุณสามารถบันทึกเข้าทางหน้าผู้ดูแล และเข้ามาจัดการได้ทุกๆอย่างที่คุณต้องการ</p>', '<p>คุณกำลังใช้งานอัคนี CMS</p>', NULL, 1339858746, 1339833546),
(8, 8, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339859139, 1339833939),
(9, 9, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339859266, 1339834066),
(10, 10, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339859301, 1339834101),
(11, 11, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339859342, 1339834142),
(12, 12, 1, NULL, '<p>You are starting to use Agni CMS, you can log in to administrator page and manage everything you want.</p>', '<p>You are using Agni CMS</p>', NULL, 1339859471, 1339834271),
(13, 13, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339859518, 1339834318),
(14, 14, 1, NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse nec ante auctor est pulvinar dictum. Morbi bibendum ipsum vel metus condimentum rhoncus id sed lorem. Nullam fermentum dapibus eros vitae feugiat. Donec vel mollis tortor. Mauris felis urna, posuere et pharetra et, luctus at diam. Etiam faucibus orci a sem blandit at posuere odio consequat. Maecenas sem mauris, interdum vel varius ac, lobortis ut magna. Etiam sed felis diam, id sagittis sem. Morbi rutrum interdum lacus ornare congue. Suspendisse dignissim ligula ut eros egestas et fermentum felis congue.</p>\r\n<p>Vestibulum malesuada varius metus non ultrices. Phasellus eget enim urna. Fusce ac erat sit amet ante convallis ornare. Nulla facilisi. Nulla accumsan tempor venenatis. Cras faucibus, diam nec posuere ullamcorper, eros mi pellentesque quam, vel semper purus elit ut est. Duis congue varius massa ac faucibus. Nulla facilisi.</p>\r\n<p>Nam pretium egestas est, eu scelerisque magna bibendum in. Quisque eros augue, luctus ac gravida sed, tristique vel turpis. Proin ut nisi lectus, in rhoncus mi. Nulla varius erat nec nunc sagittis consectetur. Integer non augue augue. Cras nec tellus in ipsum faucibus malesuada sagittis in elit. Ut egestas, felis sit amet auctor scelerisque, lacus risus fermentum magna, non rhoncus tellus tellus at nulla. Pellentesque id quam sed purus tempus fringilla non vel ipsum. Nulla dapibus consectetur ante, eget hendrerit neque lacinia vel. Cras vehicula vehicula mauris, quis placerat ipsum egestas in.</p>\r\n<p>Pellentesque arcu ante, tincidunt eu dapibus id, mattis a neque. Cras quis mauris est. Aliquam dolor velit, elementum vel consectetur ut, dapibus ac nisl. Pellentesque tempor pharetra quam in blandit. Duis felis dolor, pretium nec imperdiet vitae, malesuada ac nulla. Fusce adipiscing luctus purus, vitae egestas arcu mattis vel. Nunc rhoncus varius ultricies. Nulla urna urna, congue vitae pulvinar non, suscipit ut dui. Fusce et sollicitudin dui. Ut nisl mauris, gravida in placerat in, adipiscing ac nisi. Mauris lorem est, pellentesque nec euismod sit amet, facilisis non nulla. Ut porttitor, nibh a congue volutpat, ipsum ipsum suscipit ligula, sit amet blandit dolor velit at nisi. Cras eget sem nibh, sed lacinia quam. Ut luctus lectus vel est vulputate placerat. Maecenas turpis mauris, consequat vitae blandit a, facilisis sed libero.</p>\r\n<p>Integer placerat elementum rhoncus. Cras hendrerit tortor non eros condimentum vel fringilla tellus congue. Cras ut justo sit amet lectus sodales pretium non nec mauris. Vestibulum non ligula massa, eu semper ante. Aenean neque nibh, fermentum accumsan porta vel, facilisis at elit. Sed a justo non urna blandit pellentesque quis sit amet nunc. Etiam eget ligula ac lorem dapibus commodo. In arcu sem, volutpat a tempor quis, suscipit ac velit. Etiam vel metus eget mauris feugiat dapibus nec non nisl. Curabitur gravida nunc at purus aliquet sollicitudin. Integer rutrum porttitor elit, sed tempor mauris viverra id. Quisque pulvinar risus non tellus condimentum sed tristique ligula varius. Morbi tristique mauris vitae est imperdiet a porttitor tortor congue. Ut sollicitudin, mauris molestie mattis gravida, enim justo rutrum augue, id dignissim erat mauris vel mi. Pellentesque mauris elit, luctus ac blandit vel, ultrices vel nisi. Donec dictum sagittis vestibulum.</p>\r\n</div>', NULL, NULL, 1339859534, 1339834334);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='store id between taxonomy/posts' AUTO_INCREMENT=35 ;

--
-- Dumping data for table `an_taxonomy_index`
--

INSERT INTO `an_taxonomy_index` (`index_id`, `post_id`, `tid`, `position`, `create`) VALUES
(1, 1, 2, 1, 1339857793),
(2, 1, 4, 1, 1339857793),
(3, 1, 1, 1, 1339857793),
(4, 1, 8, 1, 1339857793),
(5, 2, 2, 2, 1339857850),
(6, 2, 3, 1, 1339857850),
(7, 2, 1, 2, 1339857850),
(8, 2, 9, 1, 1339857850),
(9, 3, 5, 1, 1339857882),
(10, 3, 6, 1, 1339857882),
(11, 3, 1, 3, 1339857882),
(12, 3, 8, 1, 1339857882),
(13, 4, 5, 2, 1339857908),
(14, 4, 7, 1, 1339857908),
(15, 4, 1, 4, 1339857908),
(16, 4, 9, 1, 1339857908),
(17, 7, 1, 5, 1339858746),
(18, 8, 10, 1, 1339859139),
(19, 8, 11, 1, 1339859140),
(20, 8, 12, 1, 1339859140),
(21, 8, 17, 1, 1339859156),
(22, 9, 10, 2, 1339859266),
(23, 9, 11, 2, 1339859266),
(24, 9, 13, 1, 1339859266),
(25, 9, 18, 1, 1339859266),
(26, 10, 10, 3, 1339859301),
(27, 10, 14, 1, 1339859301),
(28, 10, 15, 1, 1339859301),
(29, 10, 17, 1, 1339859301),
(30, 11, 10, 4, 1339859342),
(31, 11, 14, 2, 1339859342),
(32, 11, 16, 1, 1339859342),
(33, 11, 18, 1, 1339859342),
(34, 12, 10, 5, 1339859472);

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
  `t_uri` varchar(255) DEFAULT NULL,
  `t_uri_encoded` tinytext,
  `t_uris` text COMMENT 'full path of uri, eg. animal/4legs/cat (no end slash and must uri encoded) ',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `theme_system_name` varchar(255) DEFAULT NULL,
  `nlevel` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `an_taxonomy_term_data`
--

INSERT INTO `an_taxonomy_term_data` (`tid`, `parent_id`, `language`, `t_type`, `t_total`, `t_name`, `t_description`, `t_uri`, `t_uri_encoded`, `t_uris`, `meta_title`, `meta_description`, `meta_keywords`, `theme_system_name`, `nlevel`) VALUES
(1, 0, 'th', 'category', 5, 'หน้าแรก', NULL, 'หน้าแรก', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B9%81%E0%B8%A3%E0%B8%81', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B9%81%E0%B8%A3%E0%B8%81', NULL, NULL, NULL, NULL, 1),
(2, 0, 'th', 'category', 2, 'การใช้งาน', NULL, 'การใช้งาน', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99', NULL, NULL, NULL, NULL, 1),
(3, 2, 'th', 'category', 1, 'โมดูล', NULL, 'โมดูล', '%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99/%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', NULL, NULL, NULL, 'quick-start', 2),
(4, 2, 'th', 'category', 1, 'ธีม', NULL, 'ธีม', '%E0%B8%98%E0%B8%B5%E0%B8%A1', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99/%E0%B8%98%E0%B8%B5%E0%B8%A1', NULL, NULL, NULL, 'quick-start', 2),
(5, 0, 'th', 'category', 2, 'การปรับแต่ง', NULL, 'การปรับแต่ง', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87', NULL, NULL, NULL, NULL, 1),
(6, 5, 'th', 'category', 1, 'ธีม', NULL, 'ปรับแต่ง-ธีม', '%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B8%98%E0%B8%B5%E0%B8%A1', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87/%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B8%98%E0%B8%B5%E0%B8%A1', NULL, NULL, NULL, NULL, 2),
(7, 5, 'th', 'category', 1, 'โมดูล', NULL, 'ปรับแต่ง-โมดูล', '%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87/%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', NULL, NULL, NULL, NULL, 2),
(8, 0, 'th', 'tag', 2, 'ธีม', NULL, 'ธีม', '%E0%B8%98%E0%B8%B5%E0%B8%A1', '%E0%B8%98%E0%B8%B5%E0%B8%A1', NULL, NULL, NULL, NULL, 1),
(9, 0, 'th', 'tag', 2, 'โมดูล', NULL, 'โมดูล', '%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', '%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', NULL, NULL, NULL, NULL, 1),
(10, 0, 'en', 'category', 5, 'Front page', NULL, 'Front-page', 'Front-page', 'Front-page', NULL, NULL, NULL, NULL, 1),
(11, 0, 'en', 'category', 2, 'To use', NULL, 'To-use', 'To-use', 'To-use', NULL, NULL, NULL, NULL, 1),
(12, 11, 'en', 'category', 1, 'Theme', NULL, 'Theme', 'Theme', 'To-use/Theme', NULL, NULL, NULL, NULL, 2),
(13, 11, 'en', 'category', 1, 'Module', NULL, 'Module', 'Module', 'To-use/Module', NULL, NULL, NULL, NULL, 2),
(14, 0, 'en', 'category', 2, 'To modify', NULL, 'To-modify', 'To-modify', 'To-modify', NULL, NULL, NULL, NULL, 1),
(15, 14, 'en', 'category', 1, 'Theme', NULL, 'modify-theme', 'modify-theme', 'To-modify/modify-theme', NULL, NULL, NULL, NULL, 2),
(16, 14, 'en', 'category', 1, 'Module', NULL, 'modify-module', 'modify-module', 'To-modify/modify-module', NULL, NULL, NULL, NULL, 2),
(17, 0, 'en', 'tag', 2, 'theme', NULL, 'theme', 'theme', 'theme', NULL, NULL, NULL, NULL, 1),
(18, 0, 'en', 'tag', 2, 'module', NULL, 'module', 'module', 'module', NULL, NULL, NULL, NULL, 1);

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
  `theme_enable` int(1) NOT NULL DEFAULT '0',
  `theme_default` int(1) NOT NULL DEFAULT '0',
  `theme_default_admin` int(1) NOT NULL DEFAULT '0',
  `theme_settings` text,
  PRIMARY KEY (`theme_id`),
  UNIQUE KEY `theme_system_name` (`theme_system_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `an_themes`
--

INSERT INTO `an_themes` (`theme_id`, `theme_system_name`, `theme_name`, `theme_url`, `theme_version`, `theme_description`, `theme_enable`, `theme_default`, `theme_default_admin`, `theme_settings`) VALUES
(1, 'system', 'System', 'http://www.agnicms.org', '1.0', 'Agni system theme.', 1, 1, 1, NULL),
(2, 'quick-start', 'Quick Start', 'http://www.agnicms.org', '1.0', 'For theme designer quick start theme.', 1, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `an_url_alias`
--

CREATE TABLE IF NOT EXISTS `an_url_alias` (
  `alias_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_type` varchar(255) DEFAULT NULL COMMENT 'content type eg. article, page, category, tag, ...etc...',
  `c_id` int(11) DEFAULT NULL COMMENT 'those content id',
  `uri` varchar(255) DEFAULT NULL,
  `uri_encoded` tinytext,
  `redirect_to` varchar(255) DEFAULT NULL COMMENT 'for use in url redirect',
  `redirect_to_encoded` TINYTEXT NULL DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`alias_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `an_url_alias`
--

INSERT INTO `an_url_alias` (`alias_id`, `c_type`, `c_id`, `uri`, `uri_encoded`, `language`) VALUES
(1, 'category', 1, 'หน้าแรก', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B9%81%E0%B8%A3%E0%B8%81', 'th'),
(2, 'category', 2, 'การใช้งาน', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99', 'th'),
(3, 'category', 3, 'โมดูล', '%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', 'th'),
(4, 'category', 4, 'ธีม', '%E0%B8%98%E0%B8%B5%E0%B8%A1', 'th'),
(5, 'category', 5, 'การปรับแต่ง', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87', 'th'),
(6, 'category', 6, 'ปรับแต่ง-ธีม', '%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B8%98%E0%B8%B5%E0%B8%A1', 'th'),
(7, 'category', 7, 'ปรับแต่ง-โมดูล', '%E0%B8%9B%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%95%E0%B9%88%E0%B8%87-%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', 'th'),
(8, 'tag', 8, 'ธีม', '%E0%B8%98%E0%B8%B5%E0%B8%A1', 'th'),
(9, 'article', 1, 'การเลือกใช้ธีม', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%80%E0%B8%A5%E0%B8%B7%E0%B8%AD%E0%B8%81%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%98%E0%B8%B5%E0%B8%A1', 'th'),
(10, 'tag', 9, 'โมดูล', '%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', 'th'),
(11, 'article', 2, 'การใช้งานโมดูล', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%83%E0%B8%8A%E0%B9%89%E0%B8%87%E0%B8%B2%E0%B8%99%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', 'th'),
(12, 'article', 3, 'การสร้างธีม', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AA%E0%B8%A3%E0%B9%89%E0%B8%B2%E0%B8%87%E0%B8%98%E0%B8%B5%E0%B8%A1', 'th'),
(13, 'article', 4, 'การสร้างโมดูล', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AA%E0%B8%A3%E0%B9%89%E0%B8%B2%E0%B8%87%E0%B9%82%E0%B8%A1%E0%B8%94%E0%B8%B9%E0%B8%A5', 'th'),
(14, 'page', 5, 'เกี่ยวกับเรา', '%E0%B9%80%E0%B8%81%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%81%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%A3%E0%B8%B2', 'th'),
(15, 'page', 6, 'การอนุญาต', '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AD%E0%B8%99%E0%B8%B8%E0%B8%8D%E0%B8%B2%E0%B8%95', 'th'),
(16, 'article', 7, 'ยินดีต้อนรับ', '%E0%B8%A2%E0%B8%B4%E0%B8%99%E0%B8%94%E0%B8%B5%E0%B8%95%E0%B9%89%E0%B8%AD%E0%B8%99%E0%B8%A3%E0%B8%B1%E0%B8%9A', 'th'),
(17, 'category', 10, 'Front-page', 'Front-page', 'en'),
(18, 'category', 11, 'To-use', 'To-use', 'en'),
(19, 'category', 12, 'Theme', 'Theme', 'en'),
(20, 'category', 13, 'Module', 'Module', 'en'),
(21, 'category', 14, 'To-modify', 'To-modify', 'en'),
(22, 'category', 15, 'modify-theme', 'modify-theme', 'en'),
(23, 'category', 16, 'modify-module', 'modify-module', 'en'),
(24, 'article', 8, 'Choose-theme', 'Choose-theme', 'en'),
(25, 'tag', 17, 'theme', 'theme', 'en'),
(26, 'tag', 18, 'module', 'module', 'en'),
(27, 'tag', 19, 'module-1', 'module-1', 'en'),
(28, 'article', 9, 'How-to-use-module', 'How-to-use-module', 'en'),
(29, 'article', 10, 'Create-a-theme', 'Create-a-theme', 'en'),
(30, 'article', 11, 'Create-a-module', 'Create-a-module', 'en'),
(31, 'article', 12, 'Welcome', 'Welcome', 'en'),
(32, 'page', 13, 'About-us', 'About-us', 'en'),
(33, 'page', 14, 'License', 'License', 'en');
