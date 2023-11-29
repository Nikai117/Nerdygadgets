<?php
include __DIR__ . "/header.php";

  //paid, canceled, expired, failed,
    if (isset($_GET["order"])){
        if($_GET["order"] == "paid"){
    ?>
    <div id="prijs">
        <?php
        //checkt het totaalbedrag
        if (isset($_SESSION['winkelmand'])) {
            $productTotaal = 0;
            $productUnits = 0;

            foreach ($_SESSION['winkelmand'] as $product) {
                $productTotaal += $product['SellPrice'] * $product['aantal'];
                $productUnits = $productUnits + $product['aantal'];
            }

            //verzendkosten berekenen
            if ($productTotaal <= 300) {
                //gebruik je voor berekeningen
                $verzendkosten = number_format(35.50, 2);
                //gebruik je voor tekst en UI
                $verzendkostenText = "€" . $verzendkosten;
            } else {
                $verzendkosten = 0;
                $verzendkostenText = "gratis";
            }

            //service kosten berekenen
            if ($productUnits <= 5) {
                //gebruik je voor berekeningen
                $serviceKosten = number_format(22.50, 2);
                //gebruik je voor tekst en UI
            } else {
                $serviceKostenBerekening = 22.50 + (2.50 * $productUnits);
                $serviceKosten = number_format($serviceKostenBerekening, 2);
            }
            $serviceKostenText = "€" . $serviceKosten;

            //zodat afgeronde getallen altijd 2 decimalen hebben (9.6 => 9.60)
            $productTotaal = number_format(round($productTotaal, 2), 2, '.', '');

            print("Productkosten: €" . $productTotaal . "<br>");
            print("Verzendkosten: $verzendkostenText<br>");
            print("<p style='color: black'>Servicekosten: $serviceKostenText</p><br>");

            //Check of er producten limiet wordt overschreden
            if ($productUnits <= 500) {
                print ("<p style='color: darkolivegreen; font-weight: bold'>Totaal bedrag: €" . number_format(($productTotaal + $verzendkosten + $serviceKosten), 2) . "</p>");
                $_SESSION['totaalprijs'] = number_format(($productTotaal + $verzendkosten + $serviceKosten), 2);
            } else
                print ("Verzenden niet mogelijk door te hoog aantal producten, bel service desk a.u.b.");
            print ("<br><i>Inclusief BTW</i>");
        } ?>
    </div>
        <br><h1>Betaling is succesvol!</h1><br>
        <br><h2>Uw bestelling:</h2><br>
    <div id="lijst">
    <table id="bestelling">
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
                        <h5>' . $product['StockItemName'] . ' | Artikelnummer: ' . $product['StockItemID'] . '</h5>'. $image . '
                    </div>
                    <p class="prijs">€' . number_format(round($product['SellPrice'], 2), 2, '.', '') . '</p>
                    <p class="aantal">' . $product['aantal'] . '</p>
                ');
                
            print("<br></div></td></tr>");
        } ?>
    </table>
    </div>

<?php
}
    elseif ($_GET["order"] == "canceled"){
    ?>
        <br><h1>Betaling is geannuleerd.</h1><br>
<?php
}
    elseif ($_GET["order"] == "expired"){
    ?>
        <br><h1>Betaling is verlopen, probeer opnieuw.</h1><br>
<?php
}
    elseif ($_GET["order"] == "failed"){
    ?>
        <br><h1>Betaling is helaas niet gelukt, probeer opnieuw.</h1><br>
<?php

}
        else {header("location: winkelmand.php");}
        }
else {header("location: winkelmand.php");}
?>
<style>
    .redirect {
        all: unset;
    }

    .redirect:hover {
        color: #a08ee6;
        cursor: pointer;
    }

    #lijst {
        margin-left: 50px;
        width: 60%;
        padding: 10px;
        background-color: #2C2F33;
        /*box-shadow: 5px 5px 5px 15px #603980;*/
        margin-top: 10px;
        border-radius: 10px;
    }

    #bestelling {
        width: 100%;
        margin: auto;
    }

    #bestelling a {
        all: unset;
        text-shadow: 2px 2px 2px black;
    }

    #bestelling a:hover {
        cursor: pointer;
        color: #a08ee6;
    }

    .itemimage {
        height: 120px;
        width: 120px;
        float: left;
    }

    .product {
        position: relative;
    }

    .prijs {
        color: #676EFF;
        float: right;
        font-family: vortice-concept, sans-serif;
        font-weight: bold;      
        text-shadow: 2px 2px 2px black;
    }

    .quantityButtons {
        float: right;
    }

    .aantal {
        background-color: #76499c;
        position: absolute;
        float: right;
        bottom: 0;
        left: 65px;
        width: 50px;
        border: 1px solid;
        text-align: center;
        text-shadow: 2px 2px 2px black;
    }

    .removebutton {
        background-color: #b88a04;
        border-radius: 30px;
        float: right;
        margin-top: 30%;
        bottom: 14px; 
        left: 10px;
        width: 50px;
    }

    .removebutton:hover {
        background-color: #735602;
    }

    .addbutton {
        background-color: #02c205;
        margin-top: 30%;
        border-radius: 30px;
        bottom: 14px; 
        left: 120px;
        width: 50px;
    }

    .addbutton:hover {
        background-color: #018203;
    }

    .bin {
        background-color: #f2f2f2;
        float: left;
    }

    .bin:hover {
        background-color: #d6d6d6;
    }

    #prijs {
        float: right;
        border-radius: 10px;
        background-color: whitesmoke;
        color: black;
        width: 18%;
        text-align: left;
        padding-left: 10px;
        margin-right: 5%;
        margin-top: 10px;
    }

    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    }

    input[type=number] {
    -moz-appearance: textfield;
    }
</style>