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
class Guild extends SQL
{
private $attrs;

public function __construct($n)
	{
		//initialize SQL class
		$this->_init();
		$this->attrs['name'] = (string) $n;
	}

public function load()
	{
		$query = 'SELECT players.account_id, guilds.* FROM players, guilds WHERE guilds.ownerid = players.id AND guilds.id = '.$this->quote($this->attrs['name']);
		$this->myQuery($query);
		if ($this->failed())
			throw new Exception('Failed to load guild:<br/>'.$this->getError());
		if ($this->num_rows() > 1)
			throw new Exception('Unexpected SQL answer. More than one guild exists:<br/>'.$this->getError());
		if ($this->num_rows() == 0)
			return false;
		$a = $this->fetch_array();
		//arranging attributes, ones on the left will be used all over the aac
		$this->attrs['id'] = (int) $a['id'];
		$this->attrs['owner_id'] = (int) $acc['ownerid'];
		$this->attrs['owner_acc'] = (string) $acc['account_id'];
		//get members of this guild
		$this->myQuery('SELECT players.rank_id, players.id, players.name, guild_ranks.name AS rank_name, players.guildnick FROM guild_ranks , players WHERE guild_id = '.$this->attrs['id'].' AND players.rank_id = guild_ranks.id ORDER BY guild_ranks.level DESC');
		if ($this->failed())
			throw new Exception('Failed to load guild members:<br/>'.$this->getError());
		while ($a = $this->fetch_array()){
			$this->attrs['data'][$a['rank_id']]['name'] = $a['rank_name'];
			$this->attrs['data'][$a['rank_id']]['members'][$a['id']]['name'] = $a['name'];
			$this->attrs['data'][$a['rank_id']]['members'][$a['id']]['nick'] = $a['guildnick'];
			$this->attrs['player_ids'][] = $a['id'];
			if (!in_array($a['rank_id'], $this->attrs['rank_ids']))
				$this->attrs['rank_ids'][] = $a['rank_id'];
		}
		return true;
	}

public function save()
	{
		$guild['name'] = $this->attrs['name'];
		$guild['ownerid'] = $this->attrs['owner_id'];
		if (isset($this->attrs['id'])){
			if (!$this->myUpdate('guilds', $guild, array('id' => $this->attrs['id'])))
				throw new Exception('Cannot save guild:<br/>'.$this->getError());
		}else{
			$guild['creationdata'] = time();
			if (!$this->myInsert('guilds',$guild))
				throw new Exception('Cannot save guild:<br/>'.$this->getError());
		}
		return true;
	}

public function isMember($id)
	{
		return in_array($id, $this->attrs['player_ids']);
	}
	
public function isRank($id)
	{
		return in_array($id, $this->attrs['rank_ids']);
	}
	
public function addRank($name, $level)
	{
		$rank['guild_id'] = $this->attrs['id'];
		$rank['name'] = $name;
		$rank['level'] = $level;
		if (!$this->myInsert('guild_ranks',$rank))
			throw new Exception('Cannot add guild rank:<br/>'.$this->getError());
		$this->attrs['rank_ids'][] = $this->insert_id();
	}
	
public function dropRank($id)
	{
		if (!$this->isRank($id))
			return false;
		if (!$this->myDelete('guild_ranks', array('id' => $id)))
			throw new Exception('Cannot remove guild rank:<br/>'.$this->getError());
		$this->myQuery('UPDATE `players` SET `rank_id` = 0 WHERE `rank_id` = '.$this->quote($id));
		if ($this->failed())
			throw new Exception('Cannot remove guild rank:<br/>'.$this->getError());
		
		unset($this->attrs['rank_ids'][array_search($id, $this->attrs['rank_ids'])]);
		return true;
	}
	
public function memberInvite($id, $rank)
	{
		//player is already a member - do nothing
		if ($this->isMember($id))
			return false;
		if (!$this->myInsert('nicaw_guild_invites', array('gid' => $this->attrs['id'], 'pid' => $id, 'rank' => $rank)))
			throw new Exception('Cannot invite:<br/>'.$this->getError());
		return true;
	}
	
public function memberRevoke($id)
	{
		if (!$this->isMember($id))
			return false;
		if (!$this->myDelete('nicaw_guild_invites', array('pid' => $id)))
			throw new Exception('Cannot remove invitation:<br/>'.$this->getError());
		return true;
	}

public function memberJoin($id)
	{
		//player is already a member - do nothing
		if ($this->isMember($id))
			return false;
		$invite = $this->myRetrieve('nicaw_guild_invites', array('pid' => $id));
		if ($this->failed())
			throw new Exception($this->getError());
		if (!$this->myUpdate('players', array('rank_id' => $invite['rank'], array('id' => $id))))
			throw new Exception('Cannot join player:<br/>'.$this->getError());		
		$this->memberRevoke($id);
		$this->attrs['player_ids'][] = $id;
		return true;
	}
	
public function memberLeave($id)
	{
		if (!$this->isMember($id))
			return false;
		if (!$this->myUpdate('players', array('rank_id' => $invite['rank'], array('id' => $id))))
			throw new Exception('Cannot join player:<br/>'.$this->getError());
		unset($this->attrs['player_ids'][array_search($id, $this->attrs['player_ids'])]);
		return false;
	}
	
public function playerChangeRank($pid, $rid)
	{
		if (!$this->isMember($pid))
			return false;
		$this->myQuery('UPDATE `players` SET `rank_id` = '.$this->quote($rid).' WHERE `id` = '.$this->qoute($pid));
		if ($this->failed())
			throw new Exception('Cannot remove guild rank:<br/>'.$this->getError());
		if ($this->num_rows() != 1)
			throw new Exception('Unexpected row count.');
		return true;
	}

public function getAttr($attr)
	{
		return $this->attrs[$attr];
	}

public function setAttr($attr,$value)
	{
		$this->attrs[$attr] = $value;
	}

public function exists()
	{
		$this->myQuery('SELECT * FROM `guilds` WHERE `name` = '.$this->quote($this->attrs['name']));
		if ($this->failed()) throw new Exception('Guild::exists() cannot determine whether guild exists');
		if ($this->num_rows($sql) > 0) return true;
		return false;
	}

public function isValidName($n)
	{global $cfg;
		foreach ($cfg['invalid_names'] as $name)
			if (eregi($name,$n))
				return false;
		return preg_match($cfg['name_format'],$n)
		&& strlen($n) <= 30 && strlen($n) >= 4;
	}
}
?>