<?php
session_start();
include "database.php";

$databaseConnection = connectToDatabase();

//als je handmatig dit url intypt, word je gestuurd naar home
if(!isset($_POST['wishlistName'], $_POST['StockItemID'])) {
    header("Location: index.php");
} else {
    $userID = $_SESSION['activeUser'][0]['userID'];
    $insertSucceeded = insertToWishlist($userID, $_POST['wishlistName'], $_POST['StockItemID'], $databaseConnection);

    if($insertSucceeded) {
        echo json_encode(['success' => true, 'message' => 'Gelukt']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Niet gelukt']);
    }
}