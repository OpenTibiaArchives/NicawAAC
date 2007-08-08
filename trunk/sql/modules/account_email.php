<?
include ("../include.inc.php");
$_SESSION['last_activity']=time();
//load account if loged in
$account = new Account($_SESSION['account']);
($account->load()) or die('You need to login first. '.$account->getError());

//retrieve post data
$form = new Form('email');
//check if any data was submited
if ($form->exists()){
	//validate email
	if (eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$",$form->attrs['email'])){
		//check if password match
		if ($account->checkPassword($form->attrs['password'])){
			$account->logAction($account->getAttr('email').' changed to '.$form->attrs['email']);
			$account->setAttr('email',$form->attrs['email']);
			if ($account->save()){
				//create new message
				$msg = new IOBox('message');
				$msg->addMsg('Email was successfuly changed.');
				$msg->addClose('Finish');
				$msg->show();
			}else $error = 'Failed saving account';
		}else{$error = "Incorrect password";}
	}else{$error = "Bad email address";}
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
	$form = new IOBox('email');
	$form->target = $_SERVER['PHP_SELF'];
	$form->addLabel('Change Email');
	$form->addInput('password','password');
	$form->addInput('email','text',$account->getAttr('email'));
	$form->addClose('Cancel');
	$form->addSubmit('Next >>');
	$form->show();
}
?>