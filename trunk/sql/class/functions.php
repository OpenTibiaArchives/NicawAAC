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

class Polls extends MySQL
{
private $list;

public function __construct()
	{
		$handle = $this->myQuery('SELECT * FROM `nicaw_polls`');
		if ($handle === false) return false;
		while ($a = mysql_fetch_array($handle)){
			$this->list[] = $a;
		}
	}

public function getPoll($n)
	{	
		if (isset($this->list[$n])){
			$this->list[$n]['options'] = explode(';',$this->list[$n]['options']);
			$this->list[$n]['results'] = explode(';',$this->list[$n]['results']);
			return $this->list[$n];
		}else
			return false;
	}
public function getMax()
	{
		return count($this->list)-1;
	}
}
################################################################################
function errorLog($err){
	$f = fopen('errors.inc','a');
	fwrite($f,date("Y.m.d H:i",time()).' '.$err);
	fclose($f);
}

function posToStr($pos){
	$array[] = $pos['x'];
	$array[] = $pos['y'];
	$array[] = $pos['z'];
	return implode(';',$array);
}

function strToPos($str){
	$pos = explode(';',$str);
	return array('x' => $pos[0], 'y' => $pos[1], 'z' => $pos[2]);
}

function getVocLvl($voc){
	global $cfg;
	return floor($cfg['vocations'][$voc]['level']);
}

function getVocExp($voc){
	global $cfg;
	$x = $cfg['vocations'][$voc]['level'];
	return round(50*($x-1)*(pow($x,2)-5*$x+12)/3);
}

function getinfo($host='localhost',$port=7171){
		// connects to server
        $socket = @fsockopen($host, $port, $errorCode, $errorString, 1);

        // if connected then checking statistics
        if($socket)
        {
            // sets 1 second timeout for reading and writing
            stream_set_timeout($socket, 1);

            // sends packet with request
            // 06 - length of packet, 255, 255 is the comamnd identifier, 'info' is a request
            fwrite($socket, chr(6).chr(0).chr(255).chr(255).'info');

            // reads respond
			while (!feof($socket)){
				$data .= fread($socket, 128);
			}

			// closing connection to current server
			fclose($socket);
		}
	return $data;
}

function getStyle($seed)
{
	if ($seed % 2 == 0)
		return 'class="color1"';
	else
		return 'class="color2"';
}
?>