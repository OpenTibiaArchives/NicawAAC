<?
include ("../include.inc.php");

$account = new Account($_SESSION['account']);
($account->load()) or die('You need to login first. '.$account->getError());
$_SESSION['last_activity']=time();

$logs = $account->getLogs(10);
	if ($logs !== false && count($logs) > 0){
		echo '<h2>Account Logs</h2>'."\n";
		echo '<table style="width:100%">'."\n";
		echo '<tr class="color0"><td><b>Date</b></td><td><b>IP address</b></td><td><b>Action</b></td></tr>'."\n";
		foreach ($logs as $log){
			$i++;
			echo '<tr '.getStyle($i).'><td>'.date("Y.m.d H:i",$log['date']).'</td><td>'.$log['ip'].'</td><td>'.$log['action'].'</td></tr>'."\n";
			}
		echo '</table>';
	}
?>