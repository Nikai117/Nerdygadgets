<?php
include __DIR__ . "/header.php";
print_r($_SESSION['activeUser']);
if($_SESSION['activeUser'] == NULL) {
    echo 'hoi';
}