<?
//page generation time
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;

error_reporting(E_ALL ^ E_NOTICE);
session_start();

require ('config.inc.php');
require ('class/globals.php');
require ('class/sql.php');
require ('class/account.php');
require ('class/player.php');
require ('class/iobox.php');

//set custom exception handler
set_exception_handler('exception_handler');

/*Checking if IP not banned.
In fact, this can be done with .htaccess,
but noobs just love this function =] */
if (file_exists('banned.inc')){
$banned_ips = file ('banned.inc');
foreach ($banned_ips as $ip){
	if ($ip == $_SERVER['REMOTE_ADDR']){
		die("Sorry, your IP is banned from the website."); 
		//ha ha ha. die die die. My favourite PHP function :)
	}
}
}

//just make sure GD extension is loaded before using CAPTCHA
$cfg['use_captha'] = $cfg['use_captha'] && extension_loaded('gd');

//store server URL in variable for redirecting
if ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443)
	$cfg['server_url'] = $_SERVER['SERVER_NAME'];
else
	$cfg['server_url'] = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
$cfg['server_href'] = 'http://'.$cfg['server_url'].dirname(htmlspecialchars($_SERVER['PHP_SELF'])).'/';

//disable magic_quotes_gpc.
if( get_magic_quotes_gpc() )
{
  $_POST = array_map('stripslashes', $_POST);
  $_GET = array_map('stripslashes', $_GET);
  $_COOKIE = array_map('stripslashes', $_COOKIE);
  $_REQUEST = array_map('stripslashes', $_REQUEST);
}

//Anti session hijacking
if ($cfg['secure_session'] && !empty($_SESSION['account']) && ($_SERVER['REMOTE_ADDR'] != $_SESSION['remote_ip'] || time() - $_SESSION['last_activity'] > 30*60))
	unset($_SESSION['account']);

//Check for correct PHP version
if (!version_compare(phpversion(), "5.1.4", ">=") )
	throw new Exception('There are known issues with this PHP version. Please update your sofware, try to get at least PHP 5.2.x');

//Check if extensions loaded
if (!extension_loaded('simplexml'))
	throw new Exception('SimpleXML extension is not installed');
if (!extension_loaded('pdo'))
	throw new Exception('PDO <i>(PHP Data Objects)</i> is not installed in php.ini');

//Set AAC version
$cfg['aac_version'] = 'sql_3.11';
?>