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
class Account extends SQL
{
private $attrs;
public $players;

public function __construct($n)
	{
		//initialize SQl class
		$this->_init();
		if (is_numeric($n) && $n > 0){
			$this->attrs['accno'] = (int) $n;
		}
	}

public function load()
	{
		if (empty($this->attrs['accno']) || $this->attrs['accno'] == 0)
			return false;
		//load attributes from database
		$acc = $this->myRetrieve('accounts', array('id' => $this->attrs['accno']));
		$nicaw_acc = $this->myRetrieve('nicaw_accounts', array('account_id' => $this->attrs['accno']));
		if ($acc === false){
			if ($this->exists())
				throw new Exception('Cannot load existing account:<br/>'.$this->getError());
			return false;
		}
		//arranging attributes, ones on the left will be used all over the aac
		$this->attrs['accno'] = (int) $acc['id'];
		$this->attrs['password'] = (string) $acc['password'];
		$this->attrs['email'] = (string) $acc['email'];
		$this->attrs['rlname'] = $nicaw_acc['rlname'];
		$this->attrs['location'] = $nicaw_acc['location'];
		$this->attrs['comment'] = $nicaw_acc['comment'];
		$this->attrs['recovery_key'] = $nicaw_acc['recovery_key'];
		//get characters of this account
		$this->myQuery('SELECT players.name FROM players WHERE (`account_id`='.$this->quote($this->attrs['accno']).')');
		if ($this->failed()) throw new Exception($this->getError());
		while ($a = $this->fetch_array()){
			$this->players[] = new Player($a['name']);
		}
		//good, now we have all attributes stored in object
		return true;
	}

public function save()
	{
		if (empty($this->attrs['accno']) || $this->attrs['accno'] == 0)
			throw new Exception('Account::save() - Invalid account number');
		$acc['id'] = $this->attrs['accno'];
		$acc['password'] = $this->attrs['password'];
		$acc['email'] = $this->attrs['email'];
		$nicaw_acc['account_id'] = $this->attrs['accno'];
		$nicaw_acc['rlname'] = $this->attrs['rlname'];
		$nicaw_acc['location'] = $this->attrs['location'];
		$nicaw_acc['comment'] = $this->attrs['comment'];
		$nicaw_acc['recovery_key'] = $this->attrs['recovery_key'];

		if (!$this->myReplace('nicaw_accounts',$nicaw_acc))
			throw new Exception('Cannot save account:<br/>'.$this->getError());
		if (!$this->myReplace('accounts',$acc))
			throw new Exception('Cannot save account:<br/>'.$this->getError());
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

public function setPassword($new)
	{global $cfg;
		if ($cfg['md5passwords']){
			$new = md5($new.$cfg['md5_salt']);
		}
		$this->attrs['password'] = $new;
	}

public function checkPassword($p)
	{global $cfg;
		if ($cfg['md5passwords']){
			$p = md5($p.$cfg['md5_salt']);
		}
		return $this->attrs['password'] == $p;
	}

public function exists()
	{
		$this->myQuery('SELECT * FROM `accounts` WHERE `id` = '.$this->quote($this->attrs['accno']));
		if ($this->failed()) throw new Exception('Account::exists() cannot determine whether account exists');
		if ($this->num_rows($sql) > 0) return true;
		return false;
	}

public function isValidNumber()
	{
		return ereg('^[0-9]{6,8}$',$this->attrs['accno']);
	}

public function logAction($action)
	{
		if (!$this->myInsert('nicaw_account_logs',array('id' => NULL, 'ip' => ip2long($_SERVER['REMOTE_ADDR']), 'account_id' => $this->attrs['accno'], 'date' => time(), 'action' => $action)))
			throw new Exception($this->getError());
	}
	
public function removeRecoveryKey()
	{
		$this->attrs['recovery_key'] = NULL;
	}

public function addRecoveryKey()
	{
		$this->attrs['recovery_key'] = substr(str_shuffle(md5(mt_rand()).md5(time())), 0, 32);
		$this->logAction('Recovery key added');
		return $this->attrs['recovery_key'];
	}

public function checkRecoveryKey($key)
	{
		return $this->attrs['recovery_key'] === $key && !empty($key);
	}

public function vote($option)
	{
		if(!$this->myInsert('nicaw_poll_votes',array('option_id' => $option, 'ip' => ip2long($_SERVER['REMOTE_ADDR']), 'account_id' => $this->attrs['accno'])))
			throw new Exception('It appears you didn\'t import database.sql for AAC:<br/>'.$this->getError());
	}
	
public function getMaxLevel()
	{
		$this->myQuery('SELECT MAX(level) FROM `players` WHERE `account_id` = '.$this->escape_string($this->attrs['accno']));
		if ($this->failed())
			throw new Exception($this->getError);
		$row = $this->fetch_array();
		return (int) $row['MAX(level)'];
	}

public function canVote($option)
	{
		$query = 'SELECT *
FROM (
SELECT MAX(level) AS maxlevel
FROM players
WHERE account_id = '.$this->quote($this->attrs['accno']).'
) AS a1, nicaw_polls, nicaw_poll_options
WHERE nicaw_polls.id = nicaw_poll_options.poll_id
AND nicaw_poll_options.id = '.$this->quote($option).'
AND maxlevel > minlevel
AND nicaw_polls.startdate < UNIX_TIMESTAMP(NOW())
AND nicaw_polls.enddate > UNIX_TIMESTAMP(NOW())
AND NOT EXISTS (
SELECT *
FROM (
SELECT nicaw_polls.id
FROM nicaw_poll_options, nicaw_polls
WHERE nicaw_poll_options.poll_id = nicaw_polls.id
AND nicaw_poll_options.id = '.$this->quote($option).'
) AS a1, nicaw_poll_options, nicaw_poll_votes
WHERE a1.id = nicaw_poll_options.poll_id
AND nicaw_poll_options.id = nicaw_poll_votes.option_id
AND (account_id = '.$this->quote($this->attrs['accno']).' OR ip = '.ip2long($_SERVER['REMOTE_ADDR']).')
)';
		$this->myQuery($query);
		if ($this->failed())
			throw new Exception($this->getError);
		if ($this->num_rows() == 0) return false;
		elseif ($this->num_rows() == 1) return true;
		else throw new Exception('Unexpected SQL answer.');
	}
/*public function getLogs($limit)
	{
		$result = $this->myQuery('SELECT * FROM `nicaw_logs` WHERE `account` = '.$this->escape_string($this->attrs['accno']).' ORDER BY `date` DESC LIMIT '.$this->escape_string($limit));
		if ($result !== false){
			while ($row = $this->fetch_array($result)) $logs[] = $row;
			return $logs;
		}
		return false;
	}

public function doVote($id,$option)
	{
		$result = $this->myRetrieve('nicaw_polls', array('id' => $id));
		if ($result === false) return false;
		$values = explode(";",$result['results']);
		$values[$option]++;
		$result = implode(";",$values);
		$success = $this->myUpdate('nicaw_polls',array('results' => $result),array('id' => $id));
		if (!$success) return false;
		$success = $this->myInsert('nicaw_votes', array('id' => $id, 'accno' => $this->attrs['accno'], 'ip' => $_SERVER['REMOTE_ADDR']));
		if (!$success) return false;
		return true;
	}*/
}
?>