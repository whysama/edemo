<?php
require_once 'Geocoder.php';

$search = '1 rue royale 166 bureaux de la colline 93210 Saint Cloud';

$geocoder = new Geocoder('AIzaSyCnhOVoZaxKI2-ZofZTO9ZrGSqZeppYA4Y');
$xml = $geocoder->getLocation($search);

print_r ($xml);

?>