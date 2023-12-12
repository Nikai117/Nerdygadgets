<?php
include __DIR__ . "/header.php";

unset($_SESSION['activeUser']);
header("location: index.php");