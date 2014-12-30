<?php
register_shutdown_function('alert_on_shutdown');

function alert_on_shutdown(){
  $error = error_get_last();
  if ($error !== NULL) {
    if ($error['type'] == E_ERROR){
      require __DIR__.'/phpmailer/class.phpmailer.php';
      $phpadmin = "team.tech@airweb.fr";
      if (isset($_SERVER['SERVER_SIGNATURE']) && $_SERVER['SERVER_SIGNATURE'] != ''){
        $servername = $_SERVER['SERVER_SIGNATURE'];
      }elseif (isset($_SERVER['HOSTNAME']) && $_SERVER['HOSTNAME'] != ''){
        $servername = $_SERVER['HOSTNAME'];
      }elseif (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] != ''){
        $servername = $_SERVER['SERVER_NAME'];
      }else{
        $servername = '???';
      }
      $servername = str_replace('<address>', '', str_replace('</address>', '', $servername));

      $mail = new PHPmailer();
      $mail->IsSMTP();
      $mail->IsHTML(true);
      $mail->FromName='PHP Mailer';
      $mail->Host='smtp';
      $mail->From='php.fatal.error@airweb.fr';
      $mail->AddAddress($phpadmin);
      $mail->AddReplyTo('php.fatal.error@airweb.fr');
      $mail->Subject= '[PHP] Fatal error on '.$servername;

      $info = "Fatal error on <b>".$servername.'</b> ('.$error['type'].')<br/><br/>';
      $info .= "File : <b>" . $error['file'] . "</b>:<b>" . $error['line'] . "</b><br/>";
      $file = file($error['file']);
      $line = $file[max(array(0,$error['line']-1))];
      unset($file);
      $info .= "Line : <b>" . $line . "</b><br/>";
      $info .= "Message : <b>" . $error['message'] . "</b><br/><br/>";
      $info .= "SERVER_INFO : ";
      $info .= '<table>';
      foreach ($_SERVER as $key => $value){
        $info .= '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
      }
      $info .= '</table>';

      $mail->Body=$info;
      $mail->Send();
      $mail->SmtpClose();
      unset($mail);
    }
  }
}