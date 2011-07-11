<?php 
/*
 ______   ______     ______     __    __     ______     _____    
/\  ___\ /\  == \   /\  __ \   /\ "-./  \   /\  ___\   /\  __-.  
\ \  __\ \ \  __<   \ \  __ \  \ \ \-./\ \  \ \  __\   \ \ \/\ \ 
 \ \_\    \ \_\ \_\  \ \_\ \_\  \ \_\ \ \_\  \ \_____\  \ \____- 
  \/_/     \/_/ /_/   \/_/\/_/   \/_/  \/_/   \/_____/   \/____/ 
                                                                 

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/



/**
 * Database
 * 
 * Simple SQLite3 DB Connect for FrameD
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package FrameD
 */


/**
 * SQLite3Db Class
 * 
 * @package FrameD
 * @subpackage core
 */
class SQLIte3Db {

/**
 * DB Construct
 * 
 * @param string $db File path to SQLite3 Database
 * @return void
 */
   function __construct($db){
	$this->config = new Config();
	$this->db_file = $db;

    $this->logger = new Logger('SQLite3DB');
   }
/**
 * Connect
 * 
 * Opens SQLite3 Database File
 *
 * @access private
 * @return void
 */
   private function connect(){
    $this->db = new PDO('sqlite:'.$this->db_file);

	return;
   }

/**
 * Query
 * 
 * @access public
 * @param string $sql Query to run to return results
 * @return array db rows
 */
   public function query($sql){

	$this->connect();

	$this->logger->trace($sql);
	
	$results = array();

	foreach ($this->db->query($sql, PDO::FETCH_ASSOC) as $row){ 
	  array_push($results, $row);

	}

	if(count($results)){
	   
		return $results;

	} else {
		return false;
	}
   }

/**
 * Execute
 * 
 * @access public
 * @param string $sql Query to run to no return anything such as INSERT or UPDATE
 * @return int 
 */
   public function execute($sql){

	$this->connect();

	$this->logger->trace($sql);

	if(stristr($sql, 'insert')){
		$this->db->exec($sql);

		return $this->db->lastInsertId();
	}

	return $this->db->exec($sql);
   }

/**
 * Insert
 * 
 * @access public
 * @param string $sql Query to run INSERTs and return new ID
 * @return int
 */
   public function insert($sql){
	
	$this->connect();

	$this->logger->trace($sql);

	$ret = $this->db->exec($sql);

	if($ret){
	   return $this->db->lastInsertId();
	} else {
	   $this->logger->error($this->db->errorCode().' : '.print_r($this->db->errorInfo(),1));
	   return;
	}
   }

/**
 * SmartInsert
 * 
 * @access public
 * @param string $table Table name on which you want to query
 * @param array  $data  key/value pair where value is the data and key is the column
 * @return int
 */
   public function smartInsert($table, $data){
	$sql = 'INSERT INTO '.$table.' ('.$this->prepareColumns(array_keys($data),',',$table).') VALUES ('.$this->prepareData($data,$table, ',') .');';

	return $this->insert($sql);
   }

/**
 * SmartSelect
 * 
 * @access public
 * @param string $table   Table name on which you want to query
 * @param array  $select  names of columns you wish to query
 * @param array  $where   key/value pair where value is the column value and the key is the column name
 * @return array db rows
 */
   public function smartSelect($table, $select, $where){
    if($select){
	   $dbSelect = $this->prepareColumns($select, ',');
	} else {
	   $dbSelect = "* ";
	}

	$sql .= 'SELECT '.$dbSelect.' FROM '.$table;

	$dbWhere = $this->prepare($where, $table);

	 if (count($dbwhere) > 0) {
      $sql .= ' WHERE ';

      foreach ($dbwhere as $key => $val) {
        if ($wheresql) { $wheresql .= ' AND '; }
        if (substr($key,-1)=='!') {
          $key = substr($key,0,strlen($key)-1);
          $wheresql .= $key . ' <> ' . $val;
        } else {
          $wheresql .= $key . '=' . $val;
        }
      }

      $sql .= $wheresql;
    }

	$sql .= ';';

	return $this->query($sql);

   }


/**
 * SmartSelectOne
 *  
 * @access public
 * @param string $table   Table name on which you want to query
 * @param array  $select  names of columns you wish to query
 * @param array  $where   key/value pair where value is the column value and the key is the column name
 * @return array db rows
 */
   public function smartSelectOne($table, $select, $where){
    if($select){
	   $dbSelect = $this->prepareColumns($select, ',', $table);
	} else {
	   $dbSelect = "* ";
	}

	$sql .= 'SELECT '.$dbSelect.' FROM '.$table;

	$dbwhere = $this->prepare($where, $table);

	 if (count($dbwhere) > 0) {
      $sql .= ' WHERE ';

      foreach ($dbwhere as $key => $val) {
        if ($wheresql) { $wheresql .= ' AND '; }
        if (substr($key,-1)=='!') {
          $key = substr($key,0,strlen($key)-1);
          $wheresql .= $key . ' <> ' . $val;
        } else {
          $wheresql .= $key . '=' . $val;
        }
      }

      $sql .= $wheresql;
    }

	$sql .= ' LIMIT 1;';

	$ret = $this->query($sql);

    return $ret[0];

   }


/**
 * SmartUpdate
 *  
 * @access public
 * @param string $table   Table name on which you want to query
 * @param array  $data    key/value pair where value is the data and the key is the column name
 * @param array  $where   key/value pair where value is the column value and the key is the column name
 * @return int
 */
   public function smartUpdate($table, $data, $where){
	$dbset = $this->prepare($data, $table);
    $dbwhere = $this->prepare($where, $table);
	$sql = 'UPDATE '.$table.' SET ';

    foreach ($dbset as $key => $val) {
      if ($setqry) { $setqry .= ', '; }
      $setqry .= $key . '=' . $val;
    }

	$sql .= $setqry;

    if (count($dbwhere) > 0) {
      $sql .= ' WHERE ';

      foreach ($dbwhere as $key => $val) {
        if ($wheresql) { $wheresql .= ' AND '; }
        if (substr($key,-1)=='!') {
          $key = substr($key,0,strlen($key)-1);
          $wheresql .= $key . ' <> ' . $val;
        } else {
          $wheresql .= $key . '=' . $val;
        }
      }

      $sql .= $wheresql;
    }

    $sql .= ';';

	return $this->execute($sql);
   }

/**
 * Now
 *  
 * @access public
 * @return datetime
 */
   public function now(){
	return date($this->config->environment['DB']['date_format'],time());
   }

/**
 * PrepareColumns
 *  
 * @access private
 * @param array  $data      key/value pair where value is the data and the key is the column name
 * @param string $delimiter Delimiter used to return data as a string
 * @param string $table     Table name on which you want to query
 * @return string delimited string
 */
   private function prepareColumns($data, $delimiter=',',$table){
      $dbInfo = $this->query('PRAGMA table_info('.$table.');');
	  foreach($data as $column){
		foreach($dbInfo as $info){
         if($column == $info['name']){
		    $columns[] = $this->prepareColumn($column);
		 }
		}
	  }

	  return join($delimiter ,$columns);
   }
/**
 * PrepareColumn
 *  
 * @access private
 * @param string  $data column name
 * @return string       quoted column name
 */
   private function prepareColumn($data){
	return '"'.$data.'"';	
   }

/**
 * Prepare
 *  
 * @access private
 * @param array  $array     key/value pair where value is the data and the key is the column name
 * @param string $table     Table name on which you want to query
 * @return array striped and cleaned data for query
 */
   private function prepare($array, $table){
    $dbInfo = $this->query('PRAGMA table_info('.$table.');');
	if(!$dbInfo){
		$this->logger->error('Table '.$table.' does not exist');
		return;
	}
	if($array){
    foreach($array as $index => $data){
       foreach($dbInfo as $info){
        if($index == $info['name']){
            switch(strtolower($info['type'])){
                case 'varchar':
                case 'text':
                case 'date':
                  $prepared[$index] = $this->prepareString($data);
                break;
                case 'numeric':
                case 'integer':
                  $prepared[$index] = $this->prepareNumber($data);
                break;

                default:
                  $prepared[$index] = $this->prepareString($data);
                break;
            }
            break;
        }
       }

     }
	 }
	 return $prepared;
   }

/**
 * PrepareData
 *  
 * @access private
 * @param array  $array     key/value pair where value is the data and the key is the column name
 * @param string $table     Table name on which you want to query
 * @param string $delimiter Delimiter used to return data as a string
 * @return string delimited string
 */
   private function prepareData($array, $table, $delimiter){
	$dbInfo = $this->query('PRAGMA table_info('.$table.');');
	if(!$dbInfo){
		$this->logger->error('Table '.$table.' does not exist');
		return;
	}
	foreach($array as $index => $data){
	   foreach($dbInfo as $info){
	   	if($index == $info['name']){ 
			switch(strtolower($info['type'])){
				case 'varchar':
				case 'text':
				case 'date':
				  $prepared[$index] = $this->prepareString($data);
				break;
				case 'numeric':
				case 'integer':
				  $prepared[$index] = $this->prepareNumber($data);
				break;

				default:
				  $prepared[$index] = $this->prepareString($data);
				break;
			}
			break;
		}
	   }
	}
	$join = join($delimiter, $prepared);

	return $join;
   }

/**
 * PrepareNumber
 *  
 * @access private
 * @param  int  $number value
 * @return int  cleaned integer
 */
   private function prepareNumber($number){
	return $number+0;
   }

/**
 * PrepareString
 *  
 * @access private
 * @param  string  $string value
 * @return string  quoted string
 */ 
   private function prepareString($string){
	return "'".$string."'";
   }
}
?>
