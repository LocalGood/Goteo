<?php

/*
 *  Copyright (C) 2012 Platoniq y Fundaci�n Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */

define('GOTEO_PATH', __DIR__ . DIRECTORY_SEPARATOR);
if (function_exists('ini_set')) {
    ini_set('include_path', GOTEO_PATH . PATH_SEPARATOR . '.');
} else {
    throw new Exception("No puedo a�adir la API GOTEO al include_path.");
}

// Nodo actual
define('GOTEO_NODE', 'goteo');

define('PEAR', GOTEO_PATH . 'library' . '/' . 'pear' . '/');
if (function_exists('ini_set')) {
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PEAR);
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . '/usr/local/lib/php/');
} else {
    throw new Exception("No puedo a�adir las librer�as PEAR al include_path.");
}

/******************************************************
PhpMailer constants
*******************************************************/
if (!defined('PHPMAILER_CLASS')) {
    define ('PHPMAILER_CLASS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.phpmailer.php');
}
if (!defined('PHPMAILER_LANGS')) {
    define ('PHPMAILER_LANGS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR);
}
if (!defined('PHPMAILER_SMTP')) {
    define ('PHPMAILER_SMTP', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.smtp.php');
}
if (!defined('PHPMAILER_POP3')) {
    define ('PHPMAILER_POP3', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.pop3.php');
}

/******************************************************
OAUTH APP's Secrets
*******************************************************/
if (!defined('OAUTH_LIBS')) {
    define ('OAUTH_LIBS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . 'SocialAuth.php');
}

if (file_exists('local-settings.php')) //en .gitignore
{
    require 'local-settings.php';
} else {
    die(<<<EOF
Localgood Goteo is not installed.<br />
Create local-setting.php like following:
<pre>
&lt;?php
/******************************************************
from local-settings.php
 *******************************************************/

// 言語設定（日本語固定）
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// 環境変数より
\$env_ids = array(
    // LG Base Name
    'LG_PLACE_NAME',
    // Metadata
    'GOTEO_META_TITLE',
    'GOTEO_META_AUTHOR',
    'GOTEO_META_COPYRIGHT',
    // Database
    'GOTEO_DB_HOST',
    'GOTEO_DB_PORT',
    'GOTEO_DB_CHARSET',
    'GOTEO_DB_SCHEMA',
    'GOTEO_DB_USERNAME',
    'GOTEO_DB_PASSWORD',
    // LocalGood Common Authentication Database
    'COMMON_AUTH_DB_SCHEMA',
    // Mail
    'GOTEO_MAIL_FROM',
    'GOTEO_MAIL_NAME',
    'GOTEO_MAIL_TYPE',
    'GOTEO_MAIL_SMTP_AUTH',
    'GOTEO_MAIL_SMTP_SECURE',
    'GOTEO_MAIL',
    'GOTEO_CONTACT_MAIL',
    'GOTEO_FAIL_MAIL',
    'GOTEO_LOG_MAIL',
    'GOTEO_MAIL_QUOTA',
    // Language
    'GOTEO_DEFAULT_LANG',
    // name of the gettext .po file (used for admin only texts at the moment)
    'GOTEO_GETTEXT_DOMAIN',
    // gettext files are cached, to reload a new one requires to restart Apache which is stupid (and annoying while
    //	developing) this setting tells the langueage code to bypass caching by using a clever file-renaming
    // mechanism described in http://blog.ghost3k.net/articles/php/11/gettext-caching-in-php
    'GOTEO_GETTEXT_BYPASS_CACHING',
    /*
     * LocalGood Server Environment
     */
    // url
    'LG_BASE_URL_GT', // endpoint url
    'SRC_URL',  // host for statics
    'LG_BASE_URL_GT',  // with SSL certified
    // Wordpress URL
    'LG_BASE_URL_WP',
    //
    'LG_TWITTER',
    // Cron params (for cron processes using wget)
    'CRON_PARAM',
    'CRON_VALUE',
    /*
     * Social Services constants  (needed to login-with on the controller/user and library/oauth)
     */
    // twitter api key
    'OAUTH_TWITTER_ID',
    'OAUTH_TWITTER_SECRET',
    'OAUTH_TWITTER_DUMMY_ID',
    // Credentials Facebook app
//    'OAUTH_FACEBOOK_ID',
    'OAUTH_FACEBOOK_SECRET',
    /*
     * Google Analytics
     */
    // ga tracking code
    'GOTEO_ANALYTICS_TRACKER',
    /*
     * AWS - Credentials SES
     */
    'AWS_SES_SOURCE',
    'AWS_SES_ACCESS',
    'AWS_SES_SECERET',
    'AWS_SES_CHARSET',
    /*
     *  SCSS Compiler
     */
    'LG_SCSS_COMPILE_PARAM',
    // WP generated json
    'LG_OMNICONFIG_JSON_FILE'
);

foreach (\$env_ids as \$env_id){
    if (getenv(\$env_id)){
        define(\$env_id, getenv(\$env_id));
    } elseif (\$defs[\$env_id]){
        define(\$env_id, \$defs[\$env_id]);
    } else {
        define(\$env_id, '');
    }
}

//Mail management: ses (amazon), phpmailer (php library)
define("MAIL_HANDLER", "phpmailer");    //def

// Database
define('GOTEO_DB_DRIVER', 'mysql');     //def

//Quota de envio m�ximo para newsletters para goteo en 24 horas
if (getenv('GOTEO_MAIL_QUOTA')){
    define('GOTEO_MAIL_SENDER_QUOTA', round(GOTEO_MAIL_QUOTA * 0.8));   // def
} else {
    define('GOTEO_MAIL_SENDER_QUOTA', round(50000 * 0.8));   // def
}

//Sessions
//session handler: php, dynamodb
define("SESSION_HANDLER", "php");   // def

//Files management: s3, file
define("FILE_HANDLER", "file");     // def

//Log file management: s3, file
define("LOG_HANDLER", "file");      // def

// environment: local, beta, real
define("GOTEO_ENV", "local");       // def


//iPad, AndroidタブレットはPCビュー
//if(preg_match('/iPod|iPhone|Android.+Mobile/i', \$ua) || strpos(\$ua, 'LocalGood/iOS (Yokohama) ') === 0 || strpos(\$ua, 'LocalGood/Android (Yokohama) ') === 0 ) {
if(isset(\$_SERVER['HTTP_USER_AGENT'])){
    \$ua = \$_SERVER['HTTP_USER_AGENT'];
}else{
    \$ua = null;
}
//if(isset(\$_SERVER['HTTP_X_LOCALGOOD_UA']) && in_array(\$_SERVER['HTTP_X_LOCALGOOD_UA'], array('iOS', 'Android')) || strpos(\$ua, 'LocalGood/iOS (Yokohama)') === 0 || strpos(\$ua, 'LocalGood/Android (Yokohama)') === 0 ) {
if( !empty(\$ua) && ( preg_match('/iPod|iPhone/i', \$_SERVER['HTTP_USER_AGENT']) === 1 || preg_match('/Android.+Mobile/i', \$_SERVER['HTTP_USER_AGENT']) === 1 ) ){
    define('PC_VIEW', false);       //def
    define('VIEW_PATH', 'view/m');  // def
} else {
    define('PC_VIEW', true);        // def
    define('VIEW_PATH', 'view');    // def
}

/****************************************************
Skillmatching
 ****************************************************/
define('LG_SM_DB_PREFIX', 'skillmatching_');    // def

//
/*
 *  omniconfig
 */
if (getenv('LG_OMNICONFIG_JSON_FILE')){
    \$_json = file_get_contents(LG_OMNICONFIG_JSON_FILE);
    if (\$_json){
        \$json = json_decode(\$_json);
        if (isset(\$json->facebook)){
            // facebook AppID
            define('OAUTH_FACEBOOK_ID',\$json->facebook);
            // google map api key
            // define('LG_GOOGLE_MAP_API_KEY',\$json->facebook);
        }
    }
}

//Uploads static files
// define('STATIC_SVR_DOMAIN','http://static.staging.localgood.jp');
\$static_svr = '';
if (getenv('STATIC_SVR_DOMAIN')){
    \$static_svr = getenv('STATIC_SVR_DOMAIN');
} else {
    \$static_svr = constant('SRC_URL');
}
define('STATIC_SVR_DOMAIN',\$static_svr);
\$static_dir = str_replace(constant('SRC_URL'), STATIC_SVR_DOMAIN, dirname(__FILE__) );
define('GOTEO_DATA_PATH', \$static_dir . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);

?&gt;
</pre>
EOF
);
}

if (file_exists('tmp-settings.php'))
    require 'tmp-settings.php';
else {
    // Temporary behaviours
    define('DEVGOTEO_LOCAL', false); // backwards compatibility
    define('GOTEO_MAINTENANCE', null); // to show the maintenance page
    define('GOTEO_EASY', null); // to take user overload easy
	define('GOTEO_FREE', true); // used somewhere...
}

$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
