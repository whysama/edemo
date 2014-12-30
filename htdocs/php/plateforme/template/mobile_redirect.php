<?php
require_once __DIR__.'/../utils/mobileRedirect.php';

$mr = new MobileRedirect();
$mr->setAndroidMarketRedirection('com.imangi.templerun');
$mr->setAppStoreRedirection('420009108');
$mr->setDefaultUri('http://www.google.com');

$mr->addRedirection('samsung', "http://www.yahoo.fr");

$mr->listen();
