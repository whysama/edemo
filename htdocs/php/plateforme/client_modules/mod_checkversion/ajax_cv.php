<?php
//require_once '../../php/conf.php';
//session_name(session_name);
//session_start();
//require_once '../../php/includes.php';

//Needs $db
$db = null;

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    $METHOD = $_POST;
}else{
    $METHOD = $_GET;
}

$auth = new Authenticate($db);
$auth->TABLE_NAME = 'pub_user';
 
if ($auth->is_logged_in()){
    $cv = new CheckVersionAdmin($db);
    if (isset($METHOD['type'])){
	switch ($METHOD['type']){
	    case 'getAppVersions' ;
		$appOS = $db->query('SELECT DISTINCT os FROM app_info', false, true, true);
		//Status des checkversion
		$appInfosTab = $db->query('SELECT app_info.key, app_info.os, app_info.value FROM app_info WHERE app_info.key = "APP_URL" OR app_info.key = "CV_ENABLED"', false, true);
		$appInfos = array();
		for ($i=0;$i<count($appInfosTab);$i++){
		    if (!array_key_exists($appInfosTab[$i]['os'], $appInfos)){
			$appInfos[$appInfosTab[$i]['os']] = array();
		    }
		    $appInfos[$appInfosTab[$i]['os']][$appInfosTab[$i]['key']] = $appInfosTab[$i]['value'];
		}
		$versionsTab = $db->query('SELECT id_app, os, id_version, limitation, required FROM checkversion ORDER BY os, id_app', false, true);
		$versionsSelector = $cv->getVersions($appOS);
		$versions = array();
		$lastId = 0;
		
		
		for ($i=0;$i<count($versionsTab);$i++){
		    if (!array_key_exists($versionsTab[$i]['os'], $versions)){
			$versions[$versionsTab[$i]['os']] = array();
			$lastId = 0;
		    }
		    $versions[$versionsTab[$i]['os']][$lastId] = array('appVersion' => $versionsTab[$i]['id_app'], 'os_version' => $versionsTab[$i]['id_version'], 'limitation' => $versionsTab[$i]['limitation'], 'required' => $versionsTab[$i]['required']);
		    if (array_key_exists($versionsTab[$i]['id_version'], $versionsSelector[$versionsTab[$i]['os']])){
			$versions[$versionsTab[$i]['os']][$lastId]['os_version_value'] = $versionsSelector[$versionsTab[$i]['os']][$versionsTab[$i]['id_version']];
		    }else{
			$versions[$versionsTab[$i]['os']][$lastId]['os_version_value'] = "inconnue";
		    }
		    $lastId++;
		}
		
		//On rajoute les elements sans versions
		for ($i=0;$i<count($appOS);$i++){
		    if (!array_key_exists($appOS[$i], $versions)){
			$versions[$appOS[$i]] = array();
		    }
		}
		
		$results = array('selector' => $versionsSelector, 'versions' => $versions, 'appInfos' => $appInfos);
		echo json_encode($results);
		break;
		
	    case 'updateAppVersion' :
		if (isset($METHOD['oldAppVersion'], $METHOD['version'], $METHOD['appVersion'], $METHOD['limitation'], $METHOD['osVersion'], $METHOD['required'])){
		    $appVersion = addslashes($METHOD['appVersion']);
		    $appVersion = str_replace(" ", "", $appVersion);
		    $appVersion = trim($appVersion);
		    $cv->addAppVersion(addslashes($METHOD['version']), $appVersion, addslashes($METHOD['required']), addslashes($METHOD['limitation']), addslashes($METHOD['osVersion']), addslashes($METHOD['oldAppVersion']));
		}
		break;
		
	    case 'addAppVersion' :
		if (isset($METHOD['version'], $METHOD['appVersion'], $METHOD['limitation'], $METHOD['osVersion'], $METHOD['required'])){
		    $appVersion = addslashes($METHOD['appVersion']);
		    $appVersion = str_replace(" ", "", $appVersion);
		    $appVersion = trim($appVersion);
		    $cv->addAppVersion(addslashes($METHOD['version']), $appVersion, addslashes($METHOD['required']), addslashes($METHOD['limitation']), addslashes($METHOD['osVersion']));
		}
		break;
		
	    case 'updateCheckingVersionStatus' :
		if (isset($METHOD['version'], $METHOD['status'])){
		    $db->query('UPDATE app_info SET app_info.value = "'.addslashes($METHOD['status']).'" WHERE app_info.key = "CV_ENABLED" AND app_info.os = "'.addslashes($METHOD['version']).'"');
		}else{
		    error_log(print_r($METHOD, true));
		}
		break;
	}
    }

}
?>
