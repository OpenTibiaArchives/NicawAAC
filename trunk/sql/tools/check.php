<?
$account = new Account($_SESSION['account']);
if (in_array($_SESSION['account'],$cfg['admin_accounts']) && $account->exists() || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' && $cfg['admin_local']){
		//pass =)
}else   //no pass
	die('Please <a href="login.php?redirect=admin.php&logout=true">login</a> to your admin account');
?>