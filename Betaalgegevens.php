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
        <label for="verzenden">Verzendmethode:</label>
        <label>PostNL</label>
        <input type="radio" id="postnl" name="verzenden" value="postnl" required>
        <label>DHL</label>
        <input type="radio" id="dhl" name="verzenden" value="dhl" required>
        <label>DPD</label>
        <input type="radio" id="dpd" name="verzenden" value="dpd" required>
        <label>UPS</label>
        <input type="radio" id="ups" name="verzenden" value="ups" required>
    </div>
    <div id="KlantGegegevens"
    <h1>Klant gegevens</h1><br>

    <label for="mail">Email:</label>
    <input type="email" name="email" id="mail" placeholder="Emailadres" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br><br>
    </div>
    <div id="Besteladres"></div>
    <h1>Besteladres</h1><br>
    <label for="vnaam">Voornaam:</label><input type="text" name="voornaam" id="vnaam" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <label for="anaam">Achternaam:</label><input type="text" name="achternaam" id="anaam" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <label for="bnaam">Bedrijfsnaam:</label><input type="text" name="bedrijfsnaam" id="bnaam" placeholder="(Optioneel)" style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <label for="adres">Adres:</label><input type="text" name="adres" id="adres" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>
    <label for="flat">Flat:</label><input type="text" name="flat" id="flat" placeholder="(Optioneel)" style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br>

    <label for="land">Land:</label><select style="width: 60%; margin-left: 10%; margin-bottom: 20px">
        <option value="">Selecteer een land</option>
    </select>
    <label for="provincie">Provincie:</label><select style="width: 60%; margin-left: 10%; margin-bottom: 20px">
        <option value="">Selecteer een provincie</option>
    </select>

    <label for="pcode">Postcode:</label><input type="text" name="postcode" id="pcode" required style="width: 60%; margin-left: 10%; margin-bottom: 20px"><br><br>

    <input type="submit" name='knop' required style="width: 30%; align-items: center; justify-content: center; margin-left: 30%">
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
