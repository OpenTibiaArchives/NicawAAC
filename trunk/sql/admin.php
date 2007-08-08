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
include ("include.inc.php");

$account = new Account($_SESSION['account']);
if (!($account->isAdmin())){
		$_SESSION['account'] = '';
		header('location: login.php?redirect=admin.php');
		die();
}
$ptitle="Admin Panel - $cfg[server_name]";
include ("header.inc.php");
?>
<div id="content">
<div class="top">.:Admin Panel:.</div>
<div class="mid">
<ul class="task-menu" style="margin: 10px">
<li onclick="ajax('form','tools/character_search.php','script=tools/character_delete.php',true)" style="display: inline; background-image: url(ico/key.png);">Delete</li>
<li onclick="ajax('form','modules/account_email.php','',true)" style="display: inline; background-image: url(ico/email.png);">Test</li>
<li onclick="ajax('form','modules/account_comments.php','',true)" style="display: inline; background-image: url(ico/page_edit.png);">Test</li>
<li onclick="ajax('ajax','modules/account_logs.php','',true)" style="display: inline; background-image: url(ico/book_open.png);">View Logs</li>
<li onclick="window.location.href='login.php?logout&amp;redirect=account.php'" style="display: inline; background-image: url(ico/resultset_previous.png);">Logout</li>
</ul>
<div id="ajax"></div>
<script language="javascript" type="text/javascript">
</script>
<?
$params = '?url='.$cfg['server_url'].'&version='.$cfg['aac_version'].'&remote_ip='.$_SERVER['REMOTE_ADDR'].'&server_ip='.$_SERVER['SERVER_ADDR'].'&port='.$_SERVER['SERVER_PORT'];
?>
<iframe width="100%" height="400px" src="http://aac.nicaw.net/<?=$params?>" ></iframe>
</div>
<div class="bot"></div>
</div>
<?
include ("footer.inc.php");
?>
