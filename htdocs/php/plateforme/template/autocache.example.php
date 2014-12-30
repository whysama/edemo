<?php
/**
 * Autocaching example 
 * IMPORTANT : the server domain name need to be include in /etc/hosts file
 * 127.0.0.1    domainname.tld localhost servername... 
 */
define ('plateforme_root', '/var/www/plateforme/');
require_once '../cache/cache.php';
require_once '../database/mysql.php';

$db = new MySQL("localhost", "root", "", "test_plateforme");
$db->connect();

$cache = new Cache($db);
$cache->set_cache_time(15);

$cache->autocache_start();

//Long processing to cache
echo "Current date : ".date('d-M-Y h:i:s');


$cache->autocache_end();


?>
