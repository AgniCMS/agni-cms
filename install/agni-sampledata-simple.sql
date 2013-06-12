-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2013 at 11:55 AM
-- Server version: 5.5.24
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `v_agnicms_multisite`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `an_blocks`
--

INSERT INTO `an_blocks` (`block_id`, `theme_system_name`, `area_name`, `position`, `language`, `block_name`, `block_file`, `block_values`, `block_status`, `block_except_uri`, `block_only_uri`) VALUES
(1, 'system', 'navigation', 1, 'en', 'corelinks', 'core/widgets/corelinks/corelinks.php', 'a:2:{s:11:"block_title";s:0:"";s:5:"mg_id";s:1:"2";}', 1, NULL, NULL),
(2, 'system', 'navigation', 1, 'th', 'corelinks', 'core/widgets/corelinks/corelinks.php', 'a:2:{s:11:"block_title";s:0:"";s:5:"mg_id";s:1:"1";}', 1, NULL, NULL),
(3, 'system', 'breadcrumb', 1, 'en', 'corebreadcrumb', 'core/widgets/corebreadcrumb/corebreadcrumb.php', NULL, 1, NULL, NULL),
(4, 'system', 'breadcrumb', 1, 'th', 'corebreadcrumb', 'core/widgets/corebreadcrumb/corebreadcrumb.php', NULL, 1, NULL, NULL),
(5, 'system', 'sidebar', 2, 'th', 'corelangswitch', 'core/widgets/corelangswitch/corelangswitch.php', 'a:1:{s:11:"block_title";s:12:"ภาษา";}', 1, NULL, NULL),
(6, 'system', 'sidebar', 2, 'en', 'corelangswitch', 'core/widgets/corelangswitch/corelangswitch.php', 'a:1:{s:11:"block_title";s:8:"Language";}', 1, NULL, NULL),
(7, 'system', 'sidebar', 3, 'th', 'corelogin', 'core/widgets/corelogin/corelogin.php', 'a:2:{s:11:"block_title";s:18:"สมาชิก";s:15:"show_admin_link";s:1:"1";}', 1, NULL, NULL),
(8, 'system', 'sidebar', 3, 'en', 'corelogin', 'core/widgets/corelogin/corelogin.php', 'a:2:{s:11:"block_title";s:6:"Member";s:15:"show_admin_link";s:1:"1";}', 1, NULL, NULL),
(9, 'system', 'sidebar', 1, 'th', 'coresearch', 'core/widgets/coresearch/coresearch.php', 'a:1:{s:11:"block_title";s:0:"";}', 1, 'search', NULL),
(10, 'system', 'sidebar', 1, 'en', 'coresearch', 'core/widgets/coresearch/coresearch.php', 'a:1:{s:11:"block_title";s:0:"";}', 1, 'search', NULL),
(11, 'system', 'footer', 1, 'en', 'corehtmlbox', 'core/widgets/corehtmlbox/corehtmlbox.php', 'a:2:{s:11:"block_title";s:0:"";s:4:"html";s:40:"Agni CMS build on Codeigniter framework.";}', 1, NULL, NULL),
(12, 'system', 'footer', 1, 'th', 'corehtmlbox', 'core/widgets/corehtmlbox/corehtmlbox.php', 'a:2:{s:11:"block_title";s:0:"";s:4:"html";s:66:"Agni CMS สร้างอยู่บน  Codeigniter framework.";}', 1, NULL, NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `an_comments`
--

INSERT INTO `an_comments` (`comment_id`, `parent_id`, `language`, `post_id`, `account_id`, `name`, `subject`, `comment_body_value`, `email`, `homepage`, `comment_status`, `comment_spam_status`, `ip_address`, `user_agent`, `comment_add`, `comment_add_gmt`, `comment_update`, `comment_update_gmt`, `thread`) VALUES
(1, 0, 'th', 1, 1, 'admin', 'ความคิดเห็นแรก', 'นี่คือความคิดเห็น.\r\n\r\nคุณสามารถจัดการแก้ไขหรือลบได้ โดยการบันทึกเข้ามาทางหน้าผู้ดูแล และจัดการตามต้องการ.', NULL, NULL, 1, 'normal', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0', 1366371477, 1366371477, 1366371477, 1366371477, '01/'),
(2, 0, 'en', 2, 1, 'admin', 'First comment', 'This is comment.\r\n\r\nYou can edit or delete comment by log in to site admin page.', NULL, NULL, 1, 'normal', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0', 1366371508, 1366371508, 1366371508, 1366371508, '01/');

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
(1, 'th'),
(2, 'en');

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
(1, 'เมนูนำทาง', NULL, 'th'),
(2, 'Navigation menu', NULL, 'en');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `an_menu_items`
--

INSERT INTO `an_menu_items` (`mi_id`, `parent_id`, `mg_id`, `position`, `language`, `mi_type`, `type_id`, `link_url`, `link_text`, `custom_link`, `nlevel`) VALUES
(1, 0, 1, 1, 'th', 'link', NULL, '/', 'หน้าแรก', '', 1),
(2, 0, 2, 1, 'en', 'link', NULL, '/', 'Home', '', 1),
(3, 0, 1, 2, 'th', 'page', 3, '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B8%95%E0%B8%B1%E0%B8%A7%E0%B8%AD%E0%B8%A2%E0%B9%88%E0%B8%B2%E0%B8%87', 'หน้าตัวอย่าง', '', 1),
(4, 0, 2, 2, 'en', 'page', 4, 'Sample-page', 'Sample page', '', 1);

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
  PRIMARY KEY (`post_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='for content-type article, pages, static content.' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `an_posts`
--

INSERT INTO `an_posts` (`post_id`, `revision_id`, `account_id`, `post_type`, `language`, `theme_system_name`, `post_name`, `post_uri`, `post_uri_encoded`, `post_feature_image`, `post_comment`, `post_status`, `post_add`, `post_add_gmt`, `post_update`, `post_update_gmt`, `post_publish_date`, `post_publish_date_gmt`, `meta_title`, `meta_description`, `meta_keywords`, `content_settings`, `comment_count`) VALUES
(1, 1, 1, 'article', 'th', NULL, 'สวัสดี', 'สวัสดี', '%E0%B8%AA%E0%B8%A7%E0%B8%B1%E0%B8%AA%E0%B8%94%E0%B8%B5', NULL, 1, 1, 1366371359, 1366371359, 1366371359, 1366371359, 1366371359, 1366371359, NULL, NULL, NULL, NULL, 1),
(2, 2, 1, 'article', 'en', NULL, 'Hello', 'Hello', 'Hello', NULL, 1, 1, 1366371431, 1366371431, 1366371431, 1366371431, 1366371431, 1366371431, NULL, NULL, NULL, NULL, 1),
(3, 3, 1, 'page', 'th', NULL, 'หน้าตัวอย่าง', 'หน้าตัวอย่าง', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B8%95%E0%B8%B1%E0%B8%A7%E0%B8%AD%E0%B8%A2%E0%B9%88%E0%B8%B2%E0%B8%87', NULL, 0, 1, 1366371743, 1366371743, 1366371743, 1366371743, 1366371743, 1366371743, NULL, NULL, NULL, NULL, 0),
(4, 4, 1, 'page', 'en', NULL, 'Sample page', 'Sample-page', 'Sample-page', NULL, 0, 1, 1366371745, 1366371745, 1366371745, 1366371745, 1366371745, 1366371745, NULL, NULL, NULL, NULL, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `an_post_revision`
--

INSERT INTO `an_post_revision` (`revision_id`, `post_id`, `account_id`, `header_value`, `body_value`, `body_summary`, `log`, `revision_date`, `revision_date_gmt`) VALUES
(1, 1, 1, NULL, '<p>ยินดีต้อนรับสู่ระบบจัดการเนื้อหา อัคนี.</p>\r\n<p>นี่คือบทความแรกของคุณ คุณสามารถเข้ามาแก้ไขหรือลบได้ทันที.</p>', NULL, NULL, 1366371359, 1366371359),
(2, 2, 1, NULL, '<p>Welcome to Agni CMS.</p>\r\n<p>This is your first article, you can edit or delete in site admin page.</p>', NULL, NULL, 1366371431, 1366371431),
(3, 3, 1, NULL, '<p>หน้า คือที่แสดงข้อมูลที่ไม่อิงกับการแบ่งประเภทใดๆ และมีข้อมูลที่ค่อนข้างนิ่ง.</p>', NULL, NULL, 1366371743, 1366371743),
(4, 4, 1, NULL, '<p>Page is display area that does not refer to any category and has static data.</p>', NULL, NULL, 1366371745, 1366371745);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='store id between taxonomy/posts' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `an_taxonomy_index`
--

INSERT INTO `an_taxonomy_index` (`index_id`, `post_id`, `tid`, `position`, `create`) VALUES
(1, 1, 1, 1, 1366371359),
(2, 2, 2, 1, 1366371432);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `an_taxonomy_term_data`
--

INSERT INTO `an_taxonomy_term_data` (`tid`, `parent_id`, `language`, `t_type`, `t_total`, `t_name`, `t_description`, `t_uri`, `t_uri_encoded`, `t_uris`, `t_position`, `t_status`, `meta_title`, `meta_description`, `meta_keywords`, `theme_system_name`, `nlevel`) VALUES
(1, 0, 'th', 'category', 1, 'หน้าแรก', NULL, 'หน้าแรก', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B9%81%E0%B8%A3%E0%B8%81', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B9%81%E0%B8%A3%E0%B8%81', 0, 1, NULL, NULL, NULL, NULL, 1),
(2, 0, 'en', 'category', 1, 'Home', NULL, 'Home', 'Home', 'Home', 0, 1, NULL, NULL, NULL, NULL, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `an_url_alias`
--

INSERT INTO `an_url_alias` (`alias_id`, `c_type`, `c_id`, `uri`, `uri_encoded`, `redirect_to`, `redirect_to_encoded`, `redirect_code`, `language`) VALUES
(1, 'category', 1, 'หน้าแรก', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B9%81%E0%B8%A3%E0%B8%81', NULL, NULL, NULL, 'th'),
(2, 'article', 1, 'สวัสดี', '%E0%B8%AA%E0%B8%A7%E0%B8%B1%E0%B8%AA%E0%B8%94%E0%B8%B5', NULL, NULL, NULL, 'th'),
(3, 'category', 2, 'Home', 'Home', NULL, NULL, NULL, 'en'),
(4, 'article', 2, 'Hello', 'Hello', NULL, NULL, NULL, 'en'),
(5, 'page', 3, 'หน้าตัวอย่าง', '%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B8%95%E0%B8%B1%E0%B8%A7%E0%B8%AD%E0%B8%A2%E0%B9%88%E0%B8%B2%E0%B8%87', NULL, NULL, NULL, 'th'),
(6, 'page', 4, 'Sample-page', 'Sample-page', NULL, NULL, NULL, 'en');
