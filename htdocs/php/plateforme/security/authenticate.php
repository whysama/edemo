<?php

class Authenticate {

  var $TABLE_NAME = "user";
  var $DB;
  var $ENCRYPTER = '\Authenticate::encrypt';
  var $login_name = '';

  function __construct($db = null) {
    $this->DB = $db;
  }

  function set_db($db) {
    $this->DB = $db;
  }

  function register($login, $password) {
    $enc_pass = call_user_func($this->ENCRYPTER, $password);
    if ($this->DB->is_connected()) {
      $this->DB->connect();
    }
    $this->DB->query('INSERT INTO ' . $this->TABLE_NAME . ' (login, password) VALUES ("' . addslashes($login) . '", "' . addslashes($enc_pass) . '")');
    $this->DB->disconnect();
  }

  function encrypt($str) {
    if (!defined("encrypter_seed")) {
      $seed = "";
    } else {
      $seed = encrypter_seed;
    }
    return md5($seed . $str . md5($seed . $str));
  }

  function login($login, $password) {
    $enc_pass = call_user_func($this->ENCRYPTER, $password);
    $user = $this->DB->query('SELECT * FROM ' . $this->TABLE_NAME . ' WHERE login = "' . addslashes($login) . '" AND password = "' . addslashes($enc_pass) . '"', false, true);
    if (count($user) > 0) {
      $_SESSION['user_logged_in'] = true;
      $_SESSION['login'] = $login;
      foreach ($user[0] as $key => $value) {
        if ($key != "login" && $key != "password") {
          $_SESSION['special_' . $key] = $value;
        }
      }
    }
    return (count($user) > 0);
  }

  function is_logged_in() {
    return (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']);
  }

  function logout() {
    unset($_SESSION['user_logged_in']);
    unset($_SESSION['login']);
  }

}

?>
