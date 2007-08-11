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
$form = new Form('search');
//check if any data was submited
if ($form->exists()){
	if (strlen($form->attrs['name']) > 1){
		//do mysql search
		$query =  'SELECT name FROM players WHERE `name` LIKE \'%'.$form->attrs['name'].'%\'';
		$mysql = new MySQL();
		$sql = $mysql->myQuery($query);
		if ($sql === false || $mysql->num_rows() == 0){
			//create new message
			$msg = new IOBox('message');
			$msg->addMsg('Nothing found.');
			$msg->addReload('<< Back');
			$msg->addClose('Close');
			$msg->show();
		}else{
			while ($a = $mysql->fetch_array($sql))
				$characters[] = $a['name'];
			//create new message
			$msg = new IOBox('admin');
			$msg->target = $form->attrs['script'];
			$msg->addMsg($mysql->num_rows().' character(s) found!');
			$msg->addSelect('list',array_combine($characters,$characters));
			$msg->addReload('<< Back');
			$msg->addClose('Cancel');
			$msg->addSubmit('Next >>');
			$msg->show();
		}
	}else{
		//create new message
		$msg = new IOBox('message');
		$msg->addMsg('Name must contain 2 characters at least.');
		$msg->addReload('<< Back');
		$msg->addClose('Close');
		$msg->show();
	}
}else{
	//create new form
	$form = new IOBox('search');
	$form->target = $_SERVER['PHP_SELF'];
	$form->addLabel('Find Character');
	$form->addInput('name');
	$form->addInput('script',$_POST['script']);
	$form->addClose('Cancel');
	$form->addSubmit('Next >>');
	$form->show();
}
?>