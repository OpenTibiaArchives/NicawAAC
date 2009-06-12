<?php
/*
     Copyright (C) 2007 - 2009  Nicaw

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
private $attrs, $skills, $storage, $deaths, $guild, $is_online;

public function __construct()
	{
		parent::__construct();
	}
	
public function find($name)
	{
		$player = $this->myRetrieve('players', array('name' => $name));
		if ($player === false) return false;
		$this->load($player['id']);
		return true;
	}
	
private function load_deaths()
	{
		if(empty($this->attrs['id'])) return false;
		$query = "SELECT * FROM `player_deaths` WHERE (`player_id` = '".$this->quote($this->attrs['id'])."') ORDER BY time DESC LIMIT 10";
		$this->myQuery($query);
		if ($this->failed()) throw new Exception('Cannot retrieve deaths! This is only compatible with TFS.'.$this->getError());;
		$i = 0;
		while($a = $this->fetch_array()){
			$this->deaths[$i]['killer'] = $a['killed_by'];
			$this->deaths[$i]['level'] = $a['level'];
			$this->deaths[$i]['date'] = $a['time'];
			$i++;
		}
		return true;
	}

private function load_skills()
	{
		if(empty($this->attrs['id'])) return false;
		$this->myQuery('SELECT * FROM `player_skills` WHERE `player_id` = '.$this->quote($this->attrs['id']));
		if ($this->failed()) throw new Exception('Cannot retrieve player skills<br/>'.$this->getError());
		while($a = $this->fetch_array()){
			$this->skills[$a['skillid']]['skill'] = (int)$a['value'];
			$this->skills[$a['skillid']]['tries'] = (int)$a['count'];
		}
		return true;
	}
	
private function load_storage()
	{
		if(empty($this->attrs['id'])) return false;
		$this->myQuery('SELECT * FROM `player_storage` WHERE `player_id` = '.$this->quote($this->attrs['id']));
		if ($this->failed()) throw new Exception('Cannot retrieve player storage<br/>'.$this->gerError());
		while($a = $this->fetch_array())
			$this->storage[$a['key']] = (int)$a['value'];
		return true;
	}
	
private function load_guild()
	{
		if(empty($this->attrs['rank_id'])) return false;
		$this->myQuery("SELECT players.guildnick, guild_ranks.level, guild_ranks.name, guilds.id, guilds.name FROM guild_ranks, guilds WHERE guilds.id = guild_ranks.guild_id AND players.rank_id = ".$this->quote($this->attrs['rank_id']));
		if (!$this->failed() && $this->num_rows() == 1){
			$a = $this->fetch_array();
			$this->guild['guild_nick'] = $a[0];
			$this->guild['guild_level'] = $a[1];
			$this->guild['guild_rank'] = $a[2];
			$this->guild['guild_id'] = $a[3];
			$this->guild['guild_name'] = $a[4];
		}
		return true;
	}

public function reload()
	{
		if(!empty($this->attrs['id'])) {
			unset($this->skills, $this->storage, $this->deaths, $this->guild);
			load($this->attrs['id']);
			return true;
		}
		return false;
	}
	
public function load($id)
	{
		//Load player attributes
		$player = $this->myRetrieve('players', array('id' => $id));
		if ($player === false) return false;
		$group = $this->myRetrieve('groups', array('id' => (int) $player['group_id']));
		if ($group === false)
			$this->attrs['access'] = 0;
		else {
			$this->attrs['group'] = (int) $player['group_id'];
			$this->attrs['access'] = (int) $group['access'];
			$this->attrs['position'] = (string) $group['name'];
		}
		if(isset($player['online'])) {
			if((bool) $player['online'] == true) {
				$this->is_online = true;
			} else {
				$this->is_online = false;
			}
		} elseif(isset($player['lastlogout'])) {
			if($player['lastlogin'] > $player['lastlogout']) {
				$this->is_online = true;
			} else {
				$this->is_online = false;
			}
		} else {
			$this->is_online = false;
		}
		$this->attrs['id'] = (int) $player['id'];
		$this->attrs['name'] = (string) $player['name'];
		$this->attrs['account'] = (int) $player['account_id'];
		$this->attrs['level'] = (int) $player['level'];
		$this->attrs['vocation'] = (int) $player['vocation'];
		$this->attrs['experience'] = (int) $player['experience'];
		$this->attrs['maglevel'] = (int) $player['maglevel'];
		$this->attrs['city'] = (int) $player['town_id'];
		$this->attrs['sex'] = (int) $player['sex'];
		$this->attrs['lastlogin'] = (int) $player['lastlogin'];
		$this->attrs['redskulltime'] = (int) $player['redskulltime'];
		$this->attrs['rank_id'] = (int) $player['rank_id'];
		$this->attrs['spawn']['x'] = (int) $player['posx'];
		$this->attrs['spawn']['y'] = (int) $player['posy'];
		$this->attrs['spawn']['z'] = (int) $player['posz'];

		return true;
	}

public function save()
	{
		//cannot save player unless it's offline
		if($this->is_online) return false;
		
		$d['group_id'] = $this->attrs['group'];
		$d['name'] = $this->attrs['name'];
		$d['account_id'] = $this->attrs['account'];
		$d['level'] = $this->attrs['level'];
		$d['vocation'] = $this->attrs['vocation'];
		$d['experience'] = $this->attrs['experience'];
		$d['maglevel'] = $this->attrs['maglevel'];
		$d['town_id'] = $this->attrs['city'];
		$d['sex'] = $this->attrs['sex'];
		$d['redskulltime'] = $this->attrs['redskulltime'];
		$d['rank_id'] = $this->attrs['rank_id'];
		
		return $this->myUpdate('players', $d, array('id' => $this->attrs['id']));
	}

public function exists()
	{
		$this->myQuery('SELECT * FROM `players` WHERE `name` = '.$this->quote($this->attrs['name']));
		if ($this->failed()) throw new Exception('Player::exists() cannot determine whether player exists');
		if ($this->num_rows() > 0) return true;
		return false;
	}
	
public function __get($attr)
	{
		if(empty($this->attrs['id']))
			throw new Exception('Attempt to get attribute of player that is not loaded.');
		if($attr == 'attrs') {
			return $this->attrs;
		}elseif($attr == 'skills') {
			if(empty($this->skills)) load_skills();
			return $this->skills;
		}elseif($attr == 'deaths') {
			if(empty($this->deaths)) load_deaths();
			return $this->deaths;
		}elseif($attr == 'storage') {
			if(empty($this->storage)) load_storage();
			return $this->storage;
		}elseif($attr == 'guild') {
			if(empty($this->guild)) load_guild();
			return $this->guild;
		}else{
			throw new Exception('Undefined property: '.$attr);
		}
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

public function create()
	{global $cfg;

		if ($this->exists())
			throw new Exception('Player already exists');

		//make player
		$d['id']			= NULL;
		$d['name']			= $this->attrs['name'];
		$d['account_id']	= $this->attrs['account'];
		$d['group_id']		= $cfg['vocations'][$this->attrs['vocation']]['group'];
		$d['rank_id']		= 0;
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
		$d['conditions']	= '';
		
		if (!$this->myInsert('players',$d)) throw new Exception('Player::make() Cannot insert attributes:<br/>'.$this->getError());
		$this->attrs['id'] = $this->insert_id();

		unset($d);

		//make items
		$sid = 100;
		while ($item = current($cfg['vocations'][$this->attrs['vocation']]['equipment'])){
			$sid++;
			$d['player_id']	= $this->attrs['id'];
			$d['pid']		= key($cfg['vocations'][$this->attrs['vocation']]['equipment']);
			$d['sid']		= $sid;
			$d['itemtype']	= $item;
			$d['attributes']= '';
			
			if (!$this->myInsert('player_items',$d)) throw new Exception('Player::make() Cannot insert items:<br/>'.$this->getError());
			unset($d);
			next($cfg['vocations'][$this->attrs['vocation']]['equipment']);
		}

		//make skills only if not created by trigger
		$this->myQuery('SELECT COUNT(player_skills.skillid) as count FROM player_skills WHERE player_id = '.$this->quote($this->attrs['id']));
		$a = $this->fetch_array();
		$i = 0;
		while ($skill = current($cfg['vocations'][(int)$this->attrs['vocation']]['skills'])){
			$skill_id	= key($cfg['vocations'][(int)$this->attrs['vocation']]['skills']);

			if ($a['count'] == 0){
				if (!$this->myInsert('player_skills',array('player_id' => $this->attrs['id'], 'skillid' => $skill_id, 'value' => $skill, 'count' => 0))) 
					throw new Exception('Player::make() Cannot insert skills:<br/>'.$this->getError());
			}else{
				if (!$this->myUpdate('player_skills',array('value' => $skill),array('player_id' => $this->attrs['id'], 'skillid' => $skill_id))) 
					throw new Exception('Player::make() Cannot update skills:<br/>'.$this->getError());
			}

			next($cfg['vocations'][$this->attrs['vocation']]['skills']);
		}
	return $this->load($this->attrs['id']);
	}

public function repair()
	{global $cfg;
		$lvl = $this->attrs['level'];
		$exp = AAC::getExperienceByLevel($lvl);
		if (!$this->myUpdate('players',array(
			'posx' => $cfg['temple'][$this->attrs['city']]['x'],
			'posy' => $cfg['temple'][$this->attrs['city']]['y'],
			'posz' => $cfg['temple'][$this->attrs['city']]['z']
			/*, 'experience' => $exp*/), array('id' => $this->attrs['id']))) throw new Exception($this->getError());
	}
}
?>