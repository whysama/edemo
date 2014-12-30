<?php
class CheckVersion {
    
    var $TABLE_NAME = "checkversion";
    var $PLATEFORME_ADDRESS = "db1";
    var $PLATEFORME_DB_NAME = "PlateformeVersions";
    var $PLATEFORME_USER_NAME = "www";
    var $PLATEFORME_PASSWORD = "jcB450cKV";
    var $PLATEFORME_TABLE_NAME = "versions";
    var $DB;
    
    const IOS = 'IOS';
    const ANDROID = 'ANDROID';
    
    function __construct($db = null){
        $this->DB = $db;
    }
    
    function set_db($db){
        $this->DB = $db;
    }
    
    /**
     * Récupération du tableau des versions par OS et par ordre d'existance
     * @param type $OsArray tableau d'OS Versions
     * @return type tableau
     */
    function getVersions($OsArray){
	$db2 = new MySQL($this->PLATEFORME_ADDRESS, $this->PLATEFORME_USER_NAME, $this->PLATEFORME_PASSWORD, $this->PLATEFORME_DB_NAME);
	$versions = array();
	for ($i=0;$i<count($OsArray);$i++){
	    $result = $db2->query('SELECT id_version, version FROM versions WHERE os = "'.$OsArray[$i].'" ORDER BY ordre', false, true);
	    $versions[$OsArray[$i]] = array();
	    for ($j=0;$j<count($result);$j++){
		$versions[$OsArray[$i]][$result[$j]['id_version']] = $result[$j]['version'];
	    }
	}
	$db2->disconnect();
	unset($db2);
	return $versions;
    }
    
    /**
     * Verification d'une version d'application
     * @param type $appversion Version de l'application du client
     * @param type $os OS du client
     * @param type $osversion Version de l'OS Client
     * @return	0  : Pas de mise à jour
     *		1  : Mise à jour facultative
     *		2  : Mise à jour obligatoire
     *		3+ : Erreur 
     */
    function checkVersion($appversion, $os, $osversion){
	$versions = $this->getVersions(array(strtoupper($os)));
	if (count($versions) > 0){
	    $this->DB->connect();
	    $currversiontab = $this->DB->query('SELECT id_app, os, id_version, limitation, required FROM '.$this->TABLE_NAME.' WHERE os = "'.strtoupper($os).'" AND id_app = "'.addslashes($appversion).'" LIMIT 0,1 ', false, true);
	    $lastversiontab = $this->DB->query('SELECT id_app, os, id_version, limitation, required FROM '.$this->TABLE_NAME.' WHERE os = "'.strtoupper($os).'" ORDER BY id_app DESC LIMIT 0,1 ', false, true);
	    if ((count($currversiontab) > 0) && (count($lastversiontab) > 0)){
		$currversion = $currversiontab[0];
		$lastversion = $lastversiontab[0];
		//OS Connu
		$isLast = ($currversion['id_app'] == $lastversion['id_app']);
		$idVersion = 0;
		$j=0;
		$osVersionOrdre = 0;
		$appVersionOrdre = 0;
		foreach ($versions[strtoupper($os)] as $id => $version){
		    if ($osversion == $version){
			$idVersion = $id;
			$osVersionOrdre = $j;
		    }
		    if ($currversion['limitation'] != 'NONE'){
			if ($id == $currversion['id_version']){
			    $appVersionOrdre = $j;
			}
		    }
		    $j++;
		}
		if ($idVersion == 0){
		    //Version inconnue
		    return 4;
		}
	    
		if ($isLast){
		    //Dernière version OK
		    return 0;
		}else{
		    if ($lastversion['limitation'] == 'NONE'){
			if (strcmp($lastversion['id_app'], $currversion['id_app']) > 0){
			    //App pas à jour
			    if ($currversion['required'] == 1){
				return 2;
			    }else{
				return 1;
			    }
			}
			if (strcmp($lastversion['id_app'], $currversion['id_app']) < 0){
			    //App "trop" à jour
			    return 0;
			}
			if (strcmp($lastversion['id_app'], $currversion['id_app']) == 0){
			    //App à jour
			    return 0;
			}
		    }else{
			$status = 0;
			switch ($lastversion['limitation']){
			    case '>' : 
				if ($osVersionOrdre > $appVersionOrdre){
				    $status = 1;
				}
				break;
			    case '<' : 
				if ($osVersionOrdre < $appVersionOrdre){
				    $status = 1;
				}
				break;
			    case '>=' : 
				if ($osVersionOrdre >= $appVersionOrdre){
				    $status = 1;
				}
				break;
			    case '<=' : 
				if ($osVersionOrdre <= $appVersionOrdre){
				    $status = 1;
				}
				break;
			    case '=' : 
				if ($osVersionOrdre == $appVersionOrdre){
				    $status = 1;
				}
				break;
			}
			if ($status == 0){
			    //Maj interdite
			    return 5;
			}else{
			    //Maj possible
			    if ($currversion['required'] == 1){
				return 2;
			    }else{
				return 1;
			    }
			}
		    }
		}
	    }else{
		//OS inexistant pour l'appli
		return 6;
		//Ou version pas renseigné dans le backoffice
	    }
	}else{
	    //OS Inconnu
	    return 3;
	}
	
	
    }
}
?>
