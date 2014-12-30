<?php
/**
 * Authenticate example 
 * 127.0.0.1    domainname.tld localhost servername... 
 */
define ('plateforme_root', '/var/www/plateforme/');
require_once '../cache/cache.php';
require_once '../database/mysql.php';
require_once '../security/authenticate.php';

$db = new MySQL("localhost", "root", "", "test_plateforme");
$db->connect();

$auth = new Authenticate($db);

$db->query('TRUNCATE TABLE user');

//Register an user
$auth->register("tata", "titi");

//Login a user
if ($auth->login("tata", "titi")){
    echo "User logged in<br/>";
}

if ($auth->is_logged_in()){
    echo "User is logged in<br/>";
}else{
    echo "User is logged out<br/>";
}

$auth->logout();
echo "Logging out user...<br/>";

if ($auth->is_logged_in()){
    echo "User is logged in<br/>";
}else{
    echo "User is logged out<br/>";
}

?>
