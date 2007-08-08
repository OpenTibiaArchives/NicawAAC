<?
include ("../include.inc.php");
$_SESSION['last_activity']=time();
//load account if loged in
$account = new Account($_SESSION['account']);
($account->load()) or die('You need to login first. '.$account->getError());

//retrieve post data
$form = new Form('comments');
//check if any data was submited
if ($form->exists()){
	$account->setAttr('comment',$form->attrs['comment']);
	if (!$account->save()) $error = 'Failed saving comments';
		if (!empty($error)){
			//create new message
			$msg = new IOBox('message');
			$msg->addMsg($error);
			$msg->addReload('<< Back');
			$msg->addClose('OK');
			$msg->show();
		}
}else{
	//create new form
	$form = new IOBox('comments');
	$form->target = $_SERVER['PHP_SELF'];
	$form->addLabel('Edit Comments');
	$form->addTextbox('comment',htmlspecialchars($account->getAttr('comment')));
	$form->addClose('Cancel');
	$form->addSubmit('Save');
	$form->show();
}
?>
