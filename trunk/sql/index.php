<?
include('config.inc.php');
if (empty($cfg['start_page'])){
	header('location: setup/1.php');
}else{
	header('location: '.$cfg['start_page']);
}
?>