<?php
session_start();
include "database.php";

$databaseConnection = connectToDatabase();

if(!isset($_SESSION['korting']) || $_SESSION['korting'] == NULL) {
    $_SESSION['korting'] = array();
}


if (ISSET($_POST['kortingscode'])) {

    $ingevuldeCode = $_POST['kortingscode']; // Hiermee neem je aan dat de kortingscode wordt ingevoerd

// Controleer de geldigheid van de kortingscode
    $result = discountCodeCheck($ingevuldeCode, $databaseConnection);

//Wordt nog verranderd

    if ($result != NULL) {
        $code = $result[0]["code"];
        $korting_percentage = $result[0]["korting_percentage"];

        $_SESSION['korting']['percentage'] = $korting_percentage;
        $_SESSION['korting']['code'] = $code;
        header("Location: winkelmand.php");
    } else {
        $_SESSION['korting']['ongeldig'] = true;
        header("Location: winkelmand.php");
    }
} else {
    header("Location: index.php");
}
?>