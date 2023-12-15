<?php
session_start();
include "database.php";

$databaseConnection = connectToDatabase();

$userID = 234;

//als je handmatig dit url intypt, word je gestuurd naar home
if(!isset($_POST['wishlistName'], $_POST['StockItemID'])) {
    header("Location: index.php");
} else {
    $insertSucceeded = insertToWishlist($userID, $_POST['wishlistName'], $_POST['StockItemID'], $databaseConnection);

    if($insertSucceeded) {
        echo json_encode(['success' => true, 'message' => 'Gelukt']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Niet gelukt']);
    }
}