<?php

//ini_set('display_errors',1);
//error_reporting(-1);

function exception_handler($exception)
{
	echo $exception;
}

set_exception_handler('exception_handler');

class BaseService
{
	
	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	
	var $connection;
	/**
	 * The constructor initializes the connection to database. Everytime a request is 
	 * received by Zend AMF, an instance of the service class is created and then the
	 * requested method is invoked.
	 */
	
	public function __construct() {
		// use the .ini file to set up the connection to this database
		$con_vars = parse_ini_file(SRC.'/config.ini');
		foreach($con_vars as $key=>$val) {
			switch($key) {
				case 'dbname':
					$this->databasename = $val;
					break;
				case 'user':
					$this->username = $val;
					break;
				case 'pass':
					$this->password = $val;
					break;
				case 'server':
					$this->server = $val;
					break;
				case 'port':
					$this->port = $val;
					break;
			}
		}
		
		//$pdostatement = "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=".$this->databasename.";";
		$pdostatement = "mysql:dbname=".$this->databasename.";dbhost=127.0.0.1;port=".$this->port;
		$this->connection = new PDO($pdostatement,$this->username,$this->password);//,array(PDO::MYSQL_ATTR_READ_DEFAULT_FILE=>'/etc/my.cnf'));
		$this->throwExceptionOnError($this->connection);
	}
	
	public function __destruct() {
		$this->connection = null;
	}
	
	/*
	*
	* Returns a single result based on a query sent by the table service call.
	*
	*/
	public function sendAndGetOne($query) {
		
		$res = $this->dbquery($query);
		
		if(count($res)>0) {
			return $res[0];
		} else {
	      return null;
		}
	}
	
	/*
	*
	*	Returns an array of results based on a query sent by the table service call.
	*
	*/
	public function sendAndGetMany($query) {
		
		$res = $this->dbquery($query);
		
		$rows = array();
		
		foreach ($res as $row) {
			$rows[] = $row;
		}
		return $res;
	}
	
	/*
	*
	*	Returns the last, single object inserted into the database, confirming the insertion
	*
	*/
	public function insertAndGetOne($query) {
		
		return( $this->dbinsert($query) );
		
	}
	
	/*
	*
	* Inserts many objects into the database based on members of an array of associative arrays.
	* Associative array requires value to be the column name and value to be the value to insert.
	* Constructs the query during each loop of the foreach statement.
	*
	*/
	public function insertAndGetMany($array,$table) {
		$ret = array();
		for($i=0;$i<count($array);$i++) {
			$sql = "INSERT INTO".$table." (";
			$x = 0;
			$t = count($array[$i])-1;
			foreach($array[$i] as $key=>$val) {
				if($x<$t) {
					$sql .= $key.",";
				} else {
					$sql .= $key.")";
				}
			}
			$sql .= " VALUES ";
			foreach($array[$i] as $key=>$val) {
				if($x<$t) {
					$sql .= $val.",";
				} else {
					$sql .= $val.")";
				}
			}
			$res = $this->sendAndGetOne("SELECT * FROM $table WHERE id='$id'");
			array_push($ret, $res[0]);
		}
		return( $ret );
	}
	
	/*
	*
	* Sends the command to delete an item 
	*
	*/
	public function sendAndDelete($query) {
		$this->dbquery($query);
	}
	
	// ret modes: MDB2_FETCHMODE_OBJECT, MDB2_FETCHMODE_ASSOC, MDB2_FETCHMODE_ORDERED
	protected function dbquery($query)
	{
		try {
			$res = $this->connection->query($query);
			$res->setFetchMode(PDO::FETCH_OBJ);
			return($res->fetchAll());
		} catch(PDOException $e) {
			$err = $e->errorInfo();
			throw new Exception($e->getMessage());
		}
	}
	
	protected function dbinsert($query)
	{
		try {
			$res = $this->connection->prepare($query);
			$res->execute();
			$id = $this->connection->lastInsertId();
			return( $id );
		} catch(PDOException $e) {
			$err = $e->errorInfo();
			throw new Exception($e->getMessage());
		}
	}
	
	function _describe($table) {
		$query = "SHOW COLUMNS FROM ".$table;
		$res = $this->dbquery($query);
		$columns = array();
		foreach($res AS $c) {
			$col = new stdClass();
			$col->name = $c->Field;
			$col->type = $c->Type;
			$columns[] = $col;
		}
		return($columns);
	}

	function _columns($table)
    {
        $query = "SHOW COLUMNS FROM " . $table;
        $res = $this->dbquery($query);
        $columns = array();
        foreach ($res AS $c) {
            $columns[] = $c->Field;
        }
        return ($columns);
    }
	
	function _insert($table, $data) {
		$columns = $this->_describe($table);
		array_shift($columns);
		
		$query = "INSERT INTO ".$table." (";
		foreach($columns AS $c) {
			if($c->type != 'timestamp') {
				$query .= $c->name.',';
			}
		}
		$query = substr($query,0,-1);
		$query .= ") VALUES (";
		foreach($columns AS $c) {
			if($c->type != 'timestamp') {
				$val = 0;
				foreach($data AS $k=>$v) {
					if($k == $c->name) {
						if($v) {
							$val = $v;
						}
					}
				}
				$query .= "'".$val."',";
			}
		}
		$query = substr($query,0,-1);
		$query .= ")";
		return($query);
	}
	
	function _update($table, $data) {
		$columns = $this->_describe($table);
		array_shift($columns);
		
		$id = $data->id;
		unset($data->id);
		
		$query = "UPDATE ".$table." SET ";
		foreach($columns AS $c) {
			if($c->type != 'timestamp') {
				foreach($data AS $k=>$v) {
					if($k == $c->name) {
						$val = 0;
						if($v) {
							$val = $v;
						}
						$query .= $c->name.'="'.$val.'",';
					}
				}
			}
		}
		$query = substr($query,0,-1);
		$query .= ' WHERE id="'.$id.'"';
		return($query);
	}
	
	/**
	 * Utility function to throw an exception if an error occurs 
	 * while running a mysql command.
	 */
	private function throwExceptionOnError($link = null) {
		if($link == null) {
			$link = $this->connection;
		}
		if ($this->connection->errorCode()) {
			$msg =$link;
			throw new Exception($msg);
		}	
	}
}

/*
$newbase = new BaseService;
$res = $newbase->sendAndGetOne("SHOW TABLES LIKE 'Order_Product_Content_Log'");
print_r($res);
*/
?>