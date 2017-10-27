<?php
/******************************************************
from local-settings.php
 *******************************************************/

// 言語設定（日本語固定）
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// デフォルト値（不要かも）
$defs = array();

// 環境変数より
$env_ids = array(
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
    'GOTEO_MAIL',
    'GOTEO_CONTACT_MAIL',
    'GOTEO_FAIL_MAIL',
    'GOTEO_LOG_MAIL',
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
    // Cron params (for cron processes using wget)
    'CRON_PARAM',
    'CRON_VALUE',
    /*
     * Social Services constants  (needed to login-with on the controller/user and library/oauth)
     */
    // twitter api key
    'OAUTH_TWITTER_ID',
    'OAUTH_TWITTER_SECRET',
    // Credentials Facebook app
    'OAUTH_FACEBOOK_ID',
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
    'LG_OMNICONFIG_JSON_FILE',
    // S3 static
    'STATIC_S3_VERSION',
    'STATIC_S3_REGION',
    'STATIC_S3_BUCKET_NAME',
    // epsilon code
    'EPSILON_CONTRACT_CODE'
);

foreach ($env_ids as $env_id){
    if (getenv($env_id)){
        define($env_id, getenv($env_id));
    } elseif ($defs[$env_id]){
        define($env_id, $defs[$env_id]);
    } else {
        define($env_id, '');
    }
}

$apikeys = json_decode(file_get_contents( '/var/www/html/omniconfig/apikeys.json'));
define('GOTEO_META_TITLE', $apikeys->meta->appName->name);
define('GOTEO_META_AUTHOR', $apikeys->meta->appName->name);
define('LG_PLACE_NAME', $apikeys->meta->appName->es);



//iPad, AndroidタブレットはPCビュー
//if(preg_match('/iPod|iPhone|Android.+Mobile/i', $ua) || strpos($ua, 'LocalGood/iOS (Yokohama) ') === 0 || strpos($ua, 'LocalGood/Android (Yokohama) ') === 0 ) {
if(isset($_SERVER['HTTP_USER_AGENT'])){
    $ua = $_SERVER['HTTP_USER_AGENT'];
}else{
    $ua = null;
}
//if(isset($_SERVER['HTTP_X_LOCALGOOD_UA']) && in_array($_SERVER['HTTP_X_LOCALGOOD_UA'], array('iOS', 'Android')) || strpos($ua, 'LocalGood/iOS (Yokohama)') === 0 || strpos($ua, 'LocalGood/Android (Yokohama)') === 0 ) {
if( !empty($ua) && ( preg_match('/iPod|iPhone/i', $_SERVER['HTTP_USER_AGENT']) === 1 || preg_match('/Android.+Mobile/i', $_SERVER['HTTP_USER_AGENT']) === 1 ) ){
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

//Uploads static files
// define('STATIC_SVR_DOMAIN','http://static.staging.localgood.jp');
$static_svr = '';
if (getenv('STATIC_SVR_DOMAIN')){
    $static_svr = getenv('STATIC_SVR_DOMAIN');
} else {
    $static_svr = constant('SRC_URL');
}
define('STATIC_SVR_DOMAIN',$static_svr);
$static_dir = str_replace(constant('SRC_URL'), STATIC_SVR_DOMAIN, dirname(__FILE__) );
//define('GOTEO_DATA_PATH', $static_dir . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
define('GOTEO_DATA_PATH', 'goteo/data' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);

// オーダー情報送信先URL																	// 本番環境
define('EPSILON_ORDER_URL', "https://secure.epsilon.jp/cgi-bin/order/receive_order3.cgi");
// 実売上処理URL
define('EPSILON_SALSED_URL', "https://secure.epsilon.jp/cgi-bin/order/sales_payment.cgi");

define('EPSILONON', 1);		// イプシロンをカードで利用
define('EPSILONCONVENI', 1);	// イプシロンでコンビニ決済を利用


//default
define("MAIL_HANDLER", "phpmailer");
define('GOTEO_DB_DRIVER', 'mysql');
define('GOTEO_MAIL_QUOTA', 50000);
define('GOTEO_MAIL_SENDER_QUOTA', round(GOTEO_MAIL_QUOTA * 0.8));
define("SESSION_HANDLER", "php");   // def
define("FILE_HANDLER", "file");     // def
define("LOG_HANDLER", "file");      // def
define("GOTEO_ENV", "local");       // def

define('GOTEO_MAIL_TYPE', 'mail'); // mail, sendmail or smtp
define('GOTEO_MAIL_SMTP_AUTH', true);
define('GOTEO_MAIL_SMTP_SECURE', 'ssl');
define('GOTEO_MAIL_SMTP_HOST', 'smtp--host');
define('GOTEO_MAIL_SMTP_PORT', '--portnumber--');
define('GOTEO_MAIL_SMTP_USERNAME', 'smtp-usermail');
define('GOTEO_MAIL_SMTP_PASSWORD', 'smtp-password');

//bucket para logs (if you set LOG_HANDLER to s3)
define("AWS_S3_LOG_BUCKET", "bucket");
define("AWS_S3_LOG_PREFIX", "applogs/");

//Amazon Web Services Credentials
define("AWS_KEY", "--------------");
define("AWS_SECRET", "----------------------------------");
define("AWS_REGION", "-----------");

//S3 bucket (if you set FILE_HANDLER to s3)
define("AWS_S3_BUCKET", "static.example.com");
define("AWS_S3_PREFIX", "");

define('AWS_SNS_CLIENT_ID', 'XXXXXXXXX');
define('AWS_SNS_REGION', 'us-east-1');
define('AWS_SNS_BOUNCES_TOPIC', 'amazon-ses-bounces');
define('AWS_SNS_COMPLAINTS_TOPIC', 'amazon-ses-complaints');

// Credentials Linkedin app
define('OAUTH_LINKEDIN_ID', '-----------------------------------'); //
define('OAUTH_LINKEDIN_SECRET', '-----------------------------------'); //

// Un secreto inventado cualquiera para encriptar los emails que sirven de secreto en openid
define('OAUTH_OPENID_SECRET','-----------------------------------');

define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/jp/cgi-bin/webscr?cmd=');
define('PAYPAL_DEVELOPER_PORTAL', 'goteo.il3c');
define('PAYPAL_DEVICE_ID', 'goteo.il3c.com');
define('PAYPAL_APPLICATION_ID', 'APP-80W284485P519543T');
define('PAYPAL_BUSINESS_ACCOUNT', 'info+merchant@info-lounge.jp');
define('PAYPAL_IP_ADDRESS', '127.0.0.1');

define('TPV_MERCHANT_CODE', 'xxxxxxxxx');
define('TPV_REDIRECT_URL', '--bank-rest-api-url--');
define('TPV_ENCRYPT_KEY', 'xxxxxxxxx');

define('RECAPTCHA_PUBLIC_KEY','-----------------------------------');
define('RECAPTCHA_PRIVATE_KEY','-----------------------------------');

define('GOTEO_GETTEXT_BYPASS_CACHING', true);
define('GOTEO_GETTEXT_DOMAIN', 'messages');
define('GOTEO_DEFAULT_LANG', 'en');

?>
