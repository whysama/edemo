<?php
//ifnotdef('project_root',      '/home/services/easyDesign/htdocs');
//ifnotdef('plateforme_root',   project_root.'/feed/plateforme/');
ifnotdef('project_name',   'easyDesign');
ifnotdef('plateforme_root',   __DIR__.'/plateforme/');

ifnotdef('dencrypter_seed', 'uSBVHQ4PJUPTzUIggFbOoVM1fiuSKIBVLnSQpVaA4KAWYMPvbcLspuPjHFTNqLvKNumDl3NTGIY3sucuwLaELaGLvQFuJmRu6nal');

ifnotdef ('db_host',          'localhost');
ifnotdef ('db_user',          'root');
ifnotdef ('db_password',      'aze789');
ifnotdef ('db_name',          'easyDesign');
ifnotdef ('session_name',     'easyDesign');

ifnotdef("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!

require_once 'plateforme/database/database.php';
require_once 'plateforme/database/mysql.php';

$db = new MySQL(db_host, db_user, db_password, db_name);

function hashPassword($password){
  return sha1(dencrypter_seed.$password.sha1(dencrypter_seed.$password));
}

function sec_session_start() {
    session_destroy();      // Détruire la session récupérée
    $secure = true;       // This stops JavaScript being able to access the session id.
    $httponly = true;       
    $cookieParams = session_get_cookie_params();
    $lifetime = 24 * 3600;

    session_set_cookie_params($cookieParams["lifetime"]+$lifetime,
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name(session_name);
    
    session_start();            // Start the PHP session 
    session_regenerate_id(true);    // regenerated the session, delete the old one. 
}

function ifnotdef($name, $value){
  if(!defined($name)){
    define($name, $value);
  }
}