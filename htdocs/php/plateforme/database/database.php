<?php

abstract class Database {
    
    var $HOST;
    var $USERNAME;
    var $PASSWORD;
    var $DB_NAME;
	var $ERRORCALLBACK = null;
    
    function connect(){
        
    }
    
    function disconnect(){
        
    }
    
    function query($query, $get_insert_id = false, $result_as_array = false, $result_as_simple_array = false, $id_pattern = ""){
        
    }
    
    function is_connected(){
        
    }

	protected function error($error_message){
		if(isset($this->ERRORCALLBACK)) {
			call_user_func($this->ERRORCALLBACK, $error_message);
		}
		else {
			die($error_message);
		}
	}
}


?>
