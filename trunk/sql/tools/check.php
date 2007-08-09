<?
$account = new Account($_SESSION['account']);
(in_array($_SESSION['account'],$cfg['admin_accounts']) || ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' && $cfg['admin_local'])) or die();
?>