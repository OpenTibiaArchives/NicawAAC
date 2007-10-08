<?
include ("../include.inc.php");
$_SESSION['last_activity']=time();
//load account if loged in
$account = new Account($_SESSION['account']);
if ($account->load())
	if ($account->canVote((int) $_POST['option'])){
		$account->vote((int) $_POST['option']);
		$message = 'You vote has been registered. Please vote only once.';
	}else $message = 'You cannot vote in this poll';
else $message = 'You are not logged in';

//create new message
$msg = new IOBox('message');
$msg->addMsg($message);
$msg->addClose('OK');
$msg->show();

?>