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
//load account if loged in
$account = new Account($_SESSION['account']);
if ($account->load())
	if ($account->canVote((int) $_POST['option'])){
		$account->vote((int) $_POST['option']);
		$message = 'You vote has been registered. Please vote only once.';
	}else $message = 'You cannot vote in this poll';
else $message = 'You are not logged in';

//create new message
$msg = new IOBox('message');
$msg->addMsg($message);
$msg->addClose('OK');
$msg->show();

?>