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

/*class Polls extends SQL
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
}*/
################################################################################
function exception_handler($exception) {
	include_once ('header.inc.php');
	echo '<b>'.$exception->getMessage(). '<br/>'.basename($exception->getFile()).' line '.$exception->getLine().'</b>';
	include_once ('footer.inc.php');
}

function errorLog($err){
	$f = fopen('errors.inc','a');
	fwrite($f,date("Y.m.d H:i",time()).' '.$err."\r\n");
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

function strToDate($str){
  $pieces = explode('-',$str);
  return mktime(0,0,0,(int)$pieces[1],(int)$pieces[2],(int)$pieces[0]);
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

function getStyle($seed)
{
	if ($seed % 2 == 0)
		return 'class="color1"';
	else
		return 'class="color2"';
}

function percent_bar($part, $total)
{
	$percent = round($part/$total*100);
	if ($percent >= 10)
		$percent_text = $percent.'%';
	return '<div class="percent_bar" style="width:'.($percent*2).'px">'.$percent_text.'</div>';
}
?>