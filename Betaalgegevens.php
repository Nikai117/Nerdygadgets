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
<form method="post">
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
    <div id="KlantGegegevens">
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
if(isset($_POST['knop'])){
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
<br><li>
    <a href="winkelmand.php" class="HrefDecoration"><- Terug naar winkelmand</a>
</li><br>

<?php  
 //$pattern = "/^[a-z]+$/i";

    $_SESSION['klant']['naam'] = ucfirst(strtolower($_POST['voornaam'])) . " " . ucfirst(strtolower($_POST['achternaam']));
    $_SESSION['klant']['adres'] = $_POST['adres'];
    $_SESSION['klant']['postcode'] = $_POST['postcode'];
    $_SESSION['klant']['email'] = $_POST['email'];

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
         $_SESSION['payment_id'] = $payment->id;

         ob_end_flush();
         exit();
     } catch ( Exception $exception) {
         print ($exception);
     }

if(isset($_SESSION['payment_id'])) {
    $paymentId = $_SESSION['payment_id'];
    $payment = $mollie->payments->get($paymentId);

    switch($payment->status) {
        case "open":
            header("Location: " . $payment->getCheckoutUrl());
            break;
        case "expired":
            unset($_SESSION['payment_id']);

            $_SESSION['klant'] = array();//klantgegevens verwijderen
            header("location: resultaat.php?order=expired");
            break;
        case "failed":
            unset($_SESSION['payment_id']);

            $_SESSION['klant'] = array();
            header("location: resultaat.php?order=failed");
            break;
        case "canceled":
            unset($_SESSION['payment_id']);

            $_SESSION['klant'] = array();
            header("location: resultaat.php?order=canceled");
            break;
        case "paid":
            unset($_SESSION['payment_id']);

            $email = addCustomer($_SESSION['klant'], $databaseConnection);
            addOrder($_SESSION['klant']['email'], $databaseConnection);

            foreach ($_SESSION['winkelmand'] as $key => $product) {
                addOrderLine($_SESSION['klant']['email'], $databaseConnection, $product);
                updateStocks($key, $product['aantal'], $databaseConnection);
            }

            $_SESSION['klant'] = array();
            $_SESSION['winkelmand'] = array();
            header("location: resultaat.php?order=paid");
            break;
    }
}
?>
<!-- plaats alles wat niet PHP is voorlopig onderaan -->

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

    #KlantGegevens {
        margin-top: 20px;
        margin-left: 40px;
    }

    #Besteladres {
        margin-top: 20px;
        margin-left: 40px;
    }

    h1 {
        /*color: #333;*/
        font-size: larger;
    }

    label {
        display: block;
        margin-bottom: 5px;
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

    #prijs

    /* Responsive styles */
    /*@media screen and (max-width: 600px) {*/
    /*    form {*/
    /*        width: 90%;*/
    /*    }*/
    /*}*/

    /*Include winkelmand tot. prijs, verz, btw*/
