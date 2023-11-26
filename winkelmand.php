<?php
include __DIR__ . "/header.php";

function removeRow($arr, $index)
{
    unset($arr[$index]);
    array_splice($arr, $index, 0);//om de NULL row te verwijderen die je krijgt van unset()

    return $arr;
}

if (!isset($_SESSION['winkelmand']) || $_SESSION['winkelmand'] == NULL) {
    print('<h1 style="text-align: center">Yarr, de winkelmand is leeg</h1>
            <h2 style="text-align: center"><a class="redirect" href="browse.php"> Op zoek naar producten?</a></h2>');
} else {
    if ($_POST != NULL) {//actionTarget kan zijn meegegeven, maar mag niet NULL zijn

        //declareert de key van het product waarop de actie zal worden uitgevoerd
        foreach ($_POST as $value) {
            $key = $value;
        }

        if (isset($_SESSION['winkelmand'][$key])) {//pre-conditie: $actionTarget moet een array key zijn

            //loopt een keer door post om action te krijgen en target. alleen maar else-ifs voor als bijv. iemand met inspect de name heeft aangepast
            foreach ($_POST as $action => $target) {

                if ($action == "remove") {
                    if ($_SESSION['winkelmand'][$target]['aantal'] == 1)//bij 0 producten uit de mand verwijderen
                        $_SESSION['winkelmand'] = removeRow($_SESSION['winkelmand'], $target);
                    else
                        $_SESSION['winkelmand'][$target]['aantal']--;

                    header("Location: winkelmand.php");

                } elseif ($action == "add") {   
                    $voorraad = getQuantity($target, $databaseConnection);
                    $QOH = $voorraad[0]["QOH"];//quantity on hand; voorraad

                    if($_SESSION['winkelmand'][$target]['aantal'] < $QOH) {
                        $_SESSION['winkelmand'][$target]['aantal']++;
                    } else {
                        //hulp variabele om error te bepalen
                        $_SESSION['error'] = "overschot";
                    }
                    
                    //header("Location: winkelmand.php");

                } elseif ($action == "clear") {
                    $_SESSION['winkelmand'] = removeRow($_SESSION['winkelmand'], $target);
                }
            }

            if ($_SESSION['winkelmand'] == NULL)
                header("Location: winkelmand.php");
        }
    }
    ?>

    <div id="error">
    <?php 
    if(isset($_SESSION['error'])) {
        if($_SESSION['error'] == "overschot") {
            print('<h5>Toevoegen is niet gelukt! Geen units meer over in voorraad.</h5>');
        }
        unset($_SESSION['error']);
    }
    ?>
    </div>

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
            } else
                print ("Verzenden niet mogelijk door te hoog aantal producten, bel service desk a.u.b.");
            print ("<br><i>Inclusief BTW</i>");
        } ?>
    </div>

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

                print('
                    <div id="quantityButtons">
                    <form method="post" action="winkelmand.php" style="float: right">
                        <button type="submit" name="remove" value="' . $key . '" class="removebutton">-</button>' .//verwijderen van product
                    '<button type="submit" name="add" value="' . $key . '" class="addbutton">+</button>' .//voeg 1 product toe
                    '<button type="submit" name="clear" value="' . $key . '" class="bin" style="position: absolute; top: 0; right: 0;"><img alt="Prullenbak" src="Public/Img/Prullenbak.png" width="20" height="20"></button>' .
                    '</form>
                    </div>
                    ');
                    
                print("<br></div></td></tr>");
            } ?>

        </table>
    </div>
    <script>
        function redirectToAnotherPage() {
            // Hier kun je eventueel wat JavaScript-code toevoegen voordat je doorverwijst
            window.location.href = 'Betaalgegevens.php';
        }
    </script>

    <form method="post" action="">
        <button type="button" onclick="redirectToAnotherPage()">Betalen</button>
    </form>

<?php }
?>

<!-- plaats alles wat niet PHP is voorlopig onderaan-->
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

    #winkelmand {
        width: 100%;
        margin: auto;
    }

    #winkelmand a {
        all: unset;
        text-shadow: 2px 2px 2px black;
    }

    #winkelmand a:hover {
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