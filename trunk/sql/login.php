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
include ("include.inc.php");

########################## LOGIN ############################
if (isset($_POST['login_submit'])){
	$account = new Account();
	if ($account->load($_POST['account'])){
		if ($account->checkPassword($_POST['password'])){
			$_SESSION['account']=$account->getAttr('accno');
			$_SESSION['remote_ip']=$_SERVER['REMOTE_ADDR'];
			$_SESSION['last_activity']=time();
			if (!empty($_GET['redirect'])) {
				header('location: '.$_GET['redirect']);
				die('Redirecting to <a href="'.$_GET['redirect'].'>'.$_GET['redirect'].'</a>');
			}
		}else{$error = 'Account and password don\'t match.';}
	}else{$error = 'Account and password don\'t match. ';}
}

########################## LOGOUT ###########################
elseif (isset($_GET['logout'])){
	session_unset();
}
elseif (!empty($_SESSION['account']) && !empty($_GET['redirect'])){
	header('location: '.$_GET['redirect']);
	die('Redirecting to <a href="'.$_GET['redirect'].'>'.$_GET['redirect'].'</a>');
}
########################## LOGIN FORM #######################
$ptitle="Account - $cfg[server_name]";
include ("header.inc.php");
?>
<div id="content">
<div class="top">Account</div>
<div class="mid">
<fieldset>
<legend><b>Account Login</b></legend>
<form id="login_form" action="login.php?redirect=<?php echo htmlspecialchars($_GET['redirect'])?>" method="post">
<table>
<tr><td style="text-align: right"><label for="account">Account:</label></td>
<td><input id="account" name="account" type="password" class="textfield" maxlength="8" size="10"/></td></tr>
<tr><td style="text-align: right"><label for="password">Password:</label></td>
<td><input id="password" name="password" type="password" class="textfield" maxlength="30" size="10"/></td></tr>
<tr><td></td><td><input type="submit" name="login_submit" value="Sign in"/></td></tr>
</table>
</form>
</fieldset>
<fieldset>
<legend>More Options</legend>
<ul class="task-menu" style="width: 200px;">
<li onclick="ajax('form','modules/account_create.php','',true)" style="background-image: url(ico/vcard_add.png);">New Account</li>
<?php if($cfg['Email_Recovery']){?><li onclick="ajax('form','modules/account_recover.php','',true)" style="background-image: url(ico/arrow_redo.png);">Recover Account</li><?php }?>
</ul>
</fieldset>
</div>
<div class="bot"></div>
</div>
<?php include ("footer.inc.php");?>