<?php
	
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// LG Base Name
define('LG_PLACE_NAME','yokohama');
define('LG_PLACE_LABEL','横浜');

define('LG_SCSS_COMPILE_PARAM','hogehoge');

// Metadata
define('GOTEO_META_TITLE', 'LOCAL GOOD YOKOHAMA');
define('GOTEO_META_DESCRIPTION', 'LOCAL GOOD YOKOHAMAは地域に暮らす市民や企業、団体が地域のことに意識を持ち、できる範囲で、サービス、モノ、カネ、ヒト、情報の循環をつくっていくことを目指すウェブサイト。地域をよくする活動「地域のGOOD=ステキないいコト」に多くの主体が参加するきっかけをつくります。');
define('GOTEO_META_KEYWORDS', 'LOCAL GOOD YOKOHAMA,クラウドファンディング,ローカルグッドヨコハマ,コミュニティ,コミュニティ経済,横浜,地域,Goteo');
define('GOTEO_META_AUTHOR', 'LOCAL GOOD YOKOHAMA');
define('GOTEO_META_COPYRIGHT', 'COPYRIGHT&copy; LOCAL GOOD YOKOHAMA. Some rights reserved.');

//Mail management: ses (amazon), phpmailer (php library)
define("MAIL_HANDLER", "phpmailer");

// Database
define('GOTEO_DB_DRIVER', 'mysql');
define('GOTEO_DB_HOST', 'mysql-localgood-stg-cluster.cluster-cqbylmzae7hn.ap-northeast-1.rds.amazonaws.com');
//define('GOTEO_DB_HOST', 'localgood-staging.cqbylmzae7hn.ap-northeast-1.rds.amazonaws.com');
define('GOTEO_DB_PORT', 3306);
define('GOTEO_DB_CHARSET', 'UTF-8');
define('GOTEO_DB_SCHEMA', 'gt_lg-yokohama');
define('GOTEO_DB_USERNAME', 'gt_lg-yokohama');
//define('GOTEO_DB_PASSWORD', 'HX2BhTDL0dAe');
define('GOTEO_DB_PASSWORD', 'gt_lg-yokohama');

// LocalGood Common Authentication Database
define('COMMON_AUTH_DB_SCHEMA', 'gt_lg-common');

// Mail
define('GOTEO_MAIL_FROM', 'info@info-lounge.jp');
define('GOTEO_MAIL_NAME', 'LOCAL GOOD YOKOHAMA');
//define('GOTEO_MAIL_TYPE', 'smtp'); // mail, sendmail or smtp
define('GOTEO_MAIL_TYPE', 'mail'); // mail, sendmail or smtp
define('GOTEO_MAIL_SMTP_AUTH', true);
define('GOTEO_MAIL_SMTP_SECURE', 'ssl');
define('GOTEO_MAIL_SMTP_HOST', 'smtp--host');
define('GOTEO_MAIL_SMTP_PORT', '--portnumber--');
define('GOTEO_MAIL_SMTP_USERNAME', 'smtp-usermail');
define('GOTEO_MAIL_SMTP_PASSWORD', 'smtp-password');

define('GOTEO_MAIL', 'info@info-lounge.jp');
define('GOTEO_CONTACT_MAIL', 'info@info-lounge.jp');
define('GOTEO_FAIL_MAIL', 'info@info-lounge.jp');
define('GOTEO_LOG_MAIL', 'info@info-lounge.jp');

//Quota de envio m�ximo para goteo en 24 horas
define('GOTEO_MAIL_QUOTA', 50000);
//Quota de envio m�ximo para newsletters para goteo en 24 horas
define('GOTEO_MAIL_SENDER_QUOTA', round(GOTEO_MAIL_QUOTA * 0.8));
//clave de Amazon SNS para recopilar bounces automaticamente: 'arn:aws:sns:us-east-1:XXXXXXXXX:amazon-ses-bounces'
//la URL de informacion debe ser: goteo_url.tld/aws-sns.php

