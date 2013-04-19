<?php
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * @version 1.0
 * @since file available since version 1.0
 *
 */

// current Agni CMS version. use for compare with auto update.
$config['agni_version'] = '0.9';

// enable auto update. recommended setting to 'true' for use auto update, but if you want manual update (core hacking or custom modification through core files) set to false.
$config['angi_auto_update'] = true;

// theme path refer from base path.
$config['agni_theme_path'] = 'public/themes/';

// upload path refer from base path.
$config['agni_upload_path'] = 'public/upload/';

// plugins path refer from base path. (plugins or 'module plug' are same but 'module plug' and 'module' different in working process.)
$config['agni_plugins_path'] = 'modules/';