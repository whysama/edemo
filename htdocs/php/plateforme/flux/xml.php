<?php

class XmlFlow {
    
    private $shared_key = "";
    private static $STATUS_OK = "OK";
    private static $STATUS_NOK = "KO";
    private $root_name = "root";
    private $entry_name = "entry";
    
    /**
     * Constructeur
     * @param string $shared_key cle privee partagee de l'application
     */
    function __construct($shared_key = "", $root_name = "root", $entry_name = "entry") {
	$this->shared_key = $shared_key;
	$this->root_name = $root_name;
	$this->entry_name = $entry_name;
    }
    
    /**
     * Encode un objet en xml 
     */
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
		foreach ($mixed as $index=>$mixedElement) {
		    if (is_int($index)) {
			    $nodeName = $this->entry_name;
		    } else {
			    $nodeName = $index;
		    }
		    if (substr($nodeName, 0, 1) == "#"){
			$node = $DOMDocument->createAttribute(substr($nodeName, 1));
			$node->value = "";
			$domElement->appendChild($node);
		    }else{
			$node = $DOMDocument->createElement($nodeName);
			$domElement->appendChild($node);
		    }
		    $this->xml_encode($mixedElement, $node, $DOMDocument);
		}
	    } else {
		$new_node = $DOMDocument->createTextNode($mixed);
		$domElement->appendChild($new_node);
	    }
	}
    } 
    
    /**
     * Verifie les conditions d'authentification pour l'acces au flux
     * @param array $sha1params parametres du sha1
     * @param string $token token envoye
     * @param string $sha1separator separateur de parametres
     */
    private function __check_security($sha1params, $token, $sha1separator = "#"){
	$sha1string = $sha1separator;
	for ($i=0;$i<count($sha1params);$i++){
	    $sha1string .= $sha1params[$i].$sha1separator;
	}
	$sha1string .= $this->shared_key.$sha1separator;
	
	if ($token != sha1($sha1string)){
	    $this->throwXmlError('Unable to Authenticate');
	}
    }
    
    /**
     * Affiche le header xml pour une interpretation correcte du flux 
     */
    private static function __print_xml_header(){
	header('Content-Type: text/xml');
    }
    
    /**
     * Emet un message d'erreur xml
     * @param string $message Message d'erreur
     */
    function throwXmlError($message){
	XmlFlow::__print_xml_header();
	echo XmlFlow::xml_encode(array('status' => XmlFlow::$STATUS_NOK, 'response' => "", 'msg' => $message));
	exit;
    }
    
    /**
     * Affiche un flux xml a partir d'un objet PHP
     * @param Object $object Objet PHP
     * @param boolean $needSecurity Necessite une securite
     * @param array $securityParams Parametres de securite
     * @param string $token token de verification
     */
    function printXml($object, $needSecurity = false, $securityParams = array(), $token = ""){
	if ($needSecurity){
	    $this->__check_security($securityParams, $token);
	}
	XmlFlow::__print_xml_header();
	echo XmlFlow::xml_encode(array('status' => XmlFlow::$STATUS_OK, 'response' => $object));
    }
}

?>