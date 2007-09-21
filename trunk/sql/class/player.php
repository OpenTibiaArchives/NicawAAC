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
class Player extends SQL
{
private $attrs;
private $skills;

public function __construct($n)
	{
		//initialize SQl object
		$this->_init();
		$this->attrs['name'] = $n;
		return true;
	}

public function load()
	{
		if (!isset($this->attrs['name'])) return false;
		//I don't load complete data like items etc, just the stuff I need
		$player = $this->myRetrieve('players', array('name' => $this->attrs['name']));
		if ($player === false) return false;
		$group = $this->myRetrieve('groups', array('id' => (int) $player['group_id']));
		if ($group === false)
			$this->attrs['access'] = 0;
		else{
      $this->attrs['group'] = (int) $player['group_id'];
			$this->attrs['access'] = (int) $group['access'];
			$this->attrs['position'] = (string) $group['name'];
		}
		$this->attrs['id'] = (int) $player['id'];
		$this->attrs['name'] = (string) $player['name'];
		$this->attrs['account'] = (int) $player['account_id'];
		$this->attrs['level'] = (int) $player['level'];
		$this->attrs['vocation'] = (int) $player['vocation'];
		$this->attrs['experience'] = (int) $player['experience'];
		$this->attrs['promoted'] = 0;
		$this->attrs['maglevel'] = (int) $player['maglevel'];
		$this->attrs['city'] = (int) $player['town_id'];
		$this->attrs['sex'] = (int) $player['sex'];
		$this->attrs['lastlogin'] = (int) $player['lastlogin'];
		$this->attrs['redskulltime'] = (int) $player['redskulltime'];
		//get skills
		$skills = $this->myQuery('SELECT * FROM `player_skills` WHERE `player_id` = '.$this->attrs['id']);
		if ($skills === false) return false;
		while($a = $this->fetch_array($skills)){
			$this->skills[$a['skillid']]['skill'] = $a['value'];
			$this->skills[$a['skillid']]['tries'] = $a['count'];
		}
		//get guild stuff
		$guild = $this->myQuery("SELECT players.guildnick, guild_ranks.level, guild_ranks.name, guilds.id, guilds.name FROM guild_ranks, players, guilds WHERE guilds.id = guild_ranks.guild_id AND players.rank_id = guild_ranks.id AND players.id = ".$this->attrs['id']);
		if ($this->num_rows($guild) == 1){
			$a = $this->fetch_array($guild);
			$this->attrs['guild_nick'] = $a[0];
			$this->attrs['guild_level'] = $a[1];
			$this->attrs['guild_rank'] = $a[2];
			$this->attrs['guild_id'] = $a[3];
			$this->attrs['guild_name'] = $a[4];
		}
		return true;
	}

public function save()
	{
    $d['group_id'] = $this->attrs['group'];
		$d['name'] = $this->attrs['name'];
		$d['account_id'] = $this->attrs['account'];
		$d['level'] = $this->attrs['level'];
		$d['vocation'] = $this->attrs['vocation'];
		$d['experience'] = $this->attrs['experience'];
		$d['maglevel'] = $this->attrs['maglevel'];
		$d['town_id'] = $this->attrs['city'];
		$d['sex'] = $this->attrs['sex'];
		$d['redskulltime'] = (int) $player['redskulltime'];
		
		return $this->myUpdate('players', $d, array('id' => $this->attrs['id']));
	}

public function isValidName()
	{global $cfg;
		foreach ($cfg['invalid_names'] as $name)
			if (eregi($name,$this->attrs['name']))
				return false;
		return preg_match($cfg['name_format'],$this->attrs['name'])
		&& strlen($this->attrs['name']) <= 25 && strlen($this->attrs['name']) >= 4
		&& !file_exists($cfg['dirdata'].'monster/'.$this->attrs['name'].'.xml')
		&& !file_exists($cfg['dirdata'].'npc/'.$this->attrs['name'].'.xml');
	}

public function exists()
	{
		$sql = $this->myRetrieve('players',array('name' => $this->attrs['name']));
		if ($sql === false){
			return false;
		}else{
			return true;
		}
	}

public function getAttr($attr)
	{
		return $this->attrs[$attr];
	}

public function isAttr($attr)
	{
		return !empty($this->attrs[$attr]);
	}

public function setAttr($attr,$value)
	{
		$this->attrs[$attr] = $value;
	}

public function getDeaths()
	{
		$query = "SELECT * FROM `deathlist` WHERE (`player` = '".$this->escape_string($this->attrs['id'])."') ORDER BY date DESC LIMIT 10";
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
		$i = 0;
		while($a = $this->fetch_array($sql)){
			$list[$i]['killer'] = $a['killer'];
			$list[$i]['level'] = $a['level'];
			$list[$i]['date'] = $a['date'];
			$i++;
		}
		return $list;
	}

/*public function getInvites()
	{
		$sql = $this->myQuery('SELECT guilds.name, guilds.id FROM guilds, nicaw_invites WHERE nicaw_invites.gid = guilds.id AND nicaw_invites.pid = '.$this->attrs['id']);
		if ($sql === false) return false;
		if ($this->num_rows($sql) == 0) return false;
		while($a = $this->fetch_array($sql)){
			$return[$a['id']]['name'] = $a['name'];
			$return[$a['id']]['id'] = $a['id'];
		}
		return $return;
	}
*/	
public function getSkill($n)
	{
		return $this->skills[$n]['skill'];
	}

public function delete()
	{
			return $this->myDelete('players',array('id' => $this->attrs['id']),0)
			&& $this->myDelete('player_items',array('player_id' => $this->attrs['id']),0)
			&& $this->myDelete('player_depotitems',array('player_id' => $this->attrs['id']),0)
			&& $this->myDelete('player_skills',array('player_id' => $this->attrs['id']),0)
			&& $this->myDelete('player_storage',array('player_id' => $this->attrs['id']),0)
			&& $this->myDelete('player_viplist',array('player_id' => $this->attrs['id']),0);
	}

public function make()
	{global $cfg;

		if ($this->exists()){
			$this->err = 'Player already exists';
			return false;
		}

		//make player
		$d['id']			= NULL;
		$d['name']			= $this->attrs['name'];
		$d['account_id']	= $this->attrs['account'];
		$d['vocation']		= $this->attrs['vocation'];
		$d['sex']			= $this->attrs['sex'];
		$d['level']			= getVocLvl($this->attrs['vocation']);
		$d['experience']	= getVocExp($this->attrs['vocation']);
		$d['health']		= $cfg['vocations'][$this->attrs['vocation']]['health'];
		$d['healthmax']		= $cfg['vocations'][$this->attrs['vocation']]['health'];
		$d['looktype']		= $cfg['vocations'][$this->attrs['vocation']]['look'][(int)$this->attrs['sex']];
		$d['maglevel']		= $cfg['vocations'][$this->attrs['vocation']]['maglevel'];
		$d['mana']			= $cfg['vocations'][$this->attrs['vocation']]['mana'];
		$d['manamax']		= $cfg['vocations'][$this->attrs['vocation']]['mana'];
		$d['cap']			= $cfg['vocations'][$this->attrs['vocation']]['cap'];
		$d['town_id']		= $this->attrs['city'];
		$d['posx']			= $cfg['temple'][$this->attrs['city']]['x'];
		$d['posy']			= $cfg['temple'][$this->attrs['city']]['y'];
		$d['posz']			= $cfg['temple'][$this->attrs['city']]['z'];
		
		if (!$this->myInsert('players',$d)) return false;
		$this->attrs['id'] = $this->PDO->lastInsertId();

		unset($d);

		//make items
		$sid = 100;
		while ($item = current($cfg['vocations'][$this->attrs['vocation']]['equipment'])){
				$sid++;
				$d['player_id']	= $this->attrs['id'];
				$d['pid']		= key($cfg['vocations'][$this->attrs['vocation']]['equipment']);
				$d['sid']		= $sid;
				$d['itemtype']	= $item;
				
				if (!$this->myInsert('player_items',$d)) return false;
				unset($d);
				next($cfg['vocations'][$this->attrs['vocation']]['equipment']);
		}

		//make skills
		$i = 0;
		while ($skill = current($cfg['vocations'][$this->attrs['vocation']]['skills'])){
			$d['player_id']	= $this->attrs['id'];
			$d['skillid']	= key($cfg['vocations'][$this->attrs['vocation']]['skills']);
			$d['value']		= $skill;
			$d['count']		= '0';

			if (!$this->myInsert('player_skills',$d)) return false;
			unset($d);
			next($cfg['vocations'][$this->attrs['vocation']]['skills']);
		}
	return $this->load();
	}

public function repair()
	{global $cfg;
		$lvl = $this->attrs['level'];
		$exp = round(50*($lvl-1)*($lvl*$lvl-5*$lvl+12)/3);
		if (!$this->myUpdate('players',array(
			'posx' => $cfg['temple'][$this->attrs['city']]['x'],
			'posy' => $cfg['temple'][$this->attrs['city']]['y'],
			'posz' => $cfg['temple'][$this->attrs['city']]['z']
			/*, 'experience' => $exp*/), array('id' => $this->attrs['id']))) return false;
		return $this->load();
	}
}
?>