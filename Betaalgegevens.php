<?php
include __DIR__ . "/header.php";
?>

<div id="lijst">
        <table id="winkelmand">
            <!-- maak tabelvakje aan per product -->
            <?php foreach ($_SESSION['winkelmand'] as $key => $product) {

    $StockItemImage = getStockItemImage($product['StockItemID'], $databaseConnection);
    $BackupImage = getBackupImage($product['StockItemID'], $databaseConnection);

    if ($StockItemImage != NULL) {
        $image = '<img src="Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'] . '" class="itemimage"></a>';
    } else {
        $image = '<img src="Public/StockGroupIMG/' . $BackupImage['ImagePath'] . '" class="itemimage"></a>';
    }

    print ('<tr class="product"><td>
                    <div id="productCard">
                        <div id="image_card">
                        <a href="view.php?id=' . $product['StockItemID'] . '">
                            <h5>' . $product['StockItemName'] . '</h5>'. $image . '
                        </a>
                        </div>
                        <p class="prijs">€' . number_format(round($product['SellPrice'], 2), 2, '.', '') . '</p>
                        <p class="aantal">' . $product['aantal'] . '</p>
                    ');
    } ?>

        </table>
    </div>
