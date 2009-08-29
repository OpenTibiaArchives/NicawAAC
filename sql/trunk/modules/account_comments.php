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

try {
    $account = new Account();
    $account->load($_SESSION['account']);

    $form = new Form('comments');

    $account->setAttr('comment',$form->attrs['comment']);
    $account->save();
  
} catch (FormNotFoundException $e)  {
    //create new form
    $form = new IOBox('comments');
    $form->target = $_SERVER['PHP_SELF'];
    $form->addLabel('Edit Comments');
    $form->addTextbox('comment',htmlspecialchars($account->attrs['comment']));
    $form->addClose('Cancel');
    $form->addSubmit('Save');
    $form->show();

} catch (AccountNotFoundException $e)  {
    $msg = new IOBox('message');
    $msg->addMsg('There was a problem loading your account. Try to login again.');
    $msg->addRefresh('OK');
    $msg->show();
    
}
?>
