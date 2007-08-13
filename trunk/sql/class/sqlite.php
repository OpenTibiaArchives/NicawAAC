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
class SQL_engine{
protected $err;
private $last_query;

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
		$this->last_query = @mysql_query($q);
		if ($this->last_query === false){
			errorLog('#'.mysql_errno()."\r\n".$q."\r\n" . mysql_error() . "\r\n");
			$this->err = 'Query failed. Refer to errors.inc for details.';
			return false;
		}
		return $this->last_query;
	}
	
public function fetch_array($resource = null)
  {
    if ($resource === null)
      $resource = $this->last_query;
    if ($resource !== false && $resource !== null)
      return mysql_fetch_array($resource);
     else
      return null;
  }
  
public function num_rows($resource = null)
  {
    if ($resource === null)
      $resource = $this->last_query;
    if ($resource !== false && $resource !== null)
      return mysql_num_rows($resource);
     else
      return null;
  }
}
?>