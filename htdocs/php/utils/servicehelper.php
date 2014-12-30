<?php

class serviceHelper{
  private static $messages = array(
      'success'  => array("code" => "111", "message" => "success"),
      'failed'  => array("code" => "000", "message" => "failed"),
      'missing'  => array('code' => '001', 'message' => 'Missing params'),
      'existing' => array('code' => '002', 'message' => 'Param is already exist in the database'),
      'inexisting' => array('code' => '003', 'message' => 'Param isn\'t exist in the database'),
    );

  public static function isValidRequest($request){
    if (!isset($request) || $request == null) {
      return false;
    }else{
      return true;
    }
  }

  public static function returnMessage($keyword,$var){
    return self::$messages[$keyword];
  }

  public static function isExistIn($var,$table,$condition){
    global $db;

    $length = count($condition);

    $sql = "SELECT {$var} FROM {$table} WHERE ";
    for ($i=0; $i < $length; $i++) { 
      $sql .= "{$condition[$i]['key']} = '{$condition[$i]['value']}'";
      if ($length > $i + 1) {
        $sql .= " AND ";
      }
    }
    $sql .= ";";
    $result = self::query($sql);
    if (count($result) > 0) {
      return $result;
    }else{
      return false;
    }
  }

  public static function query($sql){
    global $db;
    $result = $db->query($sql,false,true);
    return $result;
  }
}