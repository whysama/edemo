<?php

require_once __DIR__.'/restclass.php';

//Inclusion des services existants
$services = glob(realpath(__DIR__.'/../services/').'/*.php');
foreach ($services as $service){
  require_once($service);
}


class RestEngine {

  private $type = "json";
  private $calltype = "json";
  private $supportedTypes = array();

  function addSupportedType($extension, $mimetype, $printingMethod){
    $this->supportedTypes[] = array('ext' => $extension, 'mime' => $mimetype, 'print' => $printingMethod);
  }

  function parse_raw_http_request(array &$a_data) {
    // read incoming data
    $input = file_get_contents('php://input');

    // grab multipart boundary from content type header
    preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
    $boundary = $matches[1];

    // split content by boundary and get rid of last -- element
    $a_blocks = preg_split("/-+$boundary/", $input);
    array_pop($a_blocks);

    // loop data blocks
    foreach ($a_blocks as $id => $block) {
      if (empty($block))
        continue;

      // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char
      // parse uploaded files
      if (strpos($block, 'application/octet-stream') !== FALSE) {
        // match "name", then everything after "stream" (optional) except for prepending newlines
        preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
      }
      // parse all other fields
      else {
        // match "name" and optional value in between newline sequences
        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
      }
      $a_data[$matches[1]] = $matches[2];
    }
  }

