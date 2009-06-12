<?php
/*
    Copyright (C) 2007 - 2009  Nicaw

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
include ("../include.inc.php");

//retrieve post data
$form = new Form('newaccount');
//check if any data was submited
if ($form->exists()){
	$errors = array();
	
	$_SESSION['_FORM_FEED_name'] = $form->attrs['name'];
	$_SESSION['_FORM_FEED_email'] = $form->attrs['email'];
	$_SESSION['_FORM_FEED_pass'] = $form->attrs['password'];
	
	//image verification
	if (!$form->validated()){
		$errors[] = 'failed image validation';
	}
	
	//email formating rules
	if (!AAC::ValidEmail($form->attrs['email'])){
		$errors[] = 'not a valid email address';
		unset($_SESSION['_FORM_FEED_email']);
	}
	
	//account name formating rules
	if (!AAC::ValidAccountName($form->attrs['name'])){
		$errors[] = 'not a valid account name';
		unset($_SESSION['_FORM_FEED_name']);
	}else{
		//check for existing name
		$account = new Account();
		$account->setAttr('name', strtolower($form->attrs['name']));
		if($account->existsName()){
			$errors[] = 'account name is already used';
			unset($_SESSION['_FORM_FEED_name']);
		}
	}
	
	//password formating rules
	if (!AAC::ValidPassword($form->attrs['password'])){
		$errors[] = 'not a valid password';
		unset($_SESSION['_FORM_FEED_pass']);
	}elseif ($form->attrs['password'] != $form->attrs['confirm']){
		$errors[] = 'passwords do not match';
		unset($_SESSION['_FORM_FEED_pass']);
	}
		
	if (count($errors) > 0){
		//create new message
		$msg = new IOBox('message');
		$errText = 'The following error(s) occurred:<br/><ul>';
		foreach($errors as $error) $errText.= '<li>'.ucfirst($error).'</li>';
		$errText.= '</ul>';
		$msg->addMsg($errText);
		$msg->addReload('<< Back');
		$msg->addClose('OK');
		$msg->show();
	}elseif (count($errors) == 0){

		//set account atrributes
		$accno = $account->getAttr('name');
		$account->setPassword($form->attrs['password']);
		$account->setAttr('email',$form->attrs['email']);
		//create the account
		$account->save();

		if ($cfg['Email_Validate']){
			$body = "Here is your login information for <a href=\"http://$cfg[server_url]/\">$cfg[server_name]</a><br/>
<b>Account name:</b> $accno<br/>
<b>Password:</b> $password<br/>
<br/>
Powered by <a href=\"http://nicaw.net/\">Nicaw AAC</a>";
			//send the email
			require_once("../extensions/class.phpmailer.php");

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->IsHTML(true);				
			$mail->Host = $cfg['SMTP_Host'];
			$mail->Port = $cfg['SMTP_Port'];
			$mail->SMTPAuth = $cfg['SMTP_Auth'];
			$mail->Username = $cfg['SMTP_User'];
			$mail->Password = $cfg['SMTP_Password'];

			$mail->From = $cfg['SMTP_From'];
			$mail->AddAddress($form->attrs['email']);

			$mail->Subject = $cfg['server_name'].' - Login Details';
			$mail->Body    = $body;

			if ($mail->Send()){
					//create new message
					$msg = new IOBox('message');
					$msg->addMsg('Your login details were emailed to '.$form->attrs['email']);
					$msg->addClose('Finish');
					$msg->show();
				}else
					$error = "Mailer Error: " . $mail->ErrorInfo;
		}else{
			//create new message
			$msg = new IOBox('message');
			$msg->addMsg('Great success!<br/>You can now login into your account and start creating characters.');
			$msg->addClose('Finish');
			$msg->show();
			$account->logAction('Created');
			
			unset($_SESSION['_FORM_FEED_name']);
			unset($_SESSION['_FORM_FEED_email']);
			unset($_SESSION['_FORM_FEED_pass']);
		}   
	}
}else{
	isset($_SESSION['_FORM_FEED_name']) || $_SESSION['_FORM_FEED_name'] = '';
	isset($_SESSION['_FORM_FEED_email']) || $_SESSION['_FORM_FEED_email'] = '';
	isset($_SESSION['_FORM_FEED_pass']) || $_SESSION['_FORM_FEED_pass'] = '';
	//create new form
	$form = new IOBox('newaccount');
	$form->target = $_SERVER['PHP_SELF'];
	$form->addLabel('Create Account');
	$form->addInput('name','text',$_SESSION['_FORM_FEED_name'],100,false,'Account name is at least 6 characters long and consists of letters A-Z, numbers 0-9 and underscores _');
	$form->addInput('email','text',$_SESSION['_FORM_FEED_email'],100,false,'Please enter a valid email. It can be used to recover your account.');
	$form->addInput('password','password',$_SESSION['_FORM_FEED_pass'],100,false,'Your password can consist of letters A-Z, numbers 0-9 and symbols ~!@#%&;,:\^$.|?*+()<br/>Never use the same password as in your email account.');
	$form->addInput('confirm','password',$_SESSION['_FORM_FEED_pass'],100,false,'Retype your password.');
	$form->addCaptcha();
	$form->addClose('Cancel');
	$form->addSubmit('Next >>');
	$form->show();
}?>