// Language
define('GOTEO_DEFAULT_LANG', 'ja');
// name of the gettext .po file (used for admin only texts at the moment)
define('GOTEO_GETTEXT_DOMAIN', 'messages');
// gettext files are cached, to reload a new one requires to restart Apache which is stupid (and annoying while 
//	developing) this setting tells the langueage code to bypass caching by using a clever file-renaming 
// mechanism described in http://blog.ghost3k.net/articles/php/11/gettext-caching-in-php
define('GOTEO_GETTEXT_BYPASS_CACHING', true);

/*
 *  LocalGood Server Environment
 */
// url
define('SITE_URL', 'https://cf.yokohama.staging.localgood.jp'); // endpoint url
define('SRC_URL', 'https://cf.yokohama.staging.localgood.jp');  // host for statics
define('SEC_URL', 'https://cf.yokohama.staging.localgood.jp');  // with SSL certified

define('LOCALGOOD_WP_BASE_URL', 'http://yokohama.staging.localgood.jp');

define('LOG_PATH', '/var/www/vhosts/cf.yokohama.staging.localgood.jp/htdocs');

define('LG_INTEGRATION_URL', 'http://localgood.jp');
define('LG_NAME', 'LOCAL GOOD YOKOHAMA');
define('LG_GOOGLE_PLUS', 'https://plus.google.com/112981975493826894716/posts');
define('LG_TWITTER', 'LogooYOKOHAMA');
define('LG_FACEBOOK_PAGE', 'https://www.facebook.com/LOCALGOODYOKOHAMA');

//define('STATIC_SVR_DOMAIN','https://static.staging.localgood.jp');
define('STATIC_SVR_DOMAIN','http://static.staging.localgood.jp.s3-website-ap-northeast-1.amazonaws.com');
//define('STATIC_S3_BUCKET_NAME',preg_replace('/https?\:\/\//','',STATIC_SVR_DOMAIN));
define('STATIC_S3_BUCKET_NAME','static.staging.localgood.jp');
define('STATIC_S3_VERSION','latest');
define('STATIC_S3_REGION','ap-northeast-1');

//$static_dir = str_replace(constant('SRC_URL'), STATIC_SVR_DOMAIN, dirname(__FILE__) );
//$static_dir = preg_replace('/\/[A-Za-z0-9.]+\.localgood/','/static.staging.localgood',dirname(__FILE__));
// todo: 環境変数->定数にする
$static_dir = '/var/www/vhosts/static.staging.localgood.jp/htdocs';
//define('GOTEO_DATA_PATH', $static_dir . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
define('GOTEO_DATA_PATH',  'goteo/data' . DIRECTORY_SEPARATOR);

//Sessions
//session handler: php, dynamodb
define("SESSION_HANDLER", "php");

//Files management: s3, file
define("FILE_HANDLER", "file");

//Log file management: s3, file
define("LOG_HANDLER", "file");

// environment: local, beta, real
define("GOTEO_ENV", "local");

// Cron params (for cron processes using wget)
define('CRON_PARAM', '3zwjrxy5h2d4');
define('CRON_VALUE', 'n76autgfinru');

/*
Any other payment system configuration should be setted here
*/

/****************************************************
Social Services constants  (needed to login-with on the controller/user and library/oauth)
****************************************************/
// Credentials Facebook app
//define('OAUTH_FACEBOOK_ID', '1504174723161858'); //
define('OAUTH_FACEBOOK_ID', '1011747602194158'); //
//define('OAUTH_FACEBOOK_SECRET', '63c5a49cce8c91b9805747446be617a5'); //
define('OAUTH_FACEBOOK_SECRET', 'bde48c526092181f7a004130a5e63dbd'); //

// Credentials Twitter app
define('OAUTH_TWITTER_ID', 'dnh1OR5qbdCd64ViF4ncg07ht'); //
define('OAUTH_TWITTER_SECRET', 'EvX0gf2WCCUXyHFPboER7vTGdhDLXPTQDzi2Z6OzDTZZ54oXQq'); //

