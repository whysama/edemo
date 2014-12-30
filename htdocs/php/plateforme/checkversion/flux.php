<?php

require_once plateforme_root.'checkversion/checkversion.php';

class CheckVersionFlux extends CheckVersion{
    
    var $SHAREDKEY = "";
    var $APP_INFO_TABLE_NAME = "app_info";
    
    function __construct($db, $sharedkey){
	parent::__construct($db);
	$this->SHAREDKEY = $sharedkey;
    }
    
    private function checkSecurity($token, $date, $uid){
	return (sha1('*'.$uid.'*'.$date.'*'.$this->SHAREDKEY.'*') == $token);
    }
    
    private function checkParams($DATA){
	if (isset($DATA['appinfo'],$DATA['terminalinfo'],$DATA['securityinfo'])){
	    if (is_array($DATA['appinfo']) && is_array($DATA['terminalinfo']) && is_array($DATA['securityinfo'])){
		if (isset(
			$DATA['appinfo']['appid'],
			$DATA['appinfo']['appversion'],
			$DATA['appinfo']['applang'],
			$DATA['terminalinfo']['os'],
			$DATA['terminalinfo']['devicename'],
			$DATA['terminalinfo']['osversion'],
			$DATA['terminalinfo']['resolution'],
			$DATA['securityinfo']['date'],
			$DATA['securityinfo']['userid'],
			$DATA['securityinfo']['token'])){
		    
		    
		    if ($this->checkSecurity($DATA['securityinfo']['token'],
					$DATA['securityinfo']['date'],
					$DATA['securityinfo']['userid'])){
			return 0;
		    }else{
			return 1;
		    }
		}
	    }
	}
	return 2;
    }
    
    function printJson($DATA){
	//Support de l'ancienne methode (sans action)
	if (isset($DATA['action'])){
	    $datas = json_decode($DATA['action'], true);
	    $DATA = $datas;
	}
	if (isset($DATA['terminalinfo']['os'])){
	    $os = $DATA['terminalinfo']['os'];
	    //Verification de l'etat du checkversion : 
	    $isEnabled = $this->DB->query('SELECT value FROM '.$this->APP_INFO_TABLE_NAME.' WHERE '.$this->APP_INFO_TABLE_NAME.'.key = "CV_ENABLED" AND '.$this->APP_INFO_TABLE_NAME.'.os = "'.strtoupper($os).'"', false, true);
	    if (count($isEnabled) > 0){
		if ($isEnabled[0]['value'] == "0"){
		    exit;
		}
	    }else{
		exit;
	    }
	}else{
	    exit;
	}
	date_default_timezone_set('Europe/Paris');
	$secDate = date('Y-m-d H:i:s');
	if (isset($DATA['securityinfo']['userid'])){
	    $secUid = $DATA['securityinfo']['userid'];
	}else{
	    $secUid = 'no-uid';
	}
	$secToken = sha1('*'.$secUid.'*'.$secDate.'*'.$this->SHAREDKEY.'*');
	$securityinfo = array('date' => $secDate, 'userid' => $secUid, 'token' => $secToken);
	$actioninfo = array('cancelbttext' => '', 'confirmbttext' => '', 'cancelbturl' => '', 'confirmbturl' => '', 'message' => '');
	switch ($this->checkParams($DATA)){
	    case 0 :
		//Security OK
		$osversion = $DATA['terminalinfo']['osversion'];
		$os = $DATA['terminalinfo']['os'];
		$appversion = $DATA['appinfo']['appversion'];
		$status = $this->checkVersion($appversion, $os, $osversion);
		$returncode = 'OK';
		$appUrlTab = $this->DB->query('SELECT value FROM '.$this->APP_INFO_TABLE_NAME.' WHERE '.$this->APP_INFO_TABLE_NAME.'.key = "APP_URL" AND '.$this->APP_INFO_TABLE_NAME.'.os = "'.strtoupper($os).'"', false, true);
		$appUrl = "";
		if (count($appUrlTab) > 0){
		    $appUrl = $appUrlTab[0]['value'];
		}
		switch ($status){
		    case 0 :
			//Pas de mise à jour
			$returncode = 'OK';
			break;
		    case 1 :
			//Mise à jour facultative
			$actioninfo = array('cancelbttext' => 'Annuler', 'confirmbttext' => 'OK', 'cancelbturl' => 'close://', 'confirmbturl' => $appUrl, 'message' => 'Une mise à jour est disponible pour votre application. Souhaitez vous effectuer cette mise à jour?');
			$returncode = 'OK';
			break;
		    case 2 :
			//Mise à jour obligatoire
			$actioninfo = array('cancelbttext' => 'Quitter', 'confirmbttext' => 'OK', 'cancelbturl' => 'exit://', 'confirmbturl' => $appUrl, 'message' => 'Veuillez télécharger la dernière version de l\'application sur le store.');
			$returncode = 'OK';
			break;
		    case 3 :
			//OS Inconnu ou pas supporté par l'application
			$returncode = 'TERM_NOK';
			break;
		    case 4 :
			//Version de l'OS non répertoriée
			$returncode = 'TERM_NOK';
			break;
		    case 5 :
			//Mise à jour interdite
			$returncode = 'APP_NOK';
			break;
		    case 6 :
			//Le backoffice ne possède pas d'informations sur l'os
			$returncode = 'OK';
			break;
		    
		}
		header('Content-Type:application/json');
		echo json_encode(array('returncode' => $returncode, 'securityinfo' => $securityinfo, 'actioninfo' => $actioninfo));
		break;
	    case 1 :
		header('Content-Type:application/json');
		echo json_encode(array('returncode' => 'AUTH_NOK', 'securityinfo' => $securityinfo, 'actioninfo' => $actioninfo));
		break;
	    case 2 :
		header('Content-Type:application/json');
		echo json_encode(array('returncode' => 'NOK', 'securityinfo' => $securityinfo, 'actioninfo' => $actioninfo));
		break;
	}
	
	
    }
    
}
?>
