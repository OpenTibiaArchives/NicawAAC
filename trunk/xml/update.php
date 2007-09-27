<?
/*FILE INFO:
Statistics generator*/

set_time_limit(3*60*60); // 3 hour should be enough
include('config.php');
include('functions.php');

//Preventing possible DoS attack
if (file_exists('statistics.php')){
  $age = time()-filemtime('statistics.php') ;
  if ($age < floor($cfg['rank_refresh']/2) && $age < time()){
	  die('Access denied.');
  }
}
touch('statistics.php');

$f_rank = fopen("statistics.php","w");

fwrite($f_rank,'<?
');

$dir_rank = opendir($cfg['dirplayer']);
while($file = readdir($dir_rank) ){
    if ( eregi('\.xml$', $file)){
        $player = new Player (basename($file, '.xml'));
		if ($player->load())
			if ($player->data['access'] < $cfg['gm_access'] && isset($player->data['name'])){
				if ($player->data['banned'] == '1' && $cfg['unban_allow'] && (time() - $player->data['lastlogin']) >= $cfg['unban_after']){
					$player->data['banned'] = '0';
					$player->save();
					$unban_log .= "Unbanned: ".$file."\r\n";
				}

				if ($cfg['delete_allow'] && (time() - $player->data['lastlogin']) >= $cfg['delete_player'] && $player->data['level'] < $cfg['delete_level']){
					unlink($cfg['dirplayer'] . $file);
					$delete_log .= "Deleted: ".$file."\r\n";
				}
				
				switch ($player->data['voc']){
					case 0: $none++; break;
					case 1: $sorcerer++; break;
					case 2: $druid++; break;
					case 3: $paladin++; break;
					case 4: $knight++; break;
				}

				switch ($player->data['sex']){
					case 0: $female++; break;
					case 1: $male++; break;
				}
				
				$all_stats['name'][] =		(string) $player->data['name'];
				$all_stats['level'][] =		(int) $player->data['level'];
				$all_stats['magic'][] =		(int) $player->data['maglevel'];
				
				$sn = Array('fist', 'club', 'sword', 'axe', 'distance', 'shield', 'fishing');
				for ($i=0; $i < count($sn); $i++){
					$all_stats[$sn[$i]][] = (int) $player->data->skills->skill[$i]['level'];
				}
		}
    }
}
// array_multisort() failed to work here :~/
foreach (array_keys($all_stats) as $stat){
	if ($stat !== 'name'){
		$buffer = $all_stats[$stat];
		arsort($buffer);
		$i = 0;
		while ($i < $cfg['highscoreshow'] ){
			fwrite($f_rank,'$statistics[\''.$stat.'\'][] = array(\'name\'=>\''.str_replace("'","\\'",$all_stats['name'][key($buffer)]).'\', \'skill\'=>\''.current($buffer).'\');'."\r\n");
			next($buffer);
			$i++;
		}
	}
}
closedir($dir_rank);

fwrite($f_rank,'
$statistics[\'census\'][\'male\'] = \''.$male.'\';
$statistics[\'census\'][\'female\'] = \''.$female.'\';

$statistics[\'census\'][\'none\'] = \''.$none.'\';
$statistics[\'census\'][\'sorcerer\'] = \''.$sorcerer.'\';
$statistics[\'census\'][\'druid\'] = \''.$druid.'\';
$statistics[\'census\'][\'paladin\'] = \''.$paladin.'\';
$statistics[\'census\'][\'knight\'] = \''.$knight.'\';
');



fwrite($f_rank,'?>');
fclose($f_rank);

//Log events
if ($cfg['delete_allow'] || $cfg['unban_allow']){
	$log = fopen("logs/playerlog.txt","a");
	fwrite($log,date("jS F Y H:i:s",time())."\r\n");
	fwrite($log,$unban_log);
	fwrite($log,$delete_log);
	fwrite($log,"#####################################################\r\n");
	fclose($log);
}
?>