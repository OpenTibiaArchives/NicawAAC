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
include ('check.php');
$_SESSION['last_activity']=time();

//retrieve post data
$form = new Form('admin');
//check if any data was submited
if ($form->exists()){
  //check if confirmed
	if (isset($form->attrs['confirm'])){
		//load player
		$player = new Player($form->attrs['confirm']);
		if ($player->load()){
				//delete the player
				if ($player->delete()){
					//create new message
					$msg = new IOBox('message');
					$msg->addMsg('Character was deleted.');
					$msg->addClose('Finish');
					$msg->show();
				}else $error = $player->getError();
		}else $error ='Unable to load player';
		}else{
         //create new message
        $msg = new IOBox('admin');
        $msg->target = $_SERVER['PHP_SELF'];
        $msg->addMsg('Are you sure you want to delete the character?');
        $msg->addInput('confirm','text', $form->attrs['list']);
        $msg->addSubmit('Yes');
        $msg->addClose('No');
        $msg->show();
			}
	if (!empty($error)){
		//create new message
		$msg = new IOBox('message');
		$msg->addMsg($error);
		$msg->addReload('<< Back');
		$msg->addClose('OK');
		$msg->show();
	}
}else{
         //create new message
        $msg = new IOBox('admin');
        $msg->target = $_SERVER['PHP_SELF'];
        $msg->addMsg('Enter character name to delete');
        $msg->addInput('confirm','text');
        $msg->addSubmit('Delete');
        $msg->addClose('Cancel');
        $msg->show();
}
?>