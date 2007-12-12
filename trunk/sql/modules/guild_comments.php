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
$_SESSION['last_activity']=time();

//retrieve post data
$form = new Form('comments');
//check if any data was submited
if ($form->exists()){
	$gid = (int) $_REQUEST['gid'];
	//get guild owner acc
	$SQL = new SQL();
	$query = 'SELECT players.account_id, guilds.name FROM players, guilds WHERE guilds.ownerid = players.id AND guilds.id = '.$SQL->quote($gid);
	$SQL->myQuery($query);
	$result = $SQL->fetch_array();
	$owner = (int) $result['account_id'];
	//check if user is guild owner
	if ($owner == $_SESSION['account'] && !empty($_SESSION['account']) && strlen($form->attrs['comment']) <= 300)
		file_put_contents('../guilds/'.$gid.'.txt',htmlspecialchars($form->attrs['comment']));
}else{
	$gid = (int) $_REQUEST['gid'];
	//create new form
	$form = new IOBox('comments');
	$form->target = $_SERVER['PHP_SELF'].'?gid='.$gid;
	$form->addLabel('Edit Description');
	$form->addMsg('Max 300 symbols');
	$form->addTextbox('comment',@file_get_contents('../guilds/'.$gid.'.txt'));
	$form->addClose('Cancel');
	$form->addSubmit('Save');
	$form->show();
}
?>
