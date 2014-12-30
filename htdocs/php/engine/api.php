<?php
//error_reporting(E_ALL);
ini_set('display_errors', '1');

if (defined('is_prod')){
  if (is_prod && defined('API_KEY')){
    $apiKeyError = (isset($_GET['API_KEY']) && $_GET['API_KEY'] != API_KEY);
    $showApi = (isset($_GET['API_KEY']) && $_GET['API_KEY'] == API_KEY);
  }else{
    $showApi = true;
  }
}else{
  $showApi = true;
}

//Inclusion des services existants
$services = glob(realpath(__DIR__ . '/../services/') . '/*.php');
foreach ($services as $service) {
  require_once($service);
}

if ($showApi) {

  if (!defined('JSON_PRETTY_PRINT')) {
    //Si json pretty print est supporte (php > 5.4.0) on l'utilise
    define('JSON_PRETTY_PRINT', 0);

    function json_pretty($json, $options = array()) {
      $tokens = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
      $result = '' . "\n";
      $indent = 0;

      $format = 'txt' . "\n";

      //$ind = "\t";
      $ind = "    ";

      if (isset($options['format'])) {
        $format = $options['format'];
      }

      switch ($format) {
        case 'html':
          $lineBreak = '<br />' . "\n";
          $ind = '&nbsp;&nbsp;&nbsp;&nbsp;' . "\n";
          break;
        default:
        case 'txt':
          $lineBreak = "\n";
          //$ind = "\t";
          $ind = "    ";
          break;
      }

      // override the defined indent setting with the supplied option
      if (isset($options['indent'])) {
        $ind = $options['indent'];
      }

      $inLiteral = false;
      foreach ($tokens as $token) {
        if ($token == '') {
          continue;
        }

        $prefix = str_repeat($ind, $indent);
        if (!$inLiteral && ($token == '{' || $token == '[')) {
          $indent++;
          if (($result != '') && ($result[(strlen($result) - 1)] == $lineBreak)) {
            $result .= $prefix;
          }
          $result .= $token . $lineBreak;
        } elseif (!$inLiteral && ($token == '}' || $token == ']')) {
          $indent--;
          $prefix = str_repeat($ind, $indent);
          $result .= $lineBreak . $prefix . $token;
        } elseif (!$inLiteral && $token == ',') {
          $result .= $token . $lineBreak;
        } else {
          $result .= ( $inLiteral ? '' : $prefix ) . $token;

          // Count # of unescaped double-quotes in token, subtract # of
          // escaped double-quotes and if the result is odd then we are
          // inside a string literal
          if ((substr_count($token, "\"") - substr_count($token, "\\\"")) % 2 != 0) {
            $inLiteral = !$inLiteral;
          }
        }
      }
      return $result;
    }

  }

  function myErrorHandler($errno, $errstr, $errfile, $errline) {
    if (E_RECOVERABLE_ERROR === $errno) {
      return true;
    }
    return false;
  }

  set_error_handler('myErrorHandler');

  function curPageURL() {
    $pageURL = 'http';
    if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") {
      $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $pageURL .= $_SERVER["HTTP_X_FORWARDED_HOST"] . ":" . $_SERVER["SERVER_PORT"];
      } else {
        $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"];
      }
    } else {
      if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $pageURL .= $_SERVER["HTTP_X_FORWARDED_HOST"];
      } else {
        $pageURL .= $_SERVER["HTTP_HOST"];
      }
    }
    return $pageURL;
  }
  ?><!DOCTYPE html>
  <html>
    <head>
      <title><?php echo project_name." API Rest"?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <style>
        body{
          font-family: 'Lucida Grande', 'Helvetica', 'arial', sans-serif;
          color:#222;
          font-size:12px;
          margin:0;
          background:#F0F0F0;
          min-width: 960px;
        }

        h1, h2, h3, h4, h5, h6, h7, p{
          margin:0;
        }

        h1, h2, h3, table{
          margin:5px 5px 5px 10px;
        }

        h2{
          padding-left:10px;
        }

        table tr td{
          padding:5px;
          vertical-align: top;
        }

        a{
          color:#000;
          margin:5px 5px 5px 20px;
        }

        a:hover{
          text-decoration: none;
        }

        .class-separator{
          height: 1px;
          background-color:#E0E0E0;
          border-bottom: 1px solid #FFF;
          margin:10px 10px 20px 10px;
        }

        .method{
          background-color:#E0E0E0;
          margin:0 10px 10px 10px;
          padding:10px;
          box-shadow: 0 1px 2px #999;
        }

        .method:last-child {
          border-radius: 0 0 5px 5px;
        }

        .class-title{
          background-color:#D8D8D8;
          margin:0 10px 2px 10px;
          padding:10px;
          position: relative;
        }

        .backtotop-button{
          position: absolute;
          right: 20px;
        }

        .hideshow-class-button{
          position: absolute;
          right: 100px;
          color:#000;
          margin:5px 5px 5px 20px;
          text-decoration: underline;
          cursor: pointer;
        }

        .method-table{
          width:90%;
        }

        .method-table tr td:first-child{
          width:200px;
        }

        .prettyprint{
          border:1px solid #CCC;
          padding:5px;
          background-color: #FFF;
          box-shadow: 0 1px 2px #333;
          word-wrap:break-word;
          max-width: 800px;
          display: none;
        }

        .hideshowlink{
          text-decoration: underline;
          cursor: pointer;
          color:#000;
        }
      </style>
      <style>
        .class-title{
          background-image: linear-gradient(bottom, rgb(59,59,59) 0%, rgb(105,105,105) 100%);
          background-image: -o-linear-gradient(bottom, rgb(59,59,59) 0%, rgb(105,105,105) 100%);
          background-image: -moz-linear-gradient(bottom, rgb(59,59,59) 0%, rgb(105,105,105) 100%);
          background-image: -webkit-linear-gradient(bottom, rgb(59,59,59) 0%, rgb(105,105,105) 100%);
          background-image: -ms-linear-gradient(bottom, rgb(59,59,59) 0%, rgb(105,105,105) 100%);

          background-image: -webkit-gradient(
            linear,
            left bottom,
            left top,
            color-stop(0, rgb(59,59,59)),
            color-stop(1, rgb(105,105,105))
            );

          color:#FFF;
          border-radius: 5px 5px 0 0;
          text-shadow: 0 1px 2px #111;
        }

        .class-title.close{
          border-radius: 5px;
        }

        .class-title span, .class-title a{
          text-shadow: none;
          color:#FFF !important;
        }

        .left-menu{
          -webkit-transform: translateZ(0);
          z-index:1000;
          position:fixed;
          left:0;
          top:0px;
          width:20%;
          overflow-y: scroll;
          height: 100%;
        }

        .left-menu-content{
          margin:10px 0;
        }

        .right-body{
          -webkit-transform: translateZ(0);
          position:fixed;
          right:0;
          top:0;
          width:80%;
          overflow-y: scroll;
          height: 100%;
        }

        .right-body-content{
          margin:10px 0;
        }

        .class-link{
          font-weight: bold;
          text-decoration: none;
          margin-top:10px;
          display: inline-block;
        }

        .method-link{
          font-weight: normal;
          text-decoration: none;
          padding-left:10px;

        }

        .method-link.selected{
          font-weight: bold;
        }
      </style>
      <script>
        function displayReturnExample(id){
          object = document.getElementById(id);
          displayState = object.style.display;
          if (displayState == 'block'){
            object.style.display = 'none';
          }else{
            object.style.display = 'block';
          }
        }

        function displayClass(id, titleId){
          object = document.getElementById(id);
          titleId = document.getElementById(titleId);
          displayState = object.style.display;
          if (displayState == 'block'){
            object.style.display = 'none';
            titleId.setAttribute('class', 'class-title close');
          }else{
            object.style.display = 'block';
            titleId.setAttribute('class', 'class-title');
          }
        }

        function addClass(element, classe){
          element.className += ' '+classe;
        }

        function removeClass(element, classe){
          var elC = ' '+element.className+' ';
          while (elC.indexOf(' '+classe+' ') !== -1){
            elC = elC.replace(' '+classe+' ', '');
          }
          element.className = elC;
        }

        function clearSelectedItems(){
          var items = document.getElementsByClassName('selected');
          for (var item in items){
            removeClass(items[item], 'selected');
          }
        }

        var noClearItems = false;

        function loadMethod(element){
          noClearItems = true;
          clearSelectedItems();
          addClass(element, 'selected');
          setTimeout(function(){
            noClearItems = false;
          }, 10);
        }

        function init(){
          var rb = document.getElementById('right-body');
          rb.addEventListener("scroll", function(evt) {
            if (!noClearItems){
              clearSelectedItems();
            }
          });
        }

      </script>
    </head>
    <body onload="init()">
      <?php
      //Chargement des classes
      //Inclusion des services existants
      $services = glob(__DIR__ . '/../services/*.php');
      foreach ($services as $service) {
        require_once($service);
      }
      ?>
      <div class="left-menu">
        <div class="left-menu-content">
        <?php
        $classes = get_declared_classes();
        echo '<h2>Summary</h2>' . "\n";
        for ($i = 0; $i < count($classes); $i++) {
          $className = $classes[$i];
          if (get_parent_class($className) == 'RestGeneric') {
            echo '<a href="#' . $className . '" class="class-link">' . $className . '</a><br/>' . "\n";
            $methods = get_class_methods($className);
            for ($j = 0; $j < count($methods); $j++) {
              $method = $methods[$j];
              echo '<a href="#method_' . $className . '_' .$method. '" class="method-link" onclick="loadMethod(this)">' . $method . '</a><br/>' . "\n";
            }
          }
        }
        ?>
        </div>
      </div>
      <div id="right-body" class="right-body">
        <div class="right-body-content">
        <?php
        $methodCount = 1;
        $classCount = 1;
        for ($i = 0; $i < count($classes); $i++) {
          $className = $classes[$i];
          if (get_parent_class($className) == 'RestGeneric') {
            echo '<div class="class-title" id="' . $classes[$i] . '" >' . "\n";
            echo '<h2 style="display:inline;">Class ' . $classes[$i] . '</h2> <span onclick="displayClass(\'class' . $classCount . '\', \'' . $classes[$i] . '\')" class="hideshow-class-button">hide/show</span> <a href="#" class="backtotop-button">back to top</a>' . "\n";
            echo '</div>' . "\n";
            echo '<div id="class' . $classCount . '" style="display:block;">' . "\n";
            $methods = get_class_methods($className);
            for ($j = 0; $j < count($methods); $j++) {
              $method = $methods[$j];
              $cmethods = $className::$methods;
              echo '<div class="method" id="method_'.$classes[$i].'_'.$method.'">' . "\n";
              echo '<h3 style="clear:both;">Method ' . $method . '</h3>' . "\n";
              echo '<table class="method-table">' . "\n";
              if (isset($cmethods[$method]['extension'])) {
                $extension = $cmethods[$method]['extension'];
              } else {
                $extension = 'json';
              }
              echo '<tr><td>URL : </td><td>' . dirname($_SERVER['SCRIPT_NAME']) . '/' . $className . '/' . $method . '.' . $extension . '</td></tr>' . "\n";
              echo '<tr><td>Name : </td><td>' . $method . '</td></tr>' . "\n";
              echo '<tr><td>Description : </td><td>' . (array_key_exists($method, $cmethods) ? (array_key_exists('description', $cmethods[$method]) ? $cmethods[$method]['description'] : '<i>Not documented</i>') : '<i>Not documented</i>') . '</td></tr>' . "\n";
              echo '<tr><td>Methods : </td><td>' . "\n";
              if (isset($cmethods[$method]) && isset($cmethods[$method]['type'])) {
                for ($k = 0; $k < count($cmethods[$method]['type']); $k++) {
                  switch ($cmethods[$method]['type'][$k]) {
                    case RestGeneric::METHOD_GET :
                      echo 'GET' . "\n";
                      break;
                    case RestGeneric::METHOD_POST :
                      echo 'POST' . "\n";
                      break;
                    case RestGeneric::METHOD_DELETE :
                      echo 'DELETE' . "\n";
                      break;
                    case RestGeneric::METHOD_PUT :
                      echo 'PUT' . "\n";
                      break;
                  }
                  if ($k < (count($cmethods[$method]['type']) - 1)) {
                    echo ', ' . "\n";
                  }
                }
              } else {
                echo '<i>Not documented.</i>' . "\n";
              }
              echo '</td></tr>' . "\n";
              echo '<tr><td>Parameters : </td><td>' . "\n";
              if (isset($cmethods[$method]) && isset($cmethods[$method]['params'])) {
                foreach ($cmethods[$method]['params'] as $param => $value) {
                  echo $param . ' : <b>' . "\n";
                  switch ($value['type']) {
                    case RestGeneric::PARAM_TYPE_INT :
                      echo 'Integer' . "\n";
                      break;
                    case RestGeneric::PARAM_TYPE_FLOAT :
                      echo 'Float' . "\n";
                      break;
                    case RestGeneric::PARAM_TYPE_STRING :
                      echo 'String' . "\n";
                      break;
                  }
                  echo ($value['required'] ? '*' : '');
                  echo '</b>' . "\n";
                  if (array_key_exists('description', $value)) {
                    echo ' <i>' . $value['description'] . '</i>' . "\n";
                  }
                  echo '<br/>' . "\n";
                }
              } else {
                echo '<i>Not documented.</i>' . "\n";
              }
              echo '</td></tr>' . "\n";
              echo '<tr><td></td><td>* Required</td></tr>' . "\n";
              echo '<tr><td>Call example : </td><td>' . "\n";
              $url = dirname($_SERVER['SCRIPT_NAME']) . '/' . $className . '/' . $method . '.' . $extension;
              $params = '';
              $paramsurl = '';
              if (isset($cmethods[$method]) && isset($cmethods[$method]['params'])) {
                $notDocumented = false;
                foreach ($cmethods[$method]['params'] as $param => $value) {
                  if (isset($value['exampleValue'])) {
                    $params .= $param . '=' . $value['exampleValue'] . '&';
                    $paramsurl .= $param . '=' . urlencode($value['exampleValue']) . '&';
                  } else {
                    echo '<i>Not documented</i>' . "\n";
                    $notDocumented = true;
                    break;
                  }
                }
                if (!$notDocumented) {
                  if ($params != '') {
                    $urldisplay = curPageURL() . $url . '?' . substr($params, 0, -1);
                    $url = curPageURL() . $url . '?' . substr($paramsurl, 0, -1);
                  } else {
                    $urldisplay = curPageURL() . $url;
                    $url = curPageURL() . $url;
                  }
                  echo '<a href="' . $url . '" target="_blank">' . $urldisplay . '</a>' . "\n";
                } else {
                  $url = null;
                }
                echo '</td>' . "\n";
                echo '<tr><td>Return example : </td><td>' . "\n";
                if (isset($cmethods[$method]['returnExample'])) {
                  if (isset($cmethods[$method]['returnExample']['type'])) {
                    echo '<span onclick="displayReturnExample(\'returnExample' . $methodCount . '\')" class="hideshowlink">Hide / Show the return example</span>' . "\n";
                    switch ($cmethods[$method]['returnExample']['type']) {
                      case RestGeneric::RETURN_EXAMPLE_DYNAMIC :
                        if ($url != null) {
                          if ($extension == 'json') {
                            echo '<pre class="prettyprint" id="' . 'returnExample' . $methodCount . '">';
                            if (JSON_PRETTY_PRINT == 0) {
                              echo json_pretty(file_get_contents($url));
                            } else {
                              echo json_encode(json_decode(file_get_contents($url)), JSON_PRETTY_PRINT);
                            }
                            echo '</pre>' . "\n";
                          } else {
                            echo '<pre class="prettyprint" id="' . 'returnExample' . $methodCount . '">' . "\n";
                            echo (file_get_contents($url));
                            echo '</pre>' . "\n";
                          }
                        } else {
                          echo '<i>Missing examples values.</i>' . "\n";
                        }
                        break;
                      case RestGeneric::RETURN_EXAMPLE_STATIC :
                        if ($extension == 'json') {
                          echo '<pre class="prettyprint" id="' . 'returnExample' . $methodCount . '">';
                          if (JSON_PRETTY_PRINT == 0) {
                            echo json_pretty($cmethods[$method]['returnExample']['value']);
                          } else {
                            if (array_key_exists('value', $cmethods[$method]['returnExample'])){
                              echo json_encode(json_decode($cmethods[$method]['returnExample']['value']), JSON_PRETTY_PRINT);
                            }else{
                              echo 'NO VALUE';
                            }
                          }
                          echo '</pre>' . "\n";
                        } else {
                          echo '<pre class="prettyprint" id="' . 'returnExample' . $methodCount . '">';
                          echo ($cmethods[$method]['returnExample']['value']);
                          echo '</pre>' . "\n";
                        }
                        break;
                    }
                    $methodCount++;
                  }
                } else {
                  echo '<i>Not documented.</i>' . "\n";
                }
                echo '</td></tr>' . "\n";
              } else {
                echo '<i>Not documented.</i>' . "\n";
              }
              echo '</td></tr>' . "\n";
              echo '</table>' . "\n";
              echo '</div>' . "\n";
            }
            echo '</div>' . "\n";
            echo '<div class="class-separator"></div>' . "\n";
            $classCount++;
          }
        }
        ?>
        </div>
      </div>
    </body>
  </html>
  <?php
} else {
  if ($apiKeyError){
    echo 'API_KEY is wrong';
  }else{
    echo 'API_KEY missing';
  }

}
