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
public $player;

public function __construct($n)
	{
		if (is_numeric($n) && $n != 0){
			$this->attrs['accno'] = (int) $n;
			return true;
		}else{
			$this->err = 'Invalid account number';
			return false;
		}
	}

public function load()
	{
		if (empty($this->attrs['accno']) || $this->attrs['accno'] == 0){
			$this->err = 'Invalid account number';
			return false;
		}
		//load attributes from database
		$acc = $this->myRetrieve('accounts', array('id' => $this->attrs['accno']));
		$nicaw_acc = $this->myRetrieve('nicaw_accounts', array('accno' => $this->attrs['accno']));
		if ($acc === false) return false;
		//arranging attributes, ones on the left will be used all over the aac
		$this->attrs['accno'] = (int) $acc['id'];
		$this->attrs['password'] = (string) $acc['password'];
		$this->attrs['rlname'] = (string) $nicaw_acc['rlname'];
		$this->attrs['location'] = (string) $nicaw_acc['location'];
		$this->attrs['blocked'] = (bool) $nicaw_acc['blocked'];
		$this->attrs['ip'] = (string) $nicaw_acc['ip'];
		$this->attrs['email'] = (string) $nicaw_acc['email'];
		$this->attrs['comment'] = (string) $nicaw_acc['comment'];
		//get characters of this account
		$query = 'SELECT * FROM `players` WHERE (`account_id`=\''.mysql_escape_string($this->attrs['accno']).'\')';
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
		while ($a = mysql_fetch_array($sql)){
			$this->player[$a['name']] = new Player($a['name']);
		}
		//good, we have all attributes stored in class
		return true;
	}

public function save()
	{
		$acc['id'] = $this->attrs['accno'];
		$acc['password'] = $this->attrs['password'];
		$nicaw_acc['accno'] = $this->attrs['accno'];
		$nicaw_acc['rlname'] = $this->attrs['rlname'];
		$nicaw_acc['location'] = $this->attrs['location'];
		$nicaw_acc['blocked'] = $this->attrs['blocked'];
		$nicaw_acc['ip'] = $this->attrs['ip'];
		$nicaw_acc['email'] = $this->attrs['email'];
		$nicaw_acc['comment'] = $this->attrs['comment'];
		//insert into accounts table
		if ($this->myUpdate('accounts',$acc,array('id' => $this->attrs['accno']))){
			//insert into nicaw_accounts table
			if ($this->myUpdate('nicaw_accounts',$nicaw_acc,array('accno' => $this->attrs['accno']))) return true;
		}
		return false;		
	}

public function make()
	{global $cfg;
		
		if ($this->exists()){
			$this->err = 'Account cannot be created because it already exists';
			return false;
		}
		
		$acc['id'] = $this->attrs['accno'];
		$acc['password'] = $this->attrs['password'];
		$nicaw_acc['accno'] = $this->attrs['accno'];
		$nicaw_acc['email'] = $this->attrs['email'];
		$nicaw_acc['rlname'] = $this->attrs['rlname'];
		$nicaw_acc['location'] = $this->attrs['location'];
		$nicaw_acc['ip'] = $_SERVER['REMOTE_ADDR'];

		if (!$this->myInsert('accounts',$acc)) return false;
		if (!$this->myInsert('nicaw_accounts',$nicaw_acc)) return false;

		return $this->load();
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

public function getCharCount()
	{
		return count($this->player);
	}

public function getCharList()
	{
	if (isset($this->player))
		return array_keys($this->player);
	else return null;
	}

public function addCharacter($name)
	{
		$this->player[$name] = new Player($name);
	}

public function exists()
	{
		$sql = $this->myQuery('SELECT * FROM `accounts` WHERE `id` = '.mysql_escape_string($this->attrs['accno']));
		if ($sql === false) return false;
		if (mysql_num_rows($sql) == 0) return false;
		else return true;
	}

public function isValidNumber()
	{
		return ereg('^[0-9]{6,8}$',$this->attrs['accno']);
	}

public function logAction($action)
	{
		return $this->myInsert('nicaw_logs',array('id' => NULL, 'ip' => $_SERVER['REMOTE_ADDR'], 'account' => $this->attrs['accno'], 'date' => time(), 'action' => $action));
	}

public function getLogs($limit)
	{
		$result = $this->myQuery('SELECT * FROM `nicaw_logs` WHERE `account` = '.mysql_escape_string($this->attrs['accno']).' ORDER BY `date` DESC LIMIT '.mysql_escape_string($limit));
		if ($result !== false){
			while ($row = mysql_fetch_array($result)) $logs[] = $row;
			return $logs;
		}
		return false;
	}

public function highestLevel()
	{
		$sql = $this->myQuery('SELECT MAX(level) FROM `players` WHERE `account_id` = '.mysql_escape_string($this->attrs['accno']));
		$row = mysql_fetch_array($sql);
		return $row['MAX(level)'];
	}

public function canVote($id)
	{
		$query = 'SELECT * FROM nicaw_votes WHERE `id` = '.$id.' AND (`accno`=\''.mysql_escape_string($this->attrs['accno']).'\' OR `ip` =\''.$_SERVER['REMOTE_ADDR'].'\')';
		$sql = $this->myQuery($query);
		if (mysql_num_rows($sql) >= 1)
			return false;
		$poll = $this->myRetrieve('nicaw_polls', array('id' => $id));
		if ($poll === false) return false;
		return $poll['minlevel'] <= $this->highestLevel() && $poll['startdate'] < time() && $poll['enddate'] > time() && !isset($_COOKIE['poll'.$id]);
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
	}

public function removeRecoveryKey()
	{
		if ($this->myDelete('nicaw_recovery',array('accno' => $this->attrs['accno']),100))  
			return true;
		else 
			return false;
	}

public function addRecoveryKey()
	{
		$key = substr(str_shuffle(md5(mt_rand()).md5(mt_rand())), 0, 32);
		$d['accno'] = $this->attrs['accno'];
		$d['email'] = $this->attrs['email'];
		$d['date'] = time();
		$d['ip'] = $_SERVER['REMOTE_ADDR'];
		$d['key'] = $key;

		$this->removeRecoveryKey();
		if (!$this->myInsert('nicaw_recovery',$d)) return false;
		return $key;
	}

public function checkRecoveryKey($key)
	{
		if (empty($key)) return false;
		$sql = $this->myRetrieve('nicaw_recovery',array('accno' => $this->attrs['accno'], 'key' => $key));
		if ($sql === false) 
			return false;
		else
			return true;
	}
}
?>