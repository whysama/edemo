<?php

class FileLogger {

  private $file_uri = "";
  private $mail = "";

  private function write_log($type, $msg, $__FUNCTION__ = "", $__FILE__ = "", $__LINE__ = "", $__NAMESPACE__ = "") {
    if ($this->file_uri != "") {
      $f = fopen($this->file_uri, 'a');
      $time = date("D M j H:i:s");
      fwrite($f, $time . ' [' . $type . '] - ' . $msg . " - in file " . $__FILE__ . " (at line " . $__LINE__ . ") - ");
      if ($__FUNCTION__ != "") {
        fwrite($f, $__FUNCTION__ . "\n");
      } else {
        fwrite($f, $__NAMESPACE__ . "\n");
      }
      fclose($f);
    }
  }

  private function send_mail($type, $msg, $__FUNCTION__ = "", $__FILE__ = "", $__LINE__ = "", $__NAMESPACE__ = "") {
    if ($this->mail != "") {
      $priority = array("WARN" => "2", "INFO" => "3", "ERR " => "1");

      $to = $this->mail;
      $subject = '[PHP-LOGGER][' . $type . '] in ' . $__FILE__;
      $message = '<table><tr><td><p style="color:#333">Log from FileLogger.php</p></td></tr>' . "" . "\r\n";
      $message = "<tr><td><fieldset style=\"color:#333\"><legend>Message</legend>" . $msg . "<br/></fieldset></td></tr>" . "\r\n";
      $message .= '<tr><td><i style="color:#333">Logged at ' . $__FILE__ . " at line " . $__LINE__ . "</i></td></tr>" . "\r\n";
      if ($__FUNCTION__ != "") {
        $message .= '<tr><td>Function : ' . $__FUNCTION__ . "</td></tr>" . "\r\n";
      } else if ($__NAMESPACE__ != "") {
        $message .= '<tr><td>Namespace : ' . $__NAMESPACE__ . "</td></tr>" . "\r\n";
      }


      $message .= '<tr><td><table style="border-spacing:0px;border-width:1px;border-collapse:collapse;border:1px solid #CCC;background-color:#D8D8D8">' . "\r\n";
      $message .= '<tr><td style="font-weight:bold;background-color:#333;color:#FFF">Key</td><td style="font-weight:bold;background-color:#333;color:#FFF">Value</td></tr>' . "\r\n";
      foreach ($_SERVER as $key => $value) {
        $message .= '<tr><td style="border:1px solid #CCC;padding:2px 10px 2px 10px">' . $key . '</td><td style="border:1px solid #CCC;padding:2px 10px 2px 10px">' . $value . '</td></tr>' . "\r\n";
      }
      $message .= '</table></td></tr>' . "\r\n";
      $message .= '</table>';

      $headers = 'From: php.logger.' . strtolower(trim($type)) . '@airweb.fr' . "\r\n" .
              'MIME-Version: 1.0' . "\r\n" .
              'Content-type: text/html; charset=utf-8' . "\r\n" .
              'Reply-To: no-reply@airweb.fr' . "\r\n" .
              'X-Priority: ' . $priority[$type] . "\r\n" .
              'X-Mailer: PHP/' . phpversion();

      $htmlHead = '<html><head></head><body style="">';
      $htmlTail = '</body></html>';

      mail($to, $subject, $htmlHead . $message . $htmlTail, $headers);
    }
  }

  function __construct($file_uri, $mail = "") {
    ini_set('date.timezone', "Europe/Paris");
    $this->file_uri = $file_uri;
    $this->mail = $mail;
  }

  function warning($msg, $__FUNCTION__ = "", $__FILE__ = "", $__LINE__ = "", $__NAMESPACE__ = "") {
    $trace = debug_backtrace();
    $caller = array_shift($trace);
    $file = $caller['file'];
    $line = $caller['line'];
    $this->send_mail('WARN', $msg, $__FUNCTION__, $file, $line, $__NAMESPACE__);
    $this->write_log('WARN', $msg, $__FUNCTION__, $file, $line, $__NAMESPACE__);
  }

  function error($msg, $__FUNCTION__ = "", $__FILE__ = "", $__LINE__ = "", $__NAMESPACE__ = "") {
    $trace = debug_backtrace();
    $caller = array_shift($trace);
    $file = $caller['file'];
    $line = $caller['line'];
    $this->send_mail('ERR ', $msg, $__FUNCTION__, $file, $line, $__NAMESPACE__);
    $this->write_log('ERR ', $msg, $__FUNCTION__, $file, $line, $__NAMESPACE__);
  }

  function info($msg, $__FUNCTION__ = "", $__FILE__ = "", $__LINE__ = "", $__NAMESPACE__ = "") {
    $trace = debug_backtrace();
    $caller = array_shift($trace);
    $file = $caller['file'];
    $line = $caller['line'];
    $this->send_mail('INFO', $msg, $__FUNCTION__, $file, $line, $__NAMESPACE__);
    $this->write_log('INFO', $msg, $__FUNCTION__, $file, $line, $__NAMESPACE__);
  }

}

?>
