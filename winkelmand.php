<?php
//onze session array heet "winkelmand". dus je roept het bijvoorbeeld zo op: $_SESSION['winkelmand'][0][0]['UnitPrice']
include __DIR__ . "/header.php";


if(!isset($_SESSION['winkelmand'][0]) || $_SESSION['winkelmand'][0] == NULL) {//het eerste element is leeg -> de hele winkelmand is leeg
    print ("Yarr, de winkelmand is leeg.");
} else {
    #hieronder zijn gewoon printjes voor testen
    foreach($_SESSION['winkelmand'] as $item) {
        print_r($item[0]['UnitPrice']);
        print("<br>");
    }

    print_r($_SESSION['winkelmand'][0][0]);

    var_dump($_SESSION['winkelmand']);
    #einde
}
?>
