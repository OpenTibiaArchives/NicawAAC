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
$ptitle="Houses - $cfg[server_name]";
include ("header.inc.php");

?>
<div id="content">
<div class="top">.:Highscores:.</div>
<div class="mid">
<div style="margin-bottom: 20px;">
<a href="ranks.php?sort=level">Level</a>&nbsp;
<a href="ranks.php?sort=maglevel">Magic Level</a>&nbsp;
<?

if (empty($_GET['sort'])) $_GET['sort'] = 'level';

foreach ($cfg['skill_names'] as $skill)
	echo '<a href="ranks.php?sort='.$skill.'">'.ucfirst($skill).'</a>&nbsp;';

$p = (int) $_GET['page'];
if (empty($p) || $p < 0) $p = 0;
?>
</div>
<input type="button" value="&lt;&lt;" onclick="self.window.location.href='ranks.php?sort=<?=$_GET['sort']?>&amp;page=<?=$p-1?>'"/>
<b>Statistics page: <?=$p+1?></b>
<input type="button" value="&gt;&gt;" onclick="self.window.location.href='ranks.php?sort=<?=$_GET['sort']?>&amp;page=<?=$p+1?>'"/>
<table style="width: 100%;">
<tr class="color0"><td>#</td><td><b>Name</b></td><td><b><?=ucfirst($_GET['sort'])?></b></td></tr>
<?
if ($_GET['sort'] == 'level' || $_GET['sort'] == 'maglevel'){
	$query = 'SELECT groups.access, groups.id, players.name, players.level, players.maglevel, players.experience FROM players LEFT OUTER JOIN groups ON players.group_id = groups.id ORDER BY '.mysql_escape_string($_GET['sort']).' DESC LIMIT '.$cfg['ranks_per_page']*$p.', '.$cfg['ranks_per_page'].';';
	$key = $_GET['sort'];
}elseif (in_array($_GET['sort'],$cfg['skill_names'])){
	$query = 'SELECT groups.access, a1.* FROM (SELECT players.group_id, players.name, player_skills.value FROM players, player_skills WHERE players.id = player_skills.player_id AND player_skills.skillid = '.array_search($_GET['sort'], $cfg['skill_names']) .') AS a1 LEFT OUTER JOIN groups ON a1.group_id = groups.id ORDER BY `value` DESC LIMIT '.$cfg['ranks_per_page']*$p.', '.$cfg['ranks_per_page'].';';
	$key = 'value';
}else{$error = "Invalid sort argument";}

if (isset($query)){
	$MySQL = new MySQL();
	$sql = $MySQL->myQuery($query);
	if ($sql === false){
		$error = $MySQL->getError();
	}else{
		$i = $cfg['ranks_per_page']*$p;
		while($a=mysql_fetch_array($sql))
		if ($a['access'] < $cfg['ranks_access'])
			{
				$i++;
				echo '<tr '.getStyle($i).'><td>'.$i.'</td><td><a href="characters.php?char='.$a['name'].'">'.$a['name'].'</a></td><td>'.$a[$key].'</td></tr>'."\n";
			}
	}
}
?>
</table>
</div>
<div class="bot"></div>
</div>
<?include('footer.inc.php');?>