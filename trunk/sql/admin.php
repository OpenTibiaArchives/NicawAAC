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
require ('tools/check.php');
$_SESSION['last_activity']=time();

$ptitle="Admin Panel - $cfg[server_name]";
include ("header.inc.php");
?>
<div id="content">
<div class="top">Admin Panel</div>
<div class="mid">
<ul class="task-menu" style="margin: 10px">
<li onclick="ajax('form','tools/news_create.php','',true)" style=" background-image: url(ico/rss_add.png);">Create News</li>
<li onclick="ajax('form','tools/poll_create.php','',true)" style=" background-image: url(ico/chart_bar_add.png);">Create Poll</li>
<li onclick="ajax('form','tools/character_search.php','script=tools/character_delete.php',true)" style=" background-image: url(ico/user_delete.png);">Delete Player</li>
<li onclick="ajax('form','tools/character_search.php','script=tools/character_group.php',true)" style=" background-image: url(ico/user_gray.png);">Create GM</li>
<li onclick="ajax('form','tools/group_create.php','',true)" style=" background-image: url(ico/group_add.png);">Create Group</li>
<li onclick="ajax('form','tools/ip_update.php','',true)" style=" background-image: url(ico/computer_link.png);">Update IP</li>
<li onclick="ajax('form','tools/table_repair.php','',true)" style=" background-image: url(ico/database_error.png);">Repair Tables</li>
<? if(!empty($_SESSION['account'])): ?>
<li onclick="window.location.href='login.php?logout&amp;redirect=account.php'" style=" background-image: url(ico/resultset_previous.png);">Logout</li>
<? endif; ?>
</ul>
<div id="ajax"></div>
<?
$ServerXML = simplexml_load_file('status.xml');
$params = htmlspecialchars('?url='.$cfg['server_url'].'&version='.$cfg['aac_version'].'&remote_ip='.$_SERVER['REMOTE_ADDR'].'&server_ip='.$_SERVER['SERVER_ADDR'].'&port='.$_SERVER['SERVER_PORT'].'&server_software='.urlencode($_SERVER['SERVER_SOFTWARE']).'&otserv_type='.$ServerXML->serverinfo['server'].$ServerXML->serverinfo['version']);
?>
<script language="javascript" type="text/javascript">
//<![CDATA[
if (Cookies.get('allow_iframe') == null){
	if (confirm('AAC will now contact external site and send your server details.\r\nNo personal information submited.\r\nIs that OK?')){
		Cookies.create('allow_iframe','yes',31);
	}else{
    Cookies.create('allow_iframe','no',31);
	}
}
if (Cookies.get('allow_iframe') == 'yes'){
	document.write('<iframe width="100%" height="400px" src="http://aac.nicaw.net/<?=$params?>" ></iframe>');
}
if (Cookies.get('allow_iframe') == 'no'){
  document.write('<span onclick="Cookies.erase(\'allow_iframe\'); location.reload(false);" style="cursor: pointer">Click here to enable iframe</span>');
}
//]]>
</script>
</div>
<div class="bot"></div>
</div>
<?
include ("footer.inc.php");
?>
