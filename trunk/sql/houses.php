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
<div class="top">Houses</div>
<div class="mid">
<div style="height: 500px; overflow: auto; margin: 10px;">
<table>
<tr class="color0"><td width="40%"><b>House</b></td><td width="30%"><b>Location</b></td><td width="30%"><b>Owner</b></td></tr>
<?

if (file_exists($cfg['dirdata'].$cfg['house_file'])){

$HousesXML = simplexml_load_file($cfg['dirdata'].$cfg['house_file']);
$MySQL = new MySQL();
$result = $MySQL->myQuery('SELECT `players`.`name`, `houses`.`id` FROM `players`, `houses` WHERE `houses`.`owner` = `players`.`id`;');
$error = $MySQL->getError();
while ($row = @mysql_fetch_array($result)){
	$houses[(int)$row['id']] = $row['name'];
}
foreach ($HousesXML->house as $house){
	$i++;
	$list.= '<tr '.getStyle($i).'><td>'.htmlspecialchars($house['name']).'</td><td>'.htmlspecialchars($cfg['temple'][(int)$house['townid']]['name']).'</td><td>'.$houses[(int)$house['houseid']].'</td></tr>'."\r\n";
}
echo $list;
}else $error = "House file not found";

?>
</table>
</div>
</div>
<div class="bot"></div>
</div>
<?include('footer.inc.php');?>