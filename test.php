<?php
include __DIR__ . "/header.php";
$r = getOrderDetails($_SESSION['activeUser'][0]['Email'], $databaseConnection);
print_r($r);
print ('<br>');
$y = getOrderLineDetails($r[0]['OrderID'], $databaseConnection);
var_dump($y);