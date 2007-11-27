<?
die();
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
	//load player
	$player = new Player($form->attrs['character']);
	if ($player->load()){
		//check if player really belongs to account
		if ($player->getAttr('account') === $account->getAttr('accno')){
			$pos = $player->getAttr('spawn');
			if ($player->repair()){
				$account->logAction('Repaired character: '.$player->getAttr('name').', '.$pos['x'].' '.$pos['y'].' '.$pos['z']);
				//create new message
				$msg = new IOBox('message');
				$msg->addMsg($player->getAttr('name').' was repaired.');
				$msg->addClose('Finish');
				$msg->show();
			}else $error = $player->getError();
		}else $error ='Player does not belong to account';
	}else $error ='Unable to load player';
	if (!empty($error)){
		//create new message
		$msg = new IOBox('message');
		$msg->addMsg($error);
		$msg->addReload('<< Back');
		$msg->addClose('OK');
		$msg->show();
	}
}else{
	foreach ($account->players as $player)
		$list[] = $player->getAttr('name');
	//create new form
	$form = new IOBox('new_guild');
	$form->target = $_SERVER['PHP_SELF'];
	$form->addLabel('Create Guild');
	if (empty($list)){
		$form->addMsg('Your account does not have any characters.');
		$form->addClose('Close');
	}else{
		$form->addMsg('Select guild name and the owner. Must have at least level '.$cfg['guild_leader_level']);
		$form->addInput('guild_name');
		$form->addSelect('leader',array_combine($list,$list));
		$form->addClose('Cancel');
		$form->addSubmit('Next >>');
	}
	$form->show();
}
?>