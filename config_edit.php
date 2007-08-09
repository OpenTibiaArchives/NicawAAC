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
$lines = file('trunk/sql/config.inc.php');
for ($i=1; $i<=count($lines); $i++){
	if (substr($lines[$i],0,4) == '$cfg'){
		if (substr($lines[$i-1],0,1) == '#')
			$item['des'] = $lines[$i-1];
		$pattern = '/^\$cfg\[\'([^\r\n\[\]\']+?)\'\]\s+?=\s+?\'?(.+?)\'?;/';
		preg_match($pattern,$lines[$i],$out);
		$item['name'] = $out[1];
		$item['value'] = $out[2];
		$pattern = '/array\((.+)?\);/';
		if (preg_match($pattern,$lines[$i],$out) > 0){
			$item['value'] = $out[1];
			$item['value'] = explode(',',$item['value']);
		}			
		print_r($item);
		unset($item);
	}
}
?>
