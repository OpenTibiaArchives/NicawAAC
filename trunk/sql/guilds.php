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
<?
// Show guild list
if (!isset($_GET['id'])){
$query = 'SELECT guilds.id, guilds.name, COUNT(guilds.id) FROM guilds, guild_ranks, players WHERE guilds.id = guild_ranks.guild_id AND guild_ranks.id = players.rank_id AND players.level >= '.$cfg['guild_level'].' GROUP BY guilds.id ORDER BY COUNT(guilds.id) DESC';
$SQL->myQuery($query);
if ($SQL->failed())
	throw new Exception('SQL query failed:<br/>'.$SQL->getError());
while ($a = $SQL->fetch_array()){
?>
<table border="1" onclick="window.location.href='guilds.php?id=<?=$a['id']?>'" style="cursor: pointer; width: 100%;">
<tr><td style="width: 64px; height: 64px; padding: 10px;"><img src="guilds/<?=$a['id']?>.gif" alt="NO IMG" height="64" width="64"/></td>
<td style="vertical-align: top;">
<b><?=htmlentities($a['name'])?></b><hr/>
<?=@file_get_contents('guilds/'.$a['id'].'.txt')?>
</td></tr>
</table>
	
<?}
}else{
#Member List
$gid = (int) $_GET['id'];
$query = 'SELECT players.account_id, guilds.name FROM players, guilds WHERE guilds.ownerid = players.id AND guilds.id = '.$SQL->escape_string($gid);
$SQL->myQuery($query);
$result = $SQL->fetch_array();
$owner = (int) $result['account_id'];
$name = $result['name'];
?>
<table style="width: 100%"><tr><td style="width: 64px; height: 64px; padding: 10px;"><img src="guilds/<?=$gid?>.gif" alt="NO IMG" height="64" width="64"/></td><td style="text-align: center">
<h1 style="display: inline"><?=htmlspecialchars($name)?>
</h1></td><td style="width: 64px; height: 64px; padding: 10px;">
<img src="guilds/<?=$gid?>.gif" alt="NO IMG" height="64" width="64"/></td></tr>
</table>
<p><?=@file_get_contents('guilds/'.$gid.'.txt')?></p><hr/>
<ul class="task-menu" style="width: 200px;">
<?
if ($owner == $_SESSION['account'] && !empty($_SESSION['account'])){?>
<li style="background-image: url(ico/image_add.png);" onclick="ajax('form','modules/guild_image.php','gid=<?=$gid?>',true)">Upload Image</li>
<li style="background-image: url(ico/page_edit.png);" onclick="ajax('form','modules/guild_comments.php','gid=<?=$gid?>',true)">Edit Description</li>
<li style="background-image: url(ico/book_previous.png);" onclick="self.window.location.href='guilds.php'">Back</li>
<li style="background-image: url(ico/resultset_previous.png);" onclick="window.location.href='login.php?logout&amp;redirect=account.php'">Logout</li>
<?}else{?>
<li style="background-image: url(ico/book_previous.png);" onclick="self.window.location.href='guilds.php'">Back</li>
<li style="background-image: url(ico/resultset_next.png);" onclick="window.location.href='login.php?redirect=guilds.php?id=<?=$gid?>'">Login</li>
<?}?>
</ul><hr/>
<h2 style="display: inline">Guild Members</h2>
<table style="width: 100%">
<tr class="color0"><td style="width: 30%"><b>Rank</b></td><td style="width: 70%"><b>Name and Title</b></td></tr>
<?
$ranks = $SQL->myQuery('SELECT id, name FROM guild_ranks WHERE guild_id = \''.$SQL->escape_string($gid).'\' ORDER BY level DESC');
		if ($ranks === false) $error = $SQL->getError();
		while ($rank = $SQL->fetch_array()){
			$members = $SQL->myQuery('SELECT players.name, players.guildnick  FROM guild_ranks, players WHERE guild_ranks.id = players.rank_id AND guild_ranks.id = '.$rank['id'].' ORDER BY players.level DESC');
			if ($members === false) $error = $SQL->getError();
			$i++;
			while ($member = $SQL->fetch_array()){
				$rank = $rank['name'];
				if (!empty($member['guildnick'])) $nick = ' (<i>'.$member['guildnick'].'</i>)';
				else $nick = '';
				echo '<tr '.getStyle($i).'><td>'.htmlentities($rank).'</td><td><a href="characters.php?char='.addslashes($member['name']).'">'.htmlentities($member['name']).'</a> '.$nick.'</td></tr>';
				$rank = '';
			}
		}
?>
</table>
<?}?>
</div>
<div class="bot"></div>
</div>
<?include('footer.inc.php');?>