<?php

class Trigger {

  var $TABLE_NAME = "trigger_scans";
  var $DB;
  private $STORE_ACTION = false;
  private $ERROR_FORBIDDEN = "<h1>Forbidden</h1>";
  private $START_TIME = null;

  function __construct($db = null, $useStoreAction = false) {
    $this->DB = $db;
    $this->STORE_ACTION = $useStoreAction;
  }

  function start_trigger() {
    $this->START_TIME = time();
    $this->check_runtime();
    register_shutdown_function(array($this, '__trigger_store_action'));
  }

  private function check_runtime() {
    $isCli = (PHP_SAPI === 'cli');
    if (!$isCli) {
      header('HTTP/1.1 401 Unauthorized');
      print $this->ERROR_FORBIDDEN;
      exit(-1);
    }
  }

  function __trigger_store_action() {
    if ($this->STORE_ACTION) {
      $path = $_SERVER['SCRIPT_FILENAME'];
      if ($_SERVER['argc'] > 1) {
        for ($i = 1; $i < count($_SERVER['argv']); $i++) {
          $path .= " " . $_SERVER['argv'][$i];
        }
      }
      $errors = "";
      $errorsTab = error_get_last();
      if (count($errorsTab) > 0) {
        $errors = print_r($errorsTab, true);
      }
      if ($this->DB->is_connected()) {
        $this->DB->connect();
      }
      $this->DB->query('INSERT INTO ' . $this->TABLE_NAME . ' (file, start_date, end_date, errors) VALUES ("' . $path . '", FROM_UNIXTIME("' . $this->START_TIME . '"), NOW(), "' . $errors . '")');
      $this->DB->disconnect();
    }
  }

}

?>
