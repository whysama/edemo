<?php
session_start();

define('is_prod', false);
define('API_KEY', 'CLE_API_A_GENERER');

require_once 'engine/engine.php';

$e = new RestEngine();
/*
function showXLS($input){
	print_r($input);
}


$e->addSupportedType('xls', 'text/plain', 'showXLS');
*/

$e->listen();