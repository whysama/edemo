<?php

require_once plateforme_root.'database/database.php';

class MySQL extends Database{
	var $HOST;
	var $USERNAME;
	var $PASSWORD;
	var $DB_NAME;
	var $ERRORCALLBACK = null;

	private $MYSQL_IDENTIFIER = null;

	/**
	 * Mysql constructor
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
		$this->MYSQL_IDENTIFIER = mysql_connect($this->HOST, $this->USERNAME, $this->PASSWORD, true) or $this->error('Mysql connection error');
		mysql_select_db($this->DB_NAME, $this->MYSQL_IDENTIFIER) or $this->error('Mysql connection error');
		$this->query("SET NAMES 'utf8'");
	}

	/**
	 * Disconnect the database
	 */
	function disconnect(){
		mysql_close($this->MYSQL_IDENTIFIER) or $this->error('Mysql connection error');
		$this->MYSQL_IDENTIFIER = null;
	}

	/**
	 * Execute a query
	 * @param string $query SQL Query
	 * @param bool $get_insert_id Get the auto-incremented id of last inserted element
	 * @param bool $result_as_array Get the result as an array
	 * @param bool $result_as_simple_array Get the result as an array of single elements
	 * @param string $id_pattern Pattern of key for result array ($result_as_array need to be true) (ex : #key_name#)
	 * @return mixed
	 */
	function query($query, $get_insert_id = false, $result_as_array = false, $result_as_simple_array = false, $id_pattern = ""){
		if (!$this->is_connected()){
			$this->connect();
		}
		if ($get_insert_id){
			mysql_query($query, $this->MYSQL_IDENTIFIER) or $this->error($this->mysql_log(mysql_error(), $query));
			return mysql_insert_id();
		}else{
			if ($result_as_array){
				if ($result_as_simple_array){
					$sqlresult = mysql_query($query, $this->MYSQL_IDENTIFIER) or $this->error($this->mysql_log(mysql_error(), $query));
					$result = array();
					while($resultRow = mysql_fetch_row($sqlresult)){
						array_push($result, $resultRow[0]);
					}
					return $result;
				}else{
					$sqlresult = mysql_query($query, $this->MYSQL_IDENTIFIER) or $this->error($this->mysql_log(mysql_error(), $query));
					$result = array();
					while($resultRow = mysql_fetch_assoc($sqlresult)){
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
				$sqlresult = mysql_query($query, $this->MYSQL_IDENTIFIER) or $this->error($this->mysql_log(mysql_error(), $query));
				return $sqlresult;
			}
		}
	}

	/**
	 * Test if the database is connected
	 * @return bool database is connected
	 */
	function is_connected(){
		return ($this->MYSQL_IDENTIFIER != null);
	}
	
	/**
	 * Return whether a table exists in the database 
	 * @param string $table_name the name of the table to search
	 * @return bool if the table exists
	 */
	function table_exists($table_name){
		$db_result = $this->query("SELECT COUNT(*) as COUNT FROM information_schema.tables WHERE table_schema = '".$this->DB_NAME."' AND table_name = '".$table_name."'", false, true);
		return $db_result[0]["COUNT"];
	}


	private function mysql_log($error, $query){
		error_log("MySQL Plateforme : ".$error." : ".$query);
	}

	/**
	 * Initialize the database
	 */
	function initialize($use_cache = false, $use_mem_cache = false, $use_security = false){
		if (!$this->is_connected()){
			$this->connect();
		}
		if ($use_cache){
			if ($use_mem_cache){
				//TODO
			}
		}

		if ($use_security){
			//TODO
		}
	}


	function getMySQLIdentifier(){
		return $this->MYSQL_IDENTIFIER;
	}
}