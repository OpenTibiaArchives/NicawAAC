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
		$this->attrs['members'] = array();
		$this->attrs['invited'] = array();
	}

public function load()
	{
		$query = 'SELECT players.account_id, guilds.* FROM players, guilds WHERE guilds.ownerid = players.id AND guilds.name = '.$this->quote($this->attrs['name']);
		$this->myQuery($query);
		if ($this->failed())
			throw new Exception('Failed to load guild:<br/>'.$this->getError());
		if ($this->num_rows() > 1)
			throw new Exception('Unexpected SQL answer. More than one guild exists:<br/>'.$this->getError());
		if ($this->num_rows() == 0)
			return false;
		$a = $this->fetch_array();
		$this->attrs['id'] = (int) $a['id'];
		$this->attrs['owner_id'] = (int) $a['ownerid'];
		$this->attrs['owner_acc'] = (string) $a['account_id'];
		
		//get invited members
		$this->myQuery('SELECT players.id, players.name, players.guildnick, guild_ranks.name AS rank FROM players, guild_ranks, nicaw_guild_invites WHERE players.id = nicaw_guild_invites.pid AND guild_ranks.id = nicaw_guild_invites.rank AND nicaw_guild_invites.gid = '.$this->attrs['id']);
		if ($this->failed())
			throw new Exception('Failed to load invited members:<br/>'.$this->getError());

		while ($a = $this->fetch_array())
			$this->attrs['invited'][] = array('id' => $a['id'], 'name' => $a['name'], 'rank' => $a['rank']);
		
		//get members
		$this->myQuery('SELECT players.name, players.guildnick, players.id, guild_ranks.name AS rank FROM guild_ranks, players WHERE players.rank_id = guild_ranks.id AND guild_ranks.guild_id = '.$this->attrs['id']);
		if ($this->failed())
			throw new Exception('Failed to load guild members:<br/>'.$this->getError());

		while ($a = $this->fetch_array())
			$this->attrs['members'][] = array('id' => $a['id'], 'name' => $a['name'], 'rank' => $a['rank'], 'nick' => $a['guildnick']);

		return true;
	}
	
public function save()
	{global $cfg;
		if (!$cfg['guild_manager_enabled']) return false;
		if (!isset($this->attrs['id'])){
			if ($this->exists()) throw new Exception('Trying to insert guild which already exists.');
			$this->myInsert('guilds', array('name' => $this->attrs['name'], 'ownerid' => $this->attrs['owner_id'], 'creationdata' => time()));
			$this->attrs['id'] = $this->insert_id();
		}else{
			$this->myUpdate('guilds', array('name' => $this->attrs['name'], 'ownerid' => $this->attrs['owner_id'], 'creationdata' => time()), array('id' => $this->attrs['id']));
		}
		
		//deleted old data first
		$this->myQuery('DELETE FROM nicaw_guild_invites WHERE gid = '.(int)$this->attrs['id']);
		$this->myQuery('DELETE FROM guild_ranks WHERE guild_id = '.(int)$this->attrs['id']);
		$this->myQuery('UPDATE players SET rank_id = 0 WHERE players.rank_id = guild_ranks.id AND guild_ranks.guild_id = '.(int)$this->attrs['id']);
		
		//save ranks (case sensitive)
		$unique_ranks = array();
		foreach ($this->attrs['members'] as $member)
			if (!array_key_exists($member['rank'], $unique_ranks)){
				if (!$this->myInsert('guild_ranks', array('guild_id' => $this->attrs['id'], 'name' => $member['rank'], 'level' => 1)))
					throw new Exception('Cannot save guild:<br/>'.$this->getError());
				$unique_ranks[$member['rank']] = $this->insert_id();
			}
		foreach ($this->attrs['invited'] as $member)
			if (!array_key_exists($member['rank'], $unique_ranks)){
				if (!$this->myInsert('guild_ranks', array('guild_id' => $this->attrs['id'], 'name' => $member['rank'], 'level' => 1)))
					throw new Exception('Cannot save guild:<br/>'.$this->getError());
				$unique_ranks[$member['rank']] = $this->insert_id();
			}
		
		//save/update players
		foreach ($this->attrs['invited'] as $member){
			if (!$this->myInsert('nicaw_guild_invites', array('gid' => $this->attrs['id'], 'pid' => $member['id'], 'rank' => $unique_ranks[$member['rank']])))
				throw new Exception('Cannot save guild:<br/>'.$this->getError());
		}
		foreach ($this->attrs['members'] as $member){
			if (!$this->myUpdate('players', array('rank_id' => $unique_ranks[$member['rank']], 'guildnick' => $member['nick']), array('id' =>  $member['id'])))
				throw new Exception('Cannot save guild:<br/>'.$this->getError());
		} 
		//phew, saving done
		return true;
	}

