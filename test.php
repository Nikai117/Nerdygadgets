<?php
include __DIR__ . "/header.php";

print_r($_SESSION['activeUser']);
unset($_SESSION['activeUser']);

