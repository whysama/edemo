<?php

class EmptyJsonFlowObject{}

class JsonFlow {
    
    private $shared_key = "";
    private $auto_ios_safe = false;
    private $ios_safe = false;
    private $use_response_encapsulation = true;
    private $status_label = 'status';
    private $STATUS_OK = 'OK';
    private $STATUS_NOK = 'KO';
    private $response_label = 'response';
    private $message_label = 'message';
    
    /**
     * Constructeur
     * @param string $shared_key cle privee partagee de l'application
     * @param boolean $auto_ios_safe active automatiquement ios safe selon le UA
     * @param boolean $ios_safe active ios safe
     */
    function __construct($shared_key = "", $auto_ios_safe = false, $ios_safe = false) {
	$this->shared_key = $shared_key;
	$this->auto_ios_safe = $auto_ios_safe;
	$this->ios_safe = $ios_safe;
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
	    $this->throwJsonError('Unable to Authenticate');
	}
    }
    
    /**
     * Utilise un sous objet response qui encapsule la réponse
     * @param type $use_encapsulation 
     */
    public function set_json_encapsulation($use_encapsulation){
	$this->use_response_encapsulation = $use_encapsulation;
    }
    
    /**
     * Définit le label de code de retour (default : "status")
     * @param type $label 
     */
    public function set_status_label($label){
	$this->status_label = $label;
    }
    
    /**
     * Définit la valeur de code de retour OK
     * @param type $value 
     */
    public function set_ok_value($value){
	$this->STATUS_OK = $value;
    }
    
    /**
     * Définit la valeur de code de retour KO
     * @param type $value 
     */
    public function set_ko_value($value){
	$this->STATUS_NOK = $value; 
    } 
    
    /**
     * Définit le label de l'encapsulation de réponse (default : "response")
     * @param type $label 
     */
    public function set_response_label($label){
	$this->response_label = $label;
    }
    
    /**
     * Définit le label de message d'erreur (default : "message")
     * @param type $label 
     */
    public function set_message_label($label){
	$this->message_label = $label;
    }
    
    /**
     * Affiche le header json pour une interpretation correcte du flux 
     */
    private static function __print_json_header(){
	header('Content-Type: application/json');
    }
    
    /**
     * Emet un message d'erreur json
     * @param string $message Message d'erreur
     */
    function throwJsonError($message){
	JsonFlow::__print_json_header();
	if ($this->use_response_encapsulation){
	    echo json_encode(array($this->status_label => $this->STATUS_NOK, $this->response_label => new EmptyJsonFlowObject(), $this->message_label => $message));
	}else{
	    echo json_encode(array($this->status_label => $this->STATUS_NOK, $this->message_label => $message));
	}
	
	exit;
    }
    
    static function arrayNoIntegers($array){
	$arrayres = $array;
	foreach ($array as $k => $v) {
	    if (is_array($v)) {
		$arrayres[$k] = JsonFlow::arrayNoIntegers($v);
	    }else{
		if (is_int($v)){
		    $arrayres[$k] = $v."";
		}
	    }
	}
	return $arrayres;
    }
    
    /**
     * Affiche un flux json a partir d'un objet PHP
     * @param Object $object Objet PHP
     * @param boolean $needSecurity Necessite une securite
     * @param array $securityParams Parametres de securite
     * @param string $token token de verification
     */
    function printJson($object, $needSecurity = false, $securityParams = array(), $token = "", $echo = true){
	if ($needSecurity){
	    $this->__check_security($securityParams, $token);
	}
	
	if ($this->auto_ios_safe){
	    if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') 
		    || strstr($_SERVER['HTTP_USER_AGENT'],'iPod')
		    || strstr($_SERVER['HTTP_USER_AGENT'],'iPad')){
		$iosObject = $this->arrayNoIntegers($object);
		if ($this->use_response_encapsulation){
		    $data = json_encode(array($this->status_label => $this->STATUS_OK, $this->response_label => $iosObject));
		}else{
		    $response = array($this->status_label => $this->STATUS_OK);
		    foreach ($iosObject as $key => $value){
			$response[$key] = $value;
		    }
		    $data = json_encode($response);
		}
	    }else{
		if ($this->use_response_encapsulation){
		    $data = json_encode(array($this->status_label => $this->STATUS_OK, $this->response_label => $object));
		}else{
		    $response = array($this->status_label => $this->STATUS_OK);
		    foreach ($object as $key => $value){
			$response[$key] = $value;
		    }
		    $data = json_encode($response);
		}
	    }
	}else{
	    if ($this->ios_safe){
		$iosObject = $this->arrayNoIntegers($object);
		if ($this->use_response_encapsulation){
		    $data = json_encode(array($this->status_label => $this->STATUS_OK, $this->response_label => $iosObject));
		}else{
		    $response = array($this->status_label => $this->STATUS_OK);
		    foreach ($iosObject as $key => $value){
			$response[$key] = $value;
		    }
		    $data = json_encode($response);
		}
	    }else{
		if ($this->use_response_encapsulation){
		    $data = json_encode(array($this->status_label => $this->STATUS_OK, $this->response_label => $object));
		}else{
		    $response = array($this->status_label => $this->STATUS_OK);
		    foreach ($object as $key => $value){
			$response[$key] = $value;
		    }
		    $data = json_encode($response);
		}
	    }
	}
	
	if(isset($data)){
		if($echo){
			JsonFlow::__print_json_header();
			echo $data;
		}
		return $data;
	}
    }
    
    
    function saveJson($object, $filename, $needSecurity = false, $securityParams = array(), $token = ""){
    	if($filename == null || $filename == ""){
    		$this->throwJsonError('Invalid file name');
    	}
    	$data = $this->printJson($object, $needSecurity, $securityParams, $token, false);
    	if($data != null){
    		return file_put_contents($filename, $data);
    	}
    	return false;
    }
}

?>