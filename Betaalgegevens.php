<?php
ob_start();
include __DIR__ . "/header.php";
require_once __DIR__ . "/mollie-api-php/vendor/autoload.php";
require_once __DIR__ . "/mollie-api-php/examples/functions.php";
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_fJJbkmF9gjs3JsrzaNapaAF68dVv9C");
?>

<div id="lijst">
        <table id="winkelmand">
            <!-- maak tabelvakje aan per product -->
            <?php foreach ($_SESSION['winkelmand'] as $key => $product) {

    $StockItemImage = getStockItemImage($product['StockItemID'], $databaseConnection);
    $BackupImage = getBackupImage($product['StockItemID'], $databaseConnection);

    if ($StockItemImage != NULL) {
        $image = '<img src="Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'] . '" class="itemimage" style="width: 20%; height: 30%"></a>';
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
<form>
    <div id="VerzendMethode">
        <p><span style="font-weight:bold;">Verzendmethode:</span></p>
        <input type="radio" id="postnl" name="verzenden" value="postnl" required>
        <label>PostNL</label>
        <input type="radio" id="dhl" name="verzenden" value="dhl" required>
        <label>DHL</label>
        <input type="radio" id="dpd" name="verzenden" value="dpd" required>
        <label>DPD</label>
        <input type="radio" id="ups" name="verzenden" value="ups" required>
        <label>UPS</label>
    </div>
    <br>
    <p class="solid"></p>
    <div id="KlantGegevens">
    <h1>Klant gegevens</h1><br>
    <input type="email" name="email" id="mail" placeholder="E-mailadres" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br><br>
    </div>

    <div id="Besteladres">
    <h1>Besteladres</h1><br>
    <input type="text" name="voornaam" id="vnaam" placeholder="Voornaam" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <input type="text" name="achternaam" id="anaam" placeholder="Achternaam" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
        <input type="text" name="telnummer" id="telnummer" placeholder="Telefoonnummer" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <input type="text" name="bedrijfsnaam" id="bnaam" placeholder="Bedrijfsnaam (optioneel)" style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <input type="text" name="adres" id="adres" placeholder="Adres" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <input type="text" name="toevoeging" id="toevoeging" placeholder="Toevoeging (optioneel)" style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>

    <select style="width: 60%; margin-left: 10%; margin-bottom: 20px">
        <option value="">Land</option>
    </select>

<!--    <label for="provincie">Provincie:</label><select style="width: 60%; margin-left: 10%; margin-bottom: 20px">-->
<!--        <option value="">Selecteer een provincie</option>-->
<!--    </select>-->

    <input type="text" name="postcode" id="pcode" placeholder="Postcode" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br><br>

    <input type="submit" name='knop' value="Verder naar betalen" required style="width: 30%; align-items: center; justify-content: center; margin-left: 30%">
    </div>
</form>

<?php
if(isset($_GET['knop'])){
    print("U wordt nu doorverwezen naar de betaalpagina");
    $totPrijs = $_SESSION['totaalprijs'];
    $orderNum = "Order ".random_int(1, 400);
    try {
        $payment = $mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => $totPrijs // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            "description" => $orderNum,
            "redirectUrl" => "http://localhost/nerdygadgets/betaalgegevens.php",
            "metadata" => [
                "order_id" => "12345",
            ],
        ]);

        header("Location: " . $payment->getCheckoutUrl());
        ob_end_flush();
        exit();
    } catch ( Exception $exception) {
        print ($exception);
    }
}
?>

<!-- plaats alles wat niet PHP is voorlopig onderaan-->
<style>
    /*body {*/
    /*    font-family: Arial, sans-serif;*/
    /*    background-color: #f8f8f8;*/
    /*    margin: 0;*/
    /*    padding: 0;*/
    /*}*/

    /*form {*/
    /*    max-width: 600px;*/
    /*    margin: 20px auto;*/
    /*    background-color: #fff;*/
    /*    padding: 20px;*/
    /*    border-radius: 8px;*/
    /*    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);*/
    /*}*/

    #lijst {
        margin-left: 50px;
        width: 60%;
        padding: 10px;
        background-color: #2C2F33;
        /*box-shadow: 5px 5px 5px 15px #603980;*/
        margin-top: 10px;
        border-radius: 10px;
        /*background-color: lightblue;*/
        width: 1000px;
        height: 1000px;
        overflow: auto;
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

    #VerzendMethode {
        float: right;
        display: block;
        background-color: #2C2F33;
        border-radius: 5px;
        margin-right: 200px;
        margin-bottom: 20px;
        margin-top: 50px;
        width: 15%;
        padding: 10px;
    }

    input[type=radio] {
        border: 0px;
        width: 10%;
        height: 1.5em;
        float: left;
        margin-right: 10px;
        margin-left: 10px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    p {
        font-size: larger;
    }

    #KlantGegevens {
        margin-top: 20px;
    }

    p.solid {
        border-style: solid;
        width: 60%;
        margin-left: 50px;
        color: purple;
    }

    #Besteladres {
        margin-top: 20px;

    }

    h1 {
        /*color: #333;*/
        font-size: larger;
        margin-left: 70px;
    }

    input[type="text"],
    input[type="email"],
    select {
        width: 60%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    input[type="submit"] {
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

    input[type="submit"]:hover {
        background-color: darkblue;
    }