public function isNameInvited($name)
	{
		return deeper_array_search($name, $this->attrs['invited'], 'name') !== false;
	}
	
public function isIdInvited($id)
	{
		return deeper_array_search($id, $this->attrs['invited'], 'id') !== false;
	}
	
public function isNameMember($name)
	{
		return deeper_array_search($name, $this->attrs['members'], 'name') !== false;
	}

public function isIdMember($id)
	{
		return deeper_array_search($id, $this->attrs['members'], 'id') !== false;
	}
	
public function memberInvite($id, $rank)
	{
		//player is already a member - do nothing
		if ($this->isIdMember($id) || $this->isIdInvited($id))
			return false;
		$this->myQuery('SELECT name FROM players WHERE id = '.(int)$id);
		if ($this->failed()) 
			throw new Exception($this->getError());
		if ($this->num_rows() != 1)
			throw new Exception('Cannot retrieve player name');
		$a = $this->fetch_array();
		$this->attrs['invited'][] = array('id' => $id, 'name' => $a['name'], 'rank' => $rank);
		return true;
	}
	
public function memberJoin($id, $level, $rank = null)
	{
		
		if ($this->isIdInvited($id)){
			$invite_key = deeper_array_search($id, $this->attrs['invited'], 'id');
			$this->attrs['members'][] = $this->attrs['invited'][$invite_key];
			unset($this->attrs['invited'][$invite_key]);
			return true;
		}elseif (!$this->isIdMember($id) && $rank != null){
			$this->myQuery('SELECT name FROM players WHERE id = '.(int)$id);
			if ($this->failed()) 
				throw new Exception($this->getError());
			if ($this->num_rows() != 1)
				throw new Exception('Cannot retrieve player name');
			$a = $this->fetch_array();
			$this->attrs['members'][] = array('id' => $id, 'name' => $a['name'], 'rank' => $rank, 'level' => $level);
			return true;
		}
		return false;
	}
	
public function memberLeave($id)
	{
		if ($this->isIdMember($id)){
			$key = deeper_array_search($id, $this->attrs['members'], 'id');
			unset($this->attrs['members'][$key]);
			return true;
		}elseif ($this->isIdInvited($id)){
			$key = deeper_array_search($id, $this->attrs['invited'], 'id');
			unset($this->attrs['invited'][$key]);
			return true;
		}
		return false;
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

public function countInvited()
	{
		return count($this->attrs['invited']);
	}

public function countMember()
	{
		return count($this->attrs['members']);
	}
	
public function remove()
	{
		$this->myQuery('DELETE FROM guilds WHERE id = '.(int)$this->attrs['id']);
		$this->myQuery('DELETE FROM nicaw_guild_invites WHERE gid = '.(int)$this->attrs['id']);
		$this->myQuery('DELETE FROM guild_ranks WHERE guild_id = '.(int)$this->attrs['id']);	
	}

public function memberNameSetRank($name, $rank)
	{
		$key = deeper_array_search($name, $this->attrs['members'], 'name');
		if ($key === false) throw new Exception('Member not found');
		$this->attrs['members'][$key]['rank'] = $rank;
	}
	
public function memberNameSetNick($name, $nick)
	{
		$key = deeper_array_search($name, $this->attrs['members'], 'name');
		if ($key === false) throw new Exception('Member not found');
		$this->attrs['members'][$key]['nick'] = $nick;
	}
}
?>