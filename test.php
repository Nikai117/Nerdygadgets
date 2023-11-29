<?php
include __DIR__ . "/header.php";

$klantEmail = addCustomer($_SESSION['klant'], $databaseConnection);
print_r($klantEmail);
?>
