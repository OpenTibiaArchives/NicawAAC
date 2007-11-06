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
private $last_query, $last_error;
public $PDO;

public function __construct(){
  $this->_init();
}

//creates new PDO object for database access
protected function _init(){
  global $cfg;
  if (!isset($this->PDO))
    try {
      if ($cfg['DB_Type'] == 'mysql')
        $this->PDO = new PDO('mysql:dbname='.$cfg['SQL_Database'].';host='.$cfg['SQL_Server'], $cfg['SQL_User'], $cfg['SQL_Password']);
      elseif ($cfg['DB_Type'] == 'sqlite2')
        $this->PDO = new PDO('sqlite2:'.$cfg['dirdata'].$cfg['SQL_filename']);
      elseif ($cfg['DB_Type'] == 'sqlite')
        $this->PDO = new PDO('sqlite:'.$cfg['dirdata'].$cfg['SQL_filename']);
      else
        $this->PDO = new PDO('uri:file://'.$cfg['dirdata'].$cfg['SQL_dnsfile'], $cfg['SQL_User'], $cfg['SQL_Password']);
    } catch (PDOException $e) {
      throw new Exception('Connection failed: ' . $e->getMessage().'<br/>Please check your SQL settings!');
    }
}

//Perform simpple SQL query
public function myQuery($q){
	if (is_object($this->last_query))
		$this->last_query->closeCursor();
	$this->last_query = $this->PDO->query($q);
	if ($this->last_query === false){
		$error = $this->PDO->errorInfo();
		$this->last_error = $q."<br/>\n".$error[2].'<br/>'.$this->analyze();
	}
	return $this->last_query;
}

//True is last query failed
public function failed()
  {
    if ($this->last_query === false) return true;
	return false;
  }

//Returns current array with data values
public function fetch_array()
  {
	if ($this->last_query !== false && $this->last_query !== null){
        return $this->last_query->fetch();
    }else
		throw new Exception('No valid resource for SQL::fetch_array()');
  }

//Returns the number of rows affected
public function num_rows()
  {
      return $this->last_query->rowCount();
  }

//Quotes a string so it's safe to use in SQL statement
public function escape_string($string)
  {
    return mysql_escape_string($string);
  }

//Quotes a string and adds apostrofes
public function quote($string)
  {
    return $this->PDO->quote($string);
  }

//Return last error
public function getError()
	{
		return $this->last_error;
	}
	
public function analyze()
	{
		$result = $this->PDO->query('SHOW TABLES');
		while ($a = $result->fetch())
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
	}
	
public function repairTables()
	{
		$this->myQuery('SHOW TABLES');
		while ($a = $this->fetch_array())
			$tables[] = $a[0];
		if (isset($tables) && !$this->failed())
			foreach($tables as $table){
				$this->myQuery('REPAIR TABLE '.$table);
				if ($this->failed()){
					echo 'Cannot repair '.$table.'<br/>';
					return false;
				}else{
					$a = $this->fetch_array();
					$return .= $a[0].' '.$a[1].' '.$a[2].' '.$a[3].'<br/>';
				}
			}
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
		$query = 'INSERT INTO `'.$this->escape_string($table).'` (';
		foreach ($fields as $field)
			$query.= '`'.$this->escape_string($field).'`,';
		$query = substr($query, 0, strlen($query)-1);
		$query.= ') VALUES (';
		foreach ($values as $value)
			if ($value === null)
				$query.= 'NULL,';
			else
				$query.= '\''.$this->escape_string($value).'\',';
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
		$query = 'REPLACE INTO `'.$this->escape_string($table).'` (';
		foreach ($fields as $field)
			$query.= '`'.$this->escape_string($field).'`,';
		$query = substr($query, 0, strlen($query)-1);
		$query.= ') VALUES (';
		foreach ($values as $value)
			if ($value === null)
				$query.= 'NULL,';
			else
				$query.= '\''.$this->escape_string($value).'\',';
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
		$query = 'SELECT * FROM `'.$this->escape_string($table).'` WHERE (';
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.$this->escape_string($fields[$i]).'` = \''.$this->escape_string($values[$i]).'\' AND ';
		$query = substr($query, 0, strlen($query)-4);
		$query.=');';
		$results = $this->myQuery($query);
		$array = $results->fetchAll();
		if ($this->failed()) return false;
		if (count($array) <= 0) return false;
		if (count($array) > 1) throw new Exception('Unexpected SQL answer. More than one row exists.');
		return $array[0];
	}

//Update data
public function myUpdate($table,$data,$where,$limit=1)
	{
		$fields = array_keys($data); 
		$values = array_values($data);
		$query = 'UPDATE `'.$this->escape_string($table).'` SET ';
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.$this->escape_string($fields[$i]).'` = \''.$this->escape_string($values[$i]).'\', ';
		$query = substr($query, 0, strlen($query)-2);
		$query.=' WHERE (';
		$fields = array_keys($where); 
		$values = array_values($where);
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.$this->escape_string($fields[$i]).'` = \''.$this->escape_string($values[$i]).'\' AND ';
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
		$query = 'DELETE FROM `'.$this->escape_string($table).'` WHERE (';
		for ($i = 0; $i < count($fields); $i++)
			$query.= '`'.$this->escape_string($fields[$i]).'` = \''.$this->escape_string($values[$i]).'\' AND ';
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