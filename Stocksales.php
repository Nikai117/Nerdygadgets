<?php
include __DIR__ . "/header.php";

$stocksaleitem = addStocksaleItem($databaseConnection);

?>

<?php
if ($stocksaleitem != NULL) {
    echo '<h2 style="margin-left: 50px;">Afgeprijsde producten:</h2>';
    echo '<ul style="list-style: none;">'; // Add style to remove list-style

    foreach ($stocksaleitem as $row) {
        echo '<li style="background-color: #595e6b; padding: 20px; margin-bottom: 10px; border-radius: 30px; position: relative; width: calc(100% - 2cm);">';

        // Apply different styles to StockItemName
        echo '<strong style="font-size: 18px; font-family: \'YourFont\', sans-serif; color: white;">' . $row['StockItemName'] . '</strong><br>';

        $leftPosition = 40 / 2.54 * 96; // Convert 5cm to pixels
        echo '<form style="position: absolute; top: 10px; left: ' . $leftPosition . 'px;" method="post" action="Stocksales.php">';
        echo '<input type="hidden" name="product_id" value="' . $row['StockItemID'] . '">';
        echo '<input type="submit" name="product" value="Bestel nu!" class="small-button">';
        echo '</form>';

        // Voorraadinformatie rechts van de bestelknop
        echo '<div style="position: absolute; top: 10px; left: ' . ($leftPosition + 160) . 'px;">';
        echo '<strong>Voorraad:</strong> ' . $row['QuantityOnHand'] . '<br>';
        echo '</div>';

        $StockItemImage = getStockItemImage($row['StockItemID'], $databaseConnection);

        // Display the stock item image
        $BackupImage = getBackupImage($row['StockItemID'], $databaseConnection);

        if ($StockItemImage != NULL) {
            $image = 'Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'];
        } else {
            $image = 'Public/StockGroupIMG/' . $BackupImage['ImagePath'];
        }
        echo '<img src="' . $image . '" alt="Product Image" style="max-width: 150px; max-height: 150px;"><br>';

        echo '</li>';
    }

    echo '</ul>';
} else {
    echo '<p>No products found with stock more than 200,000.</p>';
}

?>

<?php
if(isset($_POST['product_id'])) {
    $new = addToCart($_POST['product_id'], $databaseConnection);

    foreach ($new as $row) {
        if (!isset($_SESSION['winkelmand'][$row["StockItemID"]])) {
            $row['SellPrice'] *= 0.9;
            $_SESSION['winkelmand'][$row["StockItemID"]] = $row;
            $_SESSION['winkelmand'][$row["StockItemID"]]['aantal'] = 1;

        } else {
            $row['SellPrice'] *= 0.9;
            $_SESSION['winkelmand'][$row["StockItemID"]]['aantal']++;
        }
    }
}
?>
<style>
    .small-button {
        width: 150px;
        height: 30px;
        background-color: #4CAF50; /* Green color */
        border: none;
        color: white;
        border-radius: 10px; /* Rounded corners */
    }
</style>
