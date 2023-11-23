<?php
include __DIR__ . "/header.php";

print_r($_SESSION['winkelmand'][98]);
updateStocks(98, $_SESSION['winkelmand'][98]['aantal'], $databaseConnection);
print_r($_SESSION['winkelmand'][98]);
?>
