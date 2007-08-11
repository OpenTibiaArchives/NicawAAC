<?
/*
    Copyright (C) 2006  Nicaw

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
include('include.inc.php');
$ptitle="Server Info - $cfg[server_name]";
include ("header.inc.php");
?>
<div id="content">
<div class="top">Server Info</div>
<div class="mid">
<?
if (!empty($info)){
$data = simplexml_load_string($info);
	$up = (int)$infoXML->serverinfo['uptime'];
	$online = (int)$infoXML->players['online'];
	$max = (int)$infoXML->players['max'];

	$h = floor($up/3600);
	$up = $up - $h*3600;
	$m = floor($up/60);
	$up = $up - $m*60;
	if ($h < 10) {$h = "0".$h;}
	if ($m < 10) {$m = "0".$m;}
?>
<table>
<tr><td>Server name</td><td><b><?=$data->serverinfo['servername']?></b></td></tr>
<tr><td>IP:port</td><td><b><?=$data->serverinfo['ip']?>:<?=$data->serverinfo['port']?></b></td></tr>
<tr><td>Server version</td><td><b><?=$data->serverinfo['server']?> <?=$data->serverinfo['version']?></b></td></tr>
<tr><td>Client</td><td><b><?=$data->serverinfo['client']?></b></td></tr>

<tr><td>Website URL</td><td><b><?=$data->serverinfo['url']?></b></td></tr>
<tr><td>Location</td><td><b><?=$data->serverinfo['location']?></b></td></tr>
<tr><td>Owner</td><td><b><?=$data->owner['name']?> (<?=$data->owner['email']?>)</b></td></tr>

<tr><td>Map</td><td><b><?=$data->map['name']?></b></td></tr>
<tr><td>Map author</td><td><b><?=$data->map['author']?></b></td></tr>
<tr><td>Map size</td><td><b><?=$data->monsters['width']?>x<?=$data->monsters['height']?></b></td></tr>
<tr><td>Monsters</td><td><b><?=$data->monsters['total']?></b></td></tr>

</table>
<pre>
<?=htmlentities($data->motd)?>
</pre>
<?}else echo 'Server not online';?>
</div>
<div class="bot"></div>
</div>
<?include ('footer.inc.php');?>