//define('OAUTH_TWITTER_DUMMY_ID', '01234567890123Y'); //

// Credentials Linkedin app
define('OAUTH_LINKEDIN_ID', '-----------------------------------'); //
define('OAUTH_LINKEDIN_SECRET', '-----------------------------------'); //

// Un secreto inventado cualquiera para encriptar los emails que sirven de secreto en openid
define('OAUTH_OPENID_SECRET','-----------------------------------');

//SNS link
//define('LG_FACEBOOK_PAGE', 'https://www.facebook.com/LOCALGOODYOKOHAMA');
//define('LG_TWITTER', 'https://twitter.com/LogooYOKOHAMA');
//define('LG_GOOGLE_PLUS', 'https://plus.google.com/112981975493826894716/');

// recaptcha ( to be used in /contact form )
define('RECAPTCHA_PUBLIC_KEY','-----------------------------------');
define('RECAPTCHA_PRIVATE_KEY','-----------------------------------');

/****************************************************
Google Analytics
****************************************************/
define('GOTEO_ANALYTICS_TRACKER', "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51541652-2', 'auto');
  ga('send', 'pageview');
</script>");

/****************************************************
AWS
****************************************************/
// Credentials SES
define('AWS_SES_SOURCE', 'localgood@yokohamalab.jp');
define('AWS_SES_ACCESS', 'AKIAJSTOTE7ZV3Q3GJ6A');
define('AWS_SES_SECERET', 'EX/bYr/4W6OrReHzBriIL9gLRW6WFAuLfJs1lStN');
define('AWS_SES_CHARSET', 'UTF-8');

/****************************************************
AXES
 ****************************************************/
define('AXES_CLIENTIP', '1011003702');

/****************************************************
CESIUM
 ****************************************************/
define('LG_EARTHVIEW', 'http://map.yokohama.localgood.jp/');

/****************************************************
Skillmatching
 ****************************************************/
define('LG_SM_DB_PREFIX', 'skillmatching_');

/****************************************************
Change view type
 ****************************************************/
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
if( !empty($ua) && ( preg_match('/iPod|iPhone/i', $_SERVER['HTTP_USER_AGENT']) === 1 || preg_match('/Android.+Mobile/i', $_SERVER['HTTP_USER_AGENT']) === 1 ) ) {
    define('PC_VIEW', false);
    define('VIEW_PATH', 'view/m');
} else {
    define('PC_VIEW', true);
    define('VIEW_PATH', 'view');
}

















#
#	追加した項目
#

putenv('GOTEO_LOCATION_NAMES=横浜市,中区,保土ケ谷区,南区,戸塚区,旭区,栄区,泉区,港北区,港南区,瀬谷区,磯子区,神奈川区,緑区,西区,都筑区,金沢区,青葉区,鶴見区');

/****************************************************
EPSILON
 ****************************************************/
define('EPSILON_CLIENTIP', '219.117.226.221');		// いらないなー


// オーダー情報送信先URL																	 // テスト環境
//define('EPSILON_ORDER_URL', "https://beta.epsilon.jp/cgi-bin/order/receive_order3.cgi");
// 実売上処理URL
//define('EPSILON_SALSED_URL', "https://beta.epsilon.jp/cgi-bin/order/sales_payment.cgi");

// オーダー情報送信先URL																	// 本番環境
define('EPSILON_ORDER_URL', "https://secure.epsilon.jp/cgi-bin/order/receive_order3.cgi");
// 実売上処理URL
define('EPSILON_SALSED_URL', "https://secure.epsilon.jp/cgi-bin/order/sales_payment.cgi");



define('EPSILON_CONTRACT_CODE', '65374640');		// イプシロン契約番号


define('DEBUGTEST', 1);		// デバッグ用


#define('AXESON', 1);		// AXES をカードで利用

define('EPSILONON', 1);		// イプシロンをカードで利用
define('EPSILONCONVENI', 1);	// イプシロンでコンビニ決済を利用


?>
