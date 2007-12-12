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
$ptitle="Guilds - $cfg[server_name]";
include ("header.inc.php");
$SQL = new SQL();
?>
<div id="content">
<div class="top">Guilds</div>
<div class="mid">
<form method="get" action="guilds.php"> 
<input type="text" name="guild"/> 
<input type="submit" value="Search"/> 
</form>
<hr style="margin-top: 5px; margin-bottom: 5px; "/>
<?
//-----------------------Guild list
if (!isset($_GET['guild'])){
$query = 'SELECT guilds.id, guilds.name, COUNT(guilds.id) FROM guilds, guild_ranks, players WHERE guilds.id = guild_ranks.guild_id AND guild_ranks.id = players.rank_id GROUP BY guilds.id ORDER BY COUNT(guilds.id) DESC';
$SQL->myQuery($query);
if ($SQL->failed())
	throw new Exception('SQL query failed:<br/>'.$SQL->getError());
while ($a = $SQL->fetch_array()){
?>
<table border="1" onclick="window.location.href='guilds.php?guild=<?=urlencode($a['name'])?>'" style="cursor: pointer; width: 100%;">
<tr><td style="width: 64px; height: 64px; padding: 10px;"><img src="guilds/<?=$a['id']?>.gif" alt="NO IMG" height="64" width="64"/></td>
<td style="vertical-align: top;">
<b><?=htmlspecialchars($a['name'])?></b><hr/>
<?=@file_get_contents('guilds/'.$a['id'].'.txt')?>
</td></tr>
</table>
	
<?}
}else{
//-------------------------Member list
$guild = new Guild($_GET['guild']);
if ($guild->load()){
?>
<table style="width: 100%"><tr><td style="width: 64px; height: 64px; padding: 10px;"><img src="guilds/<?=$guild->getAttr('id')?>.gif" alt="NO IMG" height="64" width="64"/></td><td style="text-align: center">
<h1 style="display: inline"><?=htmlspecialchars($guild->getAttr('name'))?>
</h1></td><td style="width: 64px; height: 64px; padding: 10px;">
<img src="guilds/<?=$guild->getAttr('id')?>.gif" alt="NO IMG" height="64" width="64"/></td></tr>
</table>
<p><?=@file_get_contents('guilds/'.$guild->getAttr('id').'.txt')?></p><hr/>
<ul class="task-menu" style="width: 200px;">
<li style="background-image: url(ico/book_previous.png);" onclick="self.window.location.href='guilds.php'">Back</li>
<?
if (!empty($_SESSION['account'])){
	$account = new Account($_SESSION['account']);
	if (!$account->load()) die('Cannot load account');
	$invited = false;
	$member = false;
	foreach ($account->players as $player){
		if ($guild->isNameInvited($player->getAttr('name')))
			$invited = true;
		if ($guild->isNameMember($player->getAttr('name')))
			$member = true;
	}
	if ($guild->getAttr('owner_acc') == $_SESSION['account']){?>
<li style="background-image: url(ico/user_go.png);" onclick="ajax('form','modules/guild_invite.php','guild_name=<?=$guild->getAttr('name')?>',true)">Invite Player</li>
<li style="background-image: url(ico/group_delete.png);" onclick="ajax('form','modules/guild_kick.php','guild_name=<?=$guild->getAttr('name')?>',true)">Kick Member</li>
<li style="background-image: url(ico/user_edit.png);" onclick="ajax('form','modules/guild_edit.php','guild_name=<?=$guild->getAttr('name')?>',true)">Edit Member</li>
<li style="background-image: url(ico/image_add.png);" onclick="ajax('form','modules/guild_image.php','gid=<?=$guild->getAttr('id')?>',true)">Upload Image</li>
<li style="background-image: url(ico/page_edit.png);" onclick="ajax('form','modules/guild_comments.php','gid=<?=$guild->getAttr('id')?>',true)">Edit Description</li>
<?	}
	if ($invited){?>
<li style="background-image: url(ico/user_red.png);" onclick="ajax('form','modules/guild_join.php','guild_name=<?=$guild->getAttr('name')?>',true)">Join Guild</li>
<?	}
	if ($member){?>
<li style="background-image: url(ico/user_delete.png);" onclick="ajax('form','modules/guild_leave.php','guild_name=<?=$guild->getAttr('name')?>',true)">Leave Guild</li>
<?	}?>
<li style="background-image: url(ico/resultset_previous.png);" onclick="self.window.location.href='login.php?logout&amp;redirect=account.php'">Logout</li>
<?}else{?>
<li style="background-image: url(ico/resultset_next.png);" onclick="self.window.location.href='login.php?redirect=guilds.php'">Login</li>
<?}?>
</ul><hr/>
<h2 style="display: inline">Guild Members</h2>
<table style="width: 100%">
<tr class="color0"><td style="width: 30%"><b>Rank</b></td><td style="width: 70%"><b>Name and Title</b></td></tr>
<?
$data = $guild->getAttr('members');
foreach ($data as $a)
	$members[$a['rank']][] = array('name' => $a['name'], 'nick' => $a['nick']);
$data = $guild->getAttr('invited');
foreach ($data as $a)
	$members[$a['rank']][] = array('name' => $a['name'], 'nick' => 'Invited');

while ($rank = current($members)){
	$i++;
	$rank_name = key($members);
	foreach ($rank as $member){
		if (!empty($member['nick'])) $nick = ' (<i>'.htmlspecialchars($member['nick']).'</i>)';
		else $nick = '';
		echo '<tr '.getStyle($i).'><td>'.htmlspecialchars($rank_name).'</td><td><a href="characters.php?char='.addslashes(htmlspecialchars($member['name'])).'">'.htmlspecialchars($member['name']).'</a> '.$nick.'</td></tr>';
		$rank_name = '';
	}
	next($members);
}
?>
</table>
<?}else echo 'Guild not found';
}?>
</div>
<div class="bot"></div>
</div>
<?include('footer.inc.php');?>