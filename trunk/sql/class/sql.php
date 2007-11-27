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
class SQL{
private $last_query, $last_error, $connection;

public function __construct(){
  $this->_init();
}

//creates new connection
protected function _init(){
	global $cfg;
	if (!isset($this->connection)){
	  	$con = @mysql_connect($cfg['SQL_Server'],$cfg['SQL_User'],$cfg['SQL_Password']);
		if ($con === false){
			throw new Exception('Unable to connect to mysql server. Please make sure it is up and running and you have correct user/password.');
			return false;
		}
		if (!@mysql_select_db($cfg['SQL_Database'])){
			throw new Exception('Unable to select databse: '.$cfg['SQL_Database'].'. Make sure it exists');
			return false;
		}
		$this->connection = $con;
	}
	return true;
}

//Perform simple SQL query
public function myQuery($q){
	$this->last_query = @mysql_query($q);
	if ($this->last_query === false){
		$this->last_error = 'Error #'.mysql_errno()."\n".$q."\n" . mysql_error() . "\n";
		$analysis = analyze();
		if ($analysis !== false)
			throw new Exception($analysis."\n".$this->last_error);
	}
	return $this->last_query;
}

//True is last query failed
public function failed(){
    if ($this->last_query === false) return true;
	return false;
}

//Returns current array with data values
public function fetch_array($resource = null){
    if ($resource === null)
      $resource = $this->last_query;
    if ($resource !== false && $resource !== null)
      return mysql_fetch_array($resource);
     else
      return null;
}

//Returns the last insert id
public function insert_id($resource = null){
      return mysql_insert_id();
}
  
//Returns the number of rows affected
public function num_rows($resource = null)
  {
    if ($resource === null)
      $resource = $this->last_query;
    if ($resource !== false && $resource !== null)
      return mysql_num_rows($resource);
     else
      return null;
  }

//Quotes a string so it's safe to use in SQL statement
public function escape_string($string)
  {
    return mysql_escape_string($string);
  }

//Quotes a string and adds apostrofes
public function quote($string)
  {
    return '\''.mysql_escape_string($string).'\'';
  }

//Return last error
public function getError()
	{
		return $this->last_error;
	}
	
public function analyze()
	{
		$result = @mysql_query('SHOW TABLES');
		while ($a = mysql_fetch_array($result))
			$t[] = $a[0];
		$is_aac_db = in_array('nicaw_accounts',$t);
		$is_server_db = in_array('accounts',$t) && in_array('players',$t);
		$is_svn = in_array('player_depotitems',$t) && in_array('groups',$t);
		$is_cvs = in_array('playerstorage',$t) && in_array('skills',$t);
		if (!$is_aac_db)
			return 'It appears you don\'t have SQL sample imported for AAC';
		elseif (!$is_server_db)
			return 'It appears you don\'t have SQL sample imported for OT server';
		elseif ($is_cvs && !$is_svn)
			return 'This AAC version does not support your server. Consider using SQL v1.5';
		return false;
	}
	
public function repairTables()
	{
		$tables = @mysql_query('SHOW TABLES');
		while ($a = @mysql_fetch_array($tables))
			$tables[] = $a[0];
		if (isset($tables))
			foreach($tables as $table)
				mysql_query('REPAIR TABLE '.$table);
		return $return;
	}

######################################
# Methods for simple  data access    #
######################################

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
	
//Replace data
public function myReplace($table,$data)
	{global $cfg;
		$fields = array_keys($data);
		$values = array_values($data);
		$query = 'REPLACE INTO `'.mysql_escape_string($table).'` (';
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
		$this->myQuery($query);
		if ($this->failed()) return false;
		if ($this->num_rows() <= 0) return false;
		if ($this->num_rows() > 1) throw new Exception('Unexpected SQL answer. More than one row exists.');
		return $this->fetch_array();
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
		$this->myQuery($query);
		if ($this->failed()) return false;
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
		$this->myQuery($query);
		if ($this->failed()) return false;
		return true;
	}
}
?>