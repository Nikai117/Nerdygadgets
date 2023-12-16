<?php
session_start();
include "database.php";

$databaseConnection = connectToDatabase();

if(!isset($_POST['newListName'])) {
    header("Location: index.php");
} else {
    //data wordt pas hier gestuurd als de klant is ingelogd
    $userID = $_SESSION['activeUser'][0]['userID'];
    $insertSucceeded = createWishlist($userID, $_POST['newListName'], $databaseConnection);

    if($insertSucceeded) {
        echo json_encode(['success' => true, 'message' => 'Gelukt']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Niet gelukt! Naam bestaat al.']);
    }
}