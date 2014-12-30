<?php
/**
 * Flux example 
 */
require_once '../logger/log.php';

function aClassicFunction(){
    //Save dans le fichier log & par mail en meme temps
	//$log = new FileLogger("/tmp/file_logger.log", "lionel.penaud@airweb.fr");
    
    //Save dans le fichier seulement
	$log = new FileLogger("/tmp/file_logger.log", "");
    
    //Envoi de mail seulement
	//$log = new FileLogger("", "lionel.penaud@airweb.fr");
    
    $log->warning("test de warning", __FUNCTION__, __FILE__, __LINE__, __NAMESPACE__);

    $log->error("test d'erreur", __FUNCTION__, __FILE__, __LINE__, __NAMESPACE__);

    $log->info("test d'info", __FUNCTION__, __FILE__, __LINE__, __NAMESPACE__);
}

aClassicFunction();

?>
