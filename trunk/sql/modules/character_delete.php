<?
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
//load account if loged in
$account = new Account($_SESSION['account']);
($account->load()) or die('You need to login first. '.$account->getError());
//retrieve post data
$form = new Form('delete');
//check if any data was submited
if ($form->exists()){
	//check for correct password
	if ($account->checkPassword($form->attrs['password'])){
		//load player
		$player = $account->player[$form->attrs['character']];
		if ($player->load()){
			//check if player really belongs to account
			if ($player->getAttr('account') === $account->getAttr('accno')){
				//delete the player
				if ($player->delete()){
					$account->logAction('Deleted character: '.$form->attrs['character']);
					//create new message
					$msg = new IOBox('message');
					$msg->addMsg('Your character was deleted.');
					$msg->addRefresh('Finish');
					$msg->show();
				}else $error = $player->getError();
			}else $error ='Player does not belong to account';
		}else $error ='Unable to load player';
	}else $error ='Wrong password';
	if (!empty($error)){
		//create new message
		$msg = new IOBox('message');
		$msg->addMsg($error);
		$msg->addReload('<< Back');
		$msg->addClose('OK');
		$msg->show();
	}
}else{
	$list = $account->getCharList();
	//create new form
	$form = new IOBox('delete');
	$form->target = $_SERVER['PHP_SELF'];
	$form->addLabel('Delete Character');
	if (empty($list)){
		$form->addMsg('Your account does not have any characters to delete.');
		$form->addClose('Close');
	}else{
		$form->addMsg('Which character do you want to delete?<br/>Enter your password to confirm.');
		$form->addSelect('character',array_combine($list,$list));
		$form->addInput('password','password');
		$form->addClose('Cancel');
		$form->addSubmit('Next >>');
	}
	$form->show();
}
?>