<?php
include __DIR__ . "/header.php";

  //paid, canceled, expired, failed,
  if (isset($_GET["order"])){
    if($_GET["order"] == "paid"){
        if(isset($_SESSION['paid'])) {
?>
    <br><h1>Betaling is succesvol!</h1><br>
    <br><h2>Uw bestelling:</h2><br>
<div id="lijst">
<table id="bestelling">
    <!-- maak tabelvakje aan per product -->
    <?php if(isset($_SESSION['winkelmand'])) {
     foreach ($_SESSION['winkelmand'] as $key => $product) {

        $StockItemImage = getStockItemImage($product['StockItemID'], $databaseConnection);
        $BackupImage = getBackupImage($product['StockItemID'], $databaseConnection);

        if ($StockItemImage != NULL) {
            $image = '<img src="Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'] . '" class="itemimage"></a>';
        } else { 
            $image = '<img src="Public/StockGroupIMG/' . $BackupImage['ImagePath'] . '" class="itemimage"></a>';
        }

        // print ('<tr class="product"><td>
        //     <div id="productCard">
        //         <div id="image_card">
        //             <h5>' . $product['StockItemName'] . ' | Artikelnummer: ' . $product['StockItemID'] . '</h5>'. $image . '
        //         </div>
        //         <p class="prijs">€' . number_format(round($product['SellPrice'], 2), 2, '.', '') . '</p>
        //         <p class="aantal">' . $product['aantal'] . '</p>
        //     ');

        print('<div>
                    <h5>' . $product['StockItemName'] . ' | Artikelnummer: ' . $product['StockItemID'] .' | €' . number_format(round($product['SellPrice'], 2), 2, '.', '') . ' x '. $product['aantal'] . '</h5><br>
                    '. $image .'
                </div>');
            
        print("<br></div></td></tr>");
    }
    $_SESSION['winkelmand'] = array();
    unset($_SESSION['paid']);
    print('<button type="button" onclick="redirectToHome()" id="href">Naar hoofdpagina</button>');
    } else {
        header("Location: index.php");
    } ?>
</table>
</div>

<?php
} else {
    print('<br><h1>U hoort hier niet te zijn.</h1><br>');
    print('<button type="button" onclick="redirectToHome()" id="href">Naar hoofdpagina</button>');
}

}
    elseif ($_GET["order"] == "canceled"){
    ?>
        <br><h1>Betaling is geannuleerd.</h1><br>
<?php
    print('<button type="button" onclick="redirectToHome()" id="href">Naar hoofdpagina</button>');
}
    elseif ($_GET["order"] == "expired"){
    ?>
        <br><h1>Betaling is verlopen, probeer opnieuw.</h1><br>
<?php
    print('<button type="button" onclick="redirectToPayment()" id="href">Naar betalen</button>');    
}
    elseif ($_GET["order"] == "failed"){
    ?>
        <br><h1>Betaling is helaas niet gelukt, probeer opnieuw.</h1><br>
<?php
    print('<button type="button" onclick="redirectToPayment()" id="href">Naar betalen</button>'); 
}
        else {header("location: winkelmand.php");}
        }
else {header("location: winkelmand.php");}
?>
<br>
<script>
    function redirectToHome() {
        // Hier kun je eventueel wat JavaScript-code toevoegen voordat je doorverwijst
        window.location.href = 'index.php';
    }
    function redirectToPayment() {
        window.location.href = 'Betaalgegevens.php';
    }
</script>

<style>
.itemimage {
    height: 120px;
    width: 120px;
    float: left;
}
#href {
        display: block;
        margin: 0 auto;
        margin-bottom: 20px;
        width: 30%;
        background-color: Blue;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

#href:hover {
    background-color: darkblue;
}
</style>