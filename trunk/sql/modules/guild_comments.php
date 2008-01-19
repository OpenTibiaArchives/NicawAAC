<?php
/*
    Copyright (C) 2007 - 2008  Nicaw

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
$form = new Form('comments');
//check if any data was submited
if ($form->exists()){
	//get guild owner acc
	$guild = new Guild();
	//check if user is guild owner
	if ($guild->load($_REQUEST['guild_id']) && $guild->getAttr('owner_acc') == $_SESSION['account'] && !empty($_SESSION['account']) && strlen($form->attrs['comment']) <= 300)
		file_put_contents('../guilds/'.$guild->getAttr('id').'.txt',htmlspecialchars($form->attrs['comment']));
	else{
		$msg = new IOBox('comments');
		$msg->addMsg('Cannot load this guild');
		$msg->show();
	}
}else{
	//create new form
	$form = new IOBox('comments');
	$form->target = $_SERVER['PHP_SELF'].'?guild_id='.(int)$_REQUEST['guild_id'];
	$form->addLabel('Edit Description');
	$form->addMsg('Max 300 symbols');
	$form->addTextbox('comment',@file_get_contents('../guilds/'.(int)$_REQUEST['guild_id'].'.txt'));
	$form->addClose('Cancel');
	$form->addSubmit('Save');
	$form->show();
}
?>
