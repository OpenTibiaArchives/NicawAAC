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
$ptitle="Highscores - $cfg[server_name]";
include ("header.inc.php");

$SQL = new SQL();
?>
<div id="content">
<div class="top">Highscores</div>
<div class="mid">
<select name="sort" onchange="self.location.href=this.value">
<?
if (empty($_GET['sort'])) $_GET['sort'] = 'census';

$options = array_merge(array('census', 'level', 'maglevel'), $cfg['skill_names']);

foreach ($options as $skill){
	if ($skill == $_GET['sort'])
		$selected = ' selected="selected"';
	else
		$selected = '';
	echo '<option value="ranks.php?sort='.$skill.'"'.$selected.'>'.ucfirst($skill).'</option>';
}
echo '</select>';

$p = (int) $_GET['page'];
if (empty($p) || $p < 0) $p = 0;
if ($_GET['sort'] == 'level' || $_GET['sort'] == 'maglevel'){
	$query = 'SELECT groups.access, groups.id, players.name, players.level, players.maglevel, players.experience FROM players LEFT OUTER JOIN groups ON players.group_id = groups.id ORDER BY `'.mysql_escape_string($_GET['sort']).'` DESC LIMIT '.$cfg['ranks_per_page']*$p.', '.$cfg['ranks_per_page'].';';
	$key = $_GET['sort'];
}elseif (in_array($_GET['sort'],$cfg['skill_names'])){
	$query = 'SELECT groups.access, a1.* FROM (SELECT players.group_id, players.name, player_skills.value FROM players, player_skills WHERE players.id = player_skills.player_id AND player_skills.skillid = '.array_search($_GET['sort'], $cfg['skill_names']) .') AS a1 LEFT OUTER JOIN groups ON a1.group_id = groups.id ORDER BY `value` DESC LIMIT '.$cfg['ranks_per_page']*$p.', '.$cfg['ranks_per_page'].';';
	$key = 'value';
}elseif ($_GET['sort'] == 'census'){
	$SQL->myQuery('SELECT players.sex, COUNT(players.id) as number FROM `players` GROUP BY players.sex');
	$total = 0;
	while ($a = $SQL->fetch_array()){
		$genders[$a['sex']] = $a['number'];
		$total += $a['number'];
	}
	$gender_names = array(0 => 'Male',1 => 'Female');
	echo '<p><h2>Gender</h2>';
	echo '<table style="font-weight: bold">';
	foreach (array_keys($genders) as $gender)
		echo '<tr><td>'.$gender_names[$gender].'</td><td>'.percent_bar($genders[$gender],$total).'</td><td>('.$genders[$gender].')</td></tr>';
	echo '</table></p>';
	$SQL->myQuery('SELECT players.vocation, COUNT(players.id) as number FROM `players` GROUP BY players.vocation');
	$total = 0;
	while ($a = $SQL->fetch_array()){
		$vocations[$a['vocation']] = $a['number'];
		$total += $a['number'];
	}
	echo '<p><h2>Vocations</h2>';
	echo '<table style="font-weight: bold">';
	foreach (array_keys($vocations) as $vocation)
		echo '<tr><td>'.$cfg['vocations'][$vocation]['name'].'</td><td>'.percent_bar($vocations[$vocation],$total).'</td><td>('.$vocations[$vocation].')</td></tr>';
	echo '</table></p>';	

}else{$error = "Invalid sort argument";}

if (isset($query)){
?>
<input type="button" value="&lt;&lt;" onclick="self.window.location.href='ranks.php?sort=<?=htmlspecialchars($_GET['sort'])?>&amp;page=<?=$p-1?>'"/>
<b>Statistics page: <?=$p+1?></b>
<input type="button" value="&gt;&gt;" onclick="self.window.location.href='ranks.php?sort=<?=htmlspecialchars($_GET['sort'])?>&amp;page=<?=$p+1?>'"/>
<table>
<tr class="color0"><td style="width:30px">#</td><td style="width:150px"><b>Name</b></td><td style="width:60px"><b><?=htmlspecialchars(ucfirst($_GET['sort']))?></b></td></tr>
<?
	$SQL->myQuery($query);
	if ($SQL->failed())
		throw new Exception('SQL query failed:<br/>'.$mysql->getError());
	else{
		$i = $cfg['ranks_per_page']*$p;
		while($a = $SQL->fetch_array())
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