<?php
/*
    Copyright (C) 2007  Nicaw

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
	//image verification
	if ($form->validated()){
		//email formating rules
		if (eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$",$form->attrs['email'])){
			$account = new Account();
			$account->setAttr('accno', (int)$form->attrs['number']);
			if (AAC::ValidAccountNumber($form->attrs['number']) && !$account->exists()){
				//set account atrributes
				$accno = $account->getAttr('accno');
				if ($form->attrs['password'] == $form->attrs['confirm'] && AAC::ValidPassword($form->attrs['password']))
					$password = $form->attrs['password'];
				else
					$password = substr(str_shuffle('qwertyuipasdfhjklzxcvbnm123456789'), 0, 6);
				$account->setPassword($password);
				$account->setAttr('email',$form->attrs['email']);
				//create the account
				$account->save();

				if ($cfg['Email_Validate']){
				$body = "Here is your login information for <a href=\"http://$cfg[server_url]/\">$cfg[server_name]</a><br/>
<b>Account number:</b> $accno<br/>
<b>Password:</b> $password<br/>
<br/>
Powered by <a href=\"http://nicaw.net/\">Nicaw AAC</a>";
				//send the email
				require("../phpmailer/class.phpmailer.php");

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

				if ($mail->Send())
						$success = 'Your login details were emailed to '.$form->attrs['email'];
					else
						$error = "Mailer Error: " . $mail->ErrorInfo;
						
				}else{
					$success ='Please write down your login information:<br/>
Account number: <b>'.$accno.'</b><br/>
Password: <b>'.$password.'</b><br/>
You can now login into your account and start creating characters.<br/>';
						$account->logAction('Created');
				}
			}else $error = 'Invalid account number';
		}else $error = 'Invalid email address';
	}else $error = 'Image verification failed';
	if (!empty($error)){
		//create new message
		$msg = new IOBox('message');
		$msg->addMsg($error);
		$msg->addReload('<< Back');
		$msg->addClose('OK');
		$msg->show();
	}elseif (!empty($success)){
		//create new message
		$msg = new IOBox('message');
		$msg->addMsg($success);
		$msg->addClose('Finish');
		$msg->show();
	}
}else{
	//create new form
	$form = new IOBox('newaccount');
	$form->target = $_SERVER['PHP_SELF'];
	$form->addLabel('Create Account');
	$form->addInput('number');
	if (!$cfg['Email_Validate']){
		$form->addInput('password','password');
		$form->addInput('confirm','password');
	}
	$form->addInput('email');
	$form->addCaptcha();
	$form->addClose('Cancel');
	$form->addSubmit('Next >>');
	$form->show();
}?>