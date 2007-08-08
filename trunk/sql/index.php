<?
include('config.inc.php');
if (empty($cfg['start_page'])){
	die('Please choose $cfg[\'start_page\'] in config.inc.php');
}else{
	header('location: '.$cfg['start_page']);
}
?>