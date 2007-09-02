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
include ("../include.inc.php");

if (isset($_POST['image_submit'])){
	$gid = (int) $_GET['gid'];
	//get guild owner acc
	$SQL = new SQL();
	$query = 'SELECT players.account_id, guilds.name FROM players, guilds WHERE guilds.ownerid = players.id AND guilds.id = '.$SQL->escape_string($gid);
	$SQL->myQuery($query);
	$result = $SQL->fetch_array();
	$owner = (int) $result['account_id'];
	//check if user is guild owner
	if ($owner == $_SESSION['account'] && !empty($_SESSION['account'])){
		if ($_FILES['image']['size'] <= 102400){
			($_FILES['image']['error'] == 0) or die('Unknown error');
			is_uploaded_file($_FILES['image']['tmp_name']) or die('File is not uploaded via HTTP POST');
			if ($_FILES['image']['type'] == 'image/gif'){
				@unlink('guilds/'.$gid.'.gif');
				copy($_FILES['image']['tmp_name'],'../guilds/'.$gid.'.gif');
			}else $error = "Unsupported image type";
		}else $error = "Image too big";
	}else $error = "Please login before uploading image";
	if (!empty($error)) echo $error;
	else header('location: '.$_SERVER['HTTP_REFERER']);
}else{
$gid = (int) $_POST['gid'];
?>
<div id="iobox" class="draggable">
<fieldset><legend>Upload Image</legend>
<form method="post" action="modules/guild_image.php?gid=<?=$gid?>" enctype="multipart/form-data">
<input type="file" name="image">
<input type="Submit" name="image_submit" value="Upload"><br/>
Supported type *.GIF 64x64 100KB
</form></fieldset></div>
<?}?>