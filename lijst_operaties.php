<?php
session_start();
include "database.php";

$databaseConnection = connectToDatabase();


//als je handmatig dit url intypt, word je gestuurd naar home
if(!isset($_POST['operation'])) {
    header("Location: index.php");
} else {
    //data wordt pas hier gestuurd als de klant is ingelogd
    $userID = $_SESSION['activeUser'][0]['userID'];

    #############################################
    #           producten toevoegen             #
    #############################################
    if($_POST['operation'] == "toevoegen_product") {
        $insertSucceeded = insertToWishlist($userID, $_POST['wishlistName'], $_POST['StockItemID'], $databaseConnection);

        if($insertSucceeded) {
            echo json_encode(['success' => true, 'message' => 'Gelukt']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Niet gelukt']);
        }
    }

    #############################################
    #           producten verwijderen           #
    #############################################
    if($_POST['operation'] == "verwijderen_product") {
        $deleteSucceeded = removeFromWishlist($userID, $_POST['wishlistName'], $_POST['StockItemID'], $databaseConnection);

        if($deleteSucceeded) {
            echo json_encode(['success' => true, 'message' => 'Gelukt']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Niet gelukt']);
        }
    }

    #############################################
    #               lijst aanmaken              #
    #############################################
    if($_POST['operation'] == "maken_lijst") {
        $createSucceeded = createWishlist($userID, $_POST['newListName'], $databaseConnection);

        if($createSucceeded) {
            echo json_encode(['success' => true, 'message' => 'Gelukt']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Niet gelukt! Naam bestaat al.']);
        }
    }

    #############################################
    #              lijst verwijderen            #
    #############################################    
    if($_POST['operation'] == "verwijderen_lijst") {
        $deleteSucceeded = deleteWishlist($userID, $_POST['wishlistName'], $databaseConnection);

        if($deleteSucceeded) {
            echo json_encode(['success' => true, 'message' => 'Gelukt']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Niet gelukt']);
        }
    }
}