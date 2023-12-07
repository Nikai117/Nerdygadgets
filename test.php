<?php
include __DIR__ . "/header.php";

$user = getAccountById(112233, $databaseConnection);
print_r($user);

