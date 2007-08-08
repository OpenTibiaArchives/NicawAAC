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
class MySQL{
protected $err;

//Establish persistent connection to MySQL server and select database
protected function myConnect()
	{global $cfg;
		$con = @mysql_pconnect($cfg['SQL_Server'],$cfg['SQL_User'],$cfg['SQL_Password']);
		if ($con === false){
			$this->err = 'Unable to connect to MySQL server';
			return false;
		}
		if (!@mysql_select_db($cfg['SQL_Database'])){
			$this->err = 'Unable to select database';
			return false;
		}
		return true;
	}

//Perform SQL query
public function myQuery($q)
	{
		if (!$this->myConnect()) return false;
		$sql = @mysql_query($q);
		if ($sql === false){
			errorLog('#'.mysql_errno()."\r\n".$q."\r\n" . mysql_error() . "\r\n");
			$this->err = 'Query failed. Refer to errors.inc for details.';
			return false;
		}
		return $sql;
	}

/*
Functions to replace SQL syntax
Input data structure
array('col1' => 'value1', 'col2' => 'value2')
*/
//Insert data
public function myInsert($table,$data)
	{global $cfg;
		$fields = array_keys($data);
		$values = array_values($data);
		$query = 'INSERT INTO `'.mysql_escape_string($table).'` (';
		foreach ($fields as $field)
			$query.= '`'.mysql_escape_string($field).'`,';
		$query = substr($query, 0, strlen($query)-1);
		$query.= ') VALUES (';
		foreach ($values as $value)
			if ($value === null)
				$query.= 'NULL,';
			else
				$query.= '\''.mysql_escape_string($value).'\',';
		$query = substr($query, 0, strlen($query)-1);
		$query.= ');';
		if ($this->myQuery($query) === false) 
			return false;
		else
			return true;

	}

//Retrieve single row
public function myRetrieve($table,$data)
	{
		$fields = array_keys($data); 
		$values = array_values($data);
		$query = 'SELECT * FROM `'.mysql_escape_string($table).'` WHERE (';
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.mysql_escape_string($fields[$i]).'` = \''.mysql_escape_string($values[$i]).'\' AND ';
		$query = substr($query, 0, strlen($query)-4);
		$query.=');';
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
		if (mysql_num_rows($sql) != 1) return false;
		return mysql_fetch_array($sql);
	}

//Update data
public function myUpdate($table,$data,$where,$limit=1)
	{
		$fields = array_keys($data); 
		$values = array_values($data);
		$query = 'UPDATE `'.mysql_escape_string($table).'` SET ';
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.mysql_escape_string($fields[$i]).'` = \''.mysql_escape_string($values[$i]).'\', ';
		$query = substr($query, 0, strlen($query)-2);
		$query.=' WHERE (';
		$fields = array_keys($where); 
		$values = array_values($where);
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.mysql_escape_string($fields[$i]).'` = \''.mysql_escape_string($values[$i]).'\' AND ';
		$query = substr($query, 0, strlen($query)-4);
		$query.=') LIMIT '.$limit.';';
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
		return true;
	}

//Delete data
public function myDelete($table,$data,$limit = 1)
	{
		$fields = array_keys($data); 
		$values = array_values($data);
		$query = 'DELETE FROM `'.mysql_escape_string($table).'` WHERE (';
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.mysql_escape_string($fields[$i]).'` = \''.mysql_escape_string($values[$i]).'\' AND ';
		$query = substr($query, 0, strlen($query)-4);
		if ($limit > 0)
			$query.=') LIMIT '.$limit.';';
		else
			$query.=');';
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
		return true;
	}

public function getError()
	{
		return $this->err;
	}
}
?>