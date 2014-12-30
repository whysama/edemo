<?php
/**
 * Flux example 
 */
define ('plateforme_root', '/var/www/plateforme/');
require_once '../flux/json.php';
require_once '../flux/xml.php';

$objet = array('nom' => 'Dupont', 'prenom' => 'Jean', 'parents' => array(array('nom' => 'Dupont', 'prenom' => 'Michel'), array('nom' => 'Dupont', 'prenom' => 'Josianne')));

if ($_GET['type'] == "json"){
    //Exemple avec json sans securite
    if (!isset($_GET['security'])){
	$json = new JsonFlow();
	$json->printJson($objet);
    }else{
	$sjson = new JsonFlow("__cle_privee__");
	$sjson->printJson($objet, true, array("__device_id__", "__date__"), sha1('#'."__device_id__".'#'."__date__".'#'."__cle_privee__".'#'));
    }
    
    
}elseif ($_GET['type'] == "xml"){
    //Exemple avec XML
    if (!isset($_GET['security'])){
	$xml = new XmlFlow();
	if (isset($_GET['attr'])){
	    //On ajoute des attributs aux balises avec un index #... (ex: #age)
	    $objet = array('nom' => 'Dupont', 'prenom' => 'Jean', 'parents' => array(array('nom' => 'Dupont', 'prenom' => 'Michel', '#age' => 42), array('nom' => 'Dupont', 'prenom' => 'Josianne', '#age' => 38)));
	    $xml->printXml($objet);
	}else{
	    $xml->printXml($objet);
	}
    }else{
	$sjson = new XmlFlow("__cle_privee__");
	$sjson->printXml($objet, true, array("__device_id__", "__date__"), sha1('#'."__device_id__".'#'."__date__".'#'."__cle_privee__".'#'));
    }
    
    
}

?>