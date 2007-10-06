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
private $last_query;
protected $err;
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
      throw new Exception('Connection failed: ' . $e->getMessage().'<br/>Please check your SQL settings');
    }
}

//Perform simpple SQL query
public function myQuery($q){
	if (is_object($this->last_query))
		$this->last_query->closeCursor();
	$this->last_query = $this->PDO->query($q);
	if ($this->last_query === false){
	  $error = $this->PDO->errorInfo();
	  errorLog(print_r($error,true));
	  $this->err = $error[2];
	}
	return $this->last_query;
}

//True is last query failed
public function failed($resource = null)
  {
    if ($this->last_query === false) return true;
	return false;
  }

//Returns current array with data values
public function fetch_array($resource = null)
  {
    if ($resource === null)
      $resource = $this->last_query;
    if ($resource !== false && $resource !== null){
        return $resource->fetch();
      }
     else
      return null;
  }

//Returns the number of rows affected
public function num_rows($resource = null)
  {
    if ($resource === null)
      $resource = $this->last_query;
    if ($resource !== false && $resource !== null)
      return $resource->rowCount();
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
    return $this->PDO->quote($string);
  }

//Return last error
public function getError()
	{
		return $this->err;
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
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
		if ($this->num_rows($sql) == 0) return false;
		return $this->fetch_array($sql);
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
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
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
		$sql = $this->myQuery($query);
		if ($sql === false) return false;
		return true;
	}
}
?>