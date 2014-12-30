<?php
error_reporting(0);
#require_once __DIR__.'/../php/conf.php';
require_once 'conf.php';
require_once 'engine/engine.php';
require_once 'utils/servicehelper.php';

$e = new RestEngine();

$e->listen();