  function listen() {
    if (isset($_REQUEST)) {
      $dir = dirname($_SERVER['SCRIPT_NAME']);
      $requestUri = substr($_SERVER['REDIRECT_URL'], strlen($dir));
      if ($requestUri == ''){
        header('Location:'.$_SERVER['REDIRECT_URL'].dirname($_SERVER['SCRIPT_NAME']).'/api/');
        exit(0);
      }
      if ($requestUri == '/api/'){
        include __DIR__.'/api.php';
        return;
      }
      $requestParts = explode('/', $requestUri);
      if (count($requestParts) > 2) {

        //Check response type
        $methodParts = explode('.', $requestParts[2]);
        if (count($methodParts) > 1){
          $method = $methodParts[0];
          $type = $methodParts[1];
        }else{
          $method = $methodParts[0];
          $type = 'json';
        }
      } else {
        $method = '';
        $type = 'json';
      }

      $this->type = $type;
      $request = null;

      $codeCallType = 0;
      //Check call type (put post get delete)
      switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST' : $this->calltype = 'post';
          $codeCallType = RestGeneric::METHOD_POST;
          break;
        case 'GET' : $this->calltype = 'get';
          $codeCallType = RestGeneric::METHOD_GET;
          break;
        case 'PUT' : $this->calltype = 'put';
          $codeCallType = RestGeneric::METHOD_PUT;
          break;
        case 'DELETE' : $this->calltype = 'delete';
          $codeCallType = RestGeneric::METHOD_DELETE;
          break;
        default : $this->calltype = 'get';
          $codeCallType = RestGeneric::METHOD_GET;
      }

      if ($this->calltype != 'get') {
        $noCheckInput = false;
        //If POST vars cant be read
        if (array_key_exists('QUERY_STRING', $_SERVER)){
          if ($_SERVER['QUERY_STRING'] != ''){
            $input = $_SERVER['QUERY_STRING'];
            $noCheckInput = true;
          }
        }
        if (!$noCheckInput){
          $input = '';
          $putdata = fopen("php://input", "r");
          while ($line = fread($putdata, 1024)) {
            $input .= $line;
          }
          fclose($putdata);
        }
        parse_str(urldecode($input), $request);
      } else {
        $request = $_GET;
      }

      if ($request == null){
        $request = array();
      }

      //Check object
      $object = $requestParts[1];

      if (class_exists($object)) {
        $o = new $object();
        if (get_parent_class($o) == 'RestGeneric') {
          //Check Method
          if (method_exists($o, $method) && array_key_exists($method, $object::$methods)){
            if (in_array($codeCallType, $object::$methods[$method]['type'])){
              //Start timer
              $stime = microtime(true);
              //Check request params (required, type and so)
              $params = $object::$methods[$method]['params'];
              $missingParams = array();
              foreach ($params as $param => $properties){
                if (($properties['required']) && !array_key_exists($param, $request)){
                  $missingParams[] = $param;
                }else{
                  if (array_key_exists($param, $request)){
                    switch ($properties['type']){
                      case RestGeneric::PARAM_TYPE_FLOAT :
                        $request[$param] = (floatval($request[$param]));
                        break;
                      case RestGeneric::PARAM_TYPE_INT :
                        $request[$param] = (intval($request[$param]));
                        break;
                    }
                  }else{
                    if (isset($properties['defaultValue'])){
                      $request[$param] = $properties['defaultValue'];
                    }
                  }
                }
              }

              if (count($missingParams) > 0){
                $errorMsg = '';
                for ($i=0;$i<count($missingParams);$i++){
                  $errorMsg .= $missingParams[$i];
                  if ($i != (count($missingParams)-1)){
                    $errorMsg .= ', ';
                  }
                }
                $this->printError('Missing params : '.$errorMsg);
              }

              try{
                $result = $o->$method($request);//call_user_func_array(array($o, $method), array($request));
              }catch(Exception $e){
                $this->printError('Method execution error');
              }
              if (!is_null($result)) {
                if (is_string($result)){
                  $this->printError($result);
                }else{
                  if (isset($object::$methods[$method]['hideJsonContainer']) && $object::$methods[$method]['hideJsonContainer']){
                    $this->printResponse($result, false, null, true, true);
                  }else{
                    $this->printResponse($result, true, $stime);
                  }

                }
              } else {
                $this->printError('Method execution error');
              }
            }else{
              $this->printError('Method call type not allowed');
            }
          } else {
            $this->printError('Unavailable method');
          }
        } else {
          $this->printError('Unallowed object');
        }
      } else {
        $this->printError('Unavailable object');
      }
    } else {
      $this->printError('No request');
    }
  }

  function printResponse($response, $printmicrotime = false, $stime = null, $statusOk = true, $hideContainer = false) {

    $this->printHeaders();

    switch ($this->type) {
      case 'json' :
        if ($statusOk) {
          $status = 'ok';
        } else {
          $status = 'ko';
        }
        if ($hideContainer){
          echo json_encode($response);
        }else{
          if ($printmicrotime && $stime != null){
            echo json_encode(array('status' => $status, 'time' => time(), 'exectime' => round((microtime(true)-$stime)*1000000)/1000, 'response' => $response));
          }else{
            echo json_encode(array('status' => $status, 'time' => time(), 'response' => $response));
          }
        }

        break;
      case 'xml' :
        echo '<root><message>Not yet supported</message></root>';
        break;
      case 'csv' :
        echo 'Not yet supported;';
        break;
      default :
        $bodyPrinted = false;
        foreach ($this->supportedTypes as $type) {
          if ($type['ext'] == $this->type){
            call_user_func($type['print'], $response);
            $bodyPrinted = true;
            break;
          }
        }
        if (!$bodyPrinted){
          echo $response;
        }
        break;
    }

    exit(0);
  }

  private function printHeaders() {
    if (!headers_sent()){
      switch ($this->type) {
        case 'json' :
          header('Cache-Control:public, max-age=10');
          header('Content-Type:application/json');
          break;
        case 'xml' :
          header('Content-Type:application/xml');
          break;
        case 'csv' :
          header('Content-Type:text/plain');
          break;
        default :
          $headPrinted = false;
          foreach ($this->supportedTypes as $type) {
            if ($type['ext'] == $this->type){
              header('Content-Type:'.$type['mime']);
              $headPrinted = true;
              break;
            }
          }
          if (!$headPrinted){
            header('Content-Type:text/plain');
          }
          break;
      }
    }
  }

  function printError($msg) {
    $this->printResponse(array('errorMessage' => $msg), false, null, false);
  }

}
