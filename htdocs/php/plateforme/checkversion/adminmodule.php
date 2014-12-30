<?php
/**
 * Classe CheckVersionAdmin, pour ajouter un module de checkversion dans l'admin
 * Nécessite une table dans la bdd de l'application "checkversion" sur le modèle :
   CREATE TABLE IF NOT EXISTS `checkversion` (
    `id_app` varchar(8) NOT NULL,
    `os` enum('IOS','ANDROID') NOT NULL,
    `id_version` int(11) DEFAULT NULL,
    `limitation` enum('NONE','=','<','>','<=','>=') NOT NULL,
    `required` tinyint(1) NOT NULL,
    PRIMARY KEY (`id_app`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 * Utilise également la table version de la bdd PlateformeVersion
 * 
 */

require_once plateforme_root.'checkversion/checkversion.php';

class CheckVersionAdmin extends CheckVersion{
        
    /**
     * Methode addAppVersion pour enregistrer une nouvelle version d'application dans la base
     * @param VERSION $Os type d'os (IOS ou ANDROID) Utiliser les valeurs statiques de la classe
     * @param String $version version de l'application
     * @param Boolean $required Cette version est elle une mise à jour obligatoire?
     * @param type $restriction Contrainte sur l'os client (valeurs : null, NONE, <, <=, =, >, >=)
     * @param type $osVersion Id de la version de l'os (récupéré par la méthode getversion
     * @param type $oldAppVersion Ancien numero de version pour un update
     */
    function addAppVersion($Os, $version, $required = false, $restriction = null, $osVersion = null, $oldAppVersion = null){
	if ($required){
	    $requiredField = "1";
	}else{
	    $requiredField = "0";
	}
	if ($restriction == null){
	    $restrictionField = '"NONE"';
	}else{
	    if (in_array($restriction, array('<', '=', '>', '>=', '<='))){
		$restrictionField = '"'.addslashes($restriction).'"';
	    }else{
		$osVersion = null;
	    }
	}
	if ($osVersion == null){
	    $osVersionField = "NULL";
	    $restrictionField = '"NONE"';
	}else{
	    $osVersionField = '"'.addslashes($osVersion).'"';
	}
	if ($oldAppVersion != null){
	    if ($version != ""){
		$this->DB->query('UPDATE '.$this->TABLE_NAME.' SET id_app = "'.addslashes($version).'", id_version = '.$osVersionField.', limitation = '.$restrictionField.', required = '.$requiredField.' WHERE id_app = "'.$oldAppVersion.'" AND os = "'.$Os.'" ');
	    }else{
		$this->DB->query('DELETE FROM '.$this->TABLE_NAME.' WHERE id_app = "'.$oldAppVersion.'" AND os = "'.$Os.'" ');
	    }
	}else{
	    if ($version != ""){
		$this->DB->query('INSERT INTO '.$this->TABLE_NAME.'(id_app, os, id_version, limitation, required) VALUES ("'.addslashes($version).'", "'.addslashes($Os).'", '.$osVersionField.', '.$restrictionField.', '.$requiredField.')
		    ON DUPLICATE KEY update id_app = "'.addslashes($version).'", id_version = '.$osVersionField.', limitation = '.$restrictionField.', required = '.$requiredField.' ');
	    }
	}
	
    }
    
    /**
     * Suppression d'une version de l'admin
     * @param type $Os Os cible
     * @param type $version Version cible
     */
    function removeAppVersion($Os, $version){
	$this->DB->query('DELETE FROM '.$this->TABLE_NAME.' WHERE id_app = "'.addslashes($version).'" AND os = "'.addslashes($Os).'"');
    }
    
}
?>
