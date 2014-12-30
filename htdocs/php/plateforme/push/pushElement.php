<?php

class XmlPushElement {

  private $element_tag = 'user';
  private $xml_push_object = array();
  private $multi_token = false;
  private $tokens = array();

  public static $XML_PUSH_OS_IOS = 'ios';
  public static $XML_PUSH_OS_ANDROID = 'android';

  public $XMLPUSH_TOKEN = '';
  public $XMLPUSH_OS = '';
  public $XMLPUSH_BADGE = '0';
  public $XMLPUSH_TEXT = '';
  public $XMLPUSH_SOUND = '';
  public $XMLPUSH_PROPERTIES = array();




  /**
   * Constructeur
   * @param string $shared_key cle privee partagee de l'application
   */
  function __construct($XMLPUSH_OS) {
    $this->XMLPUSH_OS = $XMLPUSH_OS;
  }


  /**
   * Affiche le header xml pour une interpretation correcte du flux
   */
  private static function __print_xml_header() {
    header('Content-Type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
  }

  /**
   * Affiche un flux xml a partir d'un objet PHP
   * @param Object $object Objet PHP
   * @param boolean $needSecurity Necessite une securite
   * @param array $securityParams Parametres de securite
   * @param string $token token de verification
   */
  function setDatas($text = '', $token = '', $properties = array(), $sound = '', $badge = 0) {
    $xmlProperties = array();
    foreach ($properties as $key => $value){
      $xmlProperties[] = array('property' => array('#type' => $key, '_' => $value));
    }
    if (is_array($token)){
      //$text = mb_convert_encoding(str_replace("\n", '\n', $text), 'HTML-ENTITIES', 'UTF-8');
      $this->xml_push_object = array('message' => array(
          'badge' => $badge,
          'text' => '|'.str_replace('\n', "\\n", str_replace("\n", '\n', $text)),
          'sound' => $sound,
          'custom_properties' => $xmlProperties
      ));
      $this->multi_token = true;
      $this->tokens = $token;
    }else{
      //t$ext = mb_convert_encoding(str_replace("\n", '\n', $text), 'HTML-ENTITIES', 'UTF-8');
      $this->xml_push_object = array($this->element_tag => array(
          '#token' => $token,
          '#os' => $this->XMLPUSH_OS,
          'badge' => $badge,
          'text' => '|'.str_replace('\n', "\\n", str_replace("\n", '\n', $text)),
          'sound' => $sound,
          'custom_properties' => $xmlProperties
      ));
    }
  }

  function getXmlArray(){
    return $this->xml_push_object;
  }

  function isMultiToken(){
    return $this->multi_token;
  }

  function getTokens(){
    return $this->tokens;
  }

}

?>