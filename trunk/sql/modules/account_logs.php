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

$account = new Account($_SESSION['account']);
($account->load()) or die('You need to login first. '.$account->getError());
$_SESSION['last_activity']=time();

$logs = $account->getLogs(10);
	if ($logs !== false && count($logs) > 0){
		echo '<h2>Account Logs</h2>'."\n";
		echo '<table style="width:100%">'."\n";
		echo '<tr class="color0"><td><b>Date</b></td><td><b>IP address</b></td><td><b>Action</b></td></tr>'."\n";
		foreach ($logs as $log){
			$i++;
			echo '<tr '.getStyle($i).'><td>'.date("Y.m.d H:i",$log['date']).'</td><td>'.$log['ip'].'</td><td>'.$log['action'].'</td></tr>'."\n";
			}
		echo '</table>';
	}
?>