<?php

require_once __DIR__.'/SmartDOMDocument.class.php';

class XmlPush{

  public $XMLPUSH_SEND_DATE = '';
  public $XMLPUSH_SERVICE_ID = '';
  public $XMLPUSH_PUSH_ID = '';
  public $XMLPUSH_CHECKSUM = '';
  public $XMLPUSH_SENDING_DATE = '';
  public $XMLPUSH_PROD = "true";

  private $root_name = 'pushes';
  private $entry_name = 'entry';
  private $pushes = array();
  private $multi_token = false;
  private $multi_token_os = '';
  private $tokens = false;

  function __construct($XMLPUSH_SEND_DATE, $XMLPUSH_SERVICE_ID, $XMLPUSH_PUSH_ID = '', $XMLPUSH_CHECKSUM = '', $XMLPUSH_PROD = true){
    $this->XMLPUSH_SEND_DATE = $XMLPUSH_SEND_DATE;
    $this->XMLPUSH_SERVICE_ID = $XMLPUSH_SERVICE_ID;
    $this->XMLPUSH_PUSH_ID = $XMLPUSH_PUSH_ID;
    $this->XMLPUSH_CHECKSUM = $XMLPUSH_CHECKSUM;
    if ($XMLPUSH_PROD == "true" || $XMLPUSH_PROD == "1" || $XMLPUSH_PROD){
      $this->XMLPUSH_PROD = "true";
    }else{
      $this->XMLPUSH_PROD = "false";
    }
  }

  function addPush($pushObject){
    if ($pushObject->isMultiToken()){
      $this->multi_token = true;
      $this->multi_token_os = $pushObject->XMLPUSH_OS;
      $this->tokens = $pushObject->getTokens();
      $this->pushes = $pushObject->getXmlArray();
    }else{
      if (!$this->multi_token){
        $this->pushes[] = $pushObject->getXmlArray();
      }else{
        //On empeche l'ajout d'autres message
      }
    }
  }

  private static function __print_xml_header() {
    header('Content-Type: text/xml');
  }

  /**
   * Encode un objet en xml
   */
  private function xml_encode_2($mixed, $domElement = NULL, $DOMDocument = NULL) {
    if (is_null($DOMDocument)) {
      $DOMDocument = new DOMDocument;
      $DOMDocument->formatOutput = true;

      $rootNode = $DOMDocument->createElement($this->root_name);
      $DOMDocument->appendChild($rootNode);

      $this->xml_encode($mixed, $rootNode, $DOMDocument);

      //Suppression des entry-delete
      $str = @$DOMDocument->saveXML();
      $str = str_replace('<entry-delete>', '', $str);
      $str = str_replace('</entry-delete>', '', $str);
      return $str;
    } else {
      if (is_array($mixed)) {
        foreach ($mixed as $index => $mixedElement) {
          if (is_int($index)) {
            $nodeName = 'entry-delete';//$this->entry_name;
            $node = $DOMDocument->createElement($nodeName);
            $domElement->appendChild($node);
            $this->xml_encode($mixedElement, $node, $DOMDocument);
          } else {
            if ($index == "_"){
              $nodeName = "toto";
              $new_node = $DOMDocument->createTextNode($mixedElement);
              $domElement->appendChild($new_node);
            }else{
              $nodeName = $index;
              if (substr($nodeName, 0, 1) == "#") {
                $node = $DOMDocument->createAttribute(substr($nodeName, 1));
                $node->value = "";
                $domElement->appendChild($node);
              } else {
                $node = $DOMDocument->createElement($nodeName);
                $domElement->appendChild($node);
              }
              $this->xml_encode($mixedElement, $node, $DOMDocument);
            }
          }
        }
      } else {
        $new_node = $DOMDocument->createTextNode($mixed);
        $domElement->appendChild($new_node);
      }
    }
  }

  private function xml_encode($mixed, $domElement = NULL, $DOMDocument = NULL) {
    if (is_null($DOMDocument)) {
      $DOMDocument = new DOMDocument;
      $DOMDocument->formatOutput = true;

      $rootNode = $DOMDocument->createElement($this->root_name);
      $DOMDocument->appendChild($rootNode);

      $this->xml_encode($mixed, $rootNode, $DOMDocument);
      return @$DOMDocument->saveXML();
    } else {
      if (is_array($mixed)) {
        foreach ($mixed as $index => $mixedElement) {
          if (is_int($index)) {
            $this->xml_encode($mixedElement, $domElement, $DOMDocument);
          } else {
            if ($index == "_"){
              $new_node = $DOMDocument->createTextNode($mixedElement);
              $domElement->appendChild($new_node);
            }else{
              $nodeName = $index;
              if (substr($nodeName, 0, 1) == "#") {
                $node = $DOMDocument->createAttribute(substr($nodeName, 1));
                $node->value = "";
                $domElement->appendChild($node);
              } else {
                $node = $DOMDocument->createElement($nodeName);
                $domElement->appendChild($node);
              }
              $this->xml_encode($mixedElement, $node, $DOMDocument);
            }
          }
        }
      } else {
        if (substr($mixed, 0, 1) == '|'){
          $new_node = $DOMDocument->createCDATASection(substr($mixed, 1));
        }else{
          $new_node = $DOMDocument->createTextNode($mixed);
        }
        $domElement->appendChild($new_node);
      }
    }
  }

  public function printXmlFile($f = null){
    if ($this->multi_token){
      $list = array();
      for ($i=0;$i<count($this->tokens);$i++){
        $list[] = array('user' => array('#token' => $this->tokens[$i], '#os' => $this->multi_token_os));
      }
      $result = array(
          '#service_id' => $this->XMLPUSH_SERVICE_ID,
          '#push_id' => $this->XMLPUSH_PUSH_ID,
          '#checksum' => $this->XMLPUSH_CHECKSUM,
          '#sendingDate' => $this->XMLPUSH_SEND_DATE,
          '#prod' => $this->XMLPUSH_PROD,
          $this->pushes,
          'list' => $list
      );
    }else{
      $result = array(
          '#service_id' => $this->XMLPUSH_SERVICE_ID,
          '#push_id' => $this->XMLPUSH_PUSH_ID,
          '#checksum' => $this->XMLPUSH_CHECKSUM,
          '#sendingDate' => $this->XMLPUSH_SEND_DATE,
          '#prod' => $this->XMLPUSH_PROD,
          'list' => $this->pushes
      );
    }
    if ($f == null){
      $this->__print_xml_header();
      echo $this->xml_encode($result);
    }else{
      fwrite($f, $this->xml_encode($result));
      fseek($f, 0);
    }
  }



}


?>
