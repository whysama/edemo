<?php
require_once plateforme_root.'database/database.php';

class Sybase extends Database {
	var $HOST;
	var $USERNAME;
	var $PASSWORD;
	var $DB_NAME;
	var $ERRORCALLBACK = null;

	private $SYBASE_IDENTIFIER = null;

	/**
	 * Sybase constructor
	 * @param string $host Hostname of the db server
	 * @param string $username Username
	 * @param string $password Password
	 * @param string $dbname Database name
	 */
	function __construct($host = "localhost", $username = "", $password = "", $dbname = "", $errorcallback = null) {
		$this->HOST = $host;
		$this->USERNAME = $username;
		$this->PASSWORD = $password;
		$this->DB_NAME = $dbname;
		$this->ERRORCALLBACK = $errorcallback;
	}

	/**
	 * Connect the database
	 */
	function connect(){
		$this->SYBASE_IDENTIFIER = sybase_connect($this->HOST, $this->USERNAME, $this->PASSWORD) or $this->error('Sybase connection error');
		sybase_select_db($this->DB_NAME, $this->SYBASE_IDENTIFIER);
	}

	/**
	 * Disconnect the database
	 */
	function disconnect(){
		sybase_close($this->SYBASE_IDENTIFIER);
		$this->SYBASE_IDENTIFIER = null;
	}

	/**
	 * Test if the database is connected
	 * @return bool database is connected
	 */
	function is_connected(){
		return ($this->SYBASE_IDENTIFIER != null);
	}
	
	/**
	 * Execute a query
	 * @param string $query SQL Query
	 * @param bool $get_insert_id Get the auto-incremented id of last inserted element
	 * @param bool $result_as_array Get the result as an array
	 * @param bool $result_as_simple_array Get the result as an array of single elements
	 * @deprecated
	 * @return mixed
	 */
	function query($query, $get_insert_id = false, $result_as_array = false, $result_as_simple_array = false, $id_pattern = ""){
		die("Sybase::query NOT IMPLEMENTED");
	}

	/**
	 * Execute a query
	 * @param string $query SQL Query
	 * @param bool $result_as_array Get the result as an array
	 * @param bool $result_as_simple_array Get the result as an array of single elements
	 * @return mixed
	 */
	function sybase_query($query, $result_as_array = false, $result_as_simple_array = false, $id_pattern = ""){
		if (!$this->is_connected()){
			$this->connect();
		}
		if ($result_as_array){
			if ($result_as_simple_array){
				$sqlresult = mysql_query($query, $this->SYBASE_IDENTIFIER) or $this->error($this->sybase_log(sybase_get_last_message(), $query));
				$result = array();
				while($resultRow = sybase_fetch_row($sqlresult)){
					array_push($result, $resultRow[0]);
				}
				return $result;
			}else{
				$sqlresult = sybase_query($query, $this->SYBASE_IDENTIFIER) or $this->error($this->sybase_log(sybase_get_last_message(), $query));
				$result = array();
				while($resultRow = sybase_fetch_assoc($sqlresult)){
					array_push($result, $resultRow);
				}
				if ($id_pattern != ""){
					$resultAssoc = array();
					$matches = null;
					preg_match_all('|#(.*)#|U', $id_pattern, $matches);
				
					$keysSharped = $matches[0];
					$keys = $matches[1];
					for ($j=0;$j<count($result);$j++){
						$key = $id_pattern;
						for ($i=0;$i<count($keys);$i++){
							$key = str_replace($keysSharped[$i], $result[$j][$keys[$i]], $key);
						}
						$resultAssoc[$key] = $result[$j];
					}
					return $resultAssoc;
				}else{
					return $result;
				}
			}
		}else{
			$sqlresult = sybase_query($query, $this->SYBASE_IDENTIFIER) or $this->error($this->sybase_log(sybase_get_last_message(), $query));
			return $sqlresult;
		}
	}

	private function sybase_log($error, $query){
		error_log("Sybase Plateforme : ".$error." : ".$query);
	}
}