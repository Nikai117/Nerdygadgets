<?php
ob_start();
include __DIR__ . "/header.php";
require_once __DIR__ . "/mollie-api-php/vendor/autoload.php";
require_once __DIR__ . "/mollie-api-php/examples/functions.php";
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_fJJbkmF9gjs3JsrzaNapaAF68dVv9C");
?>

<div id="prijs">
    <?php
    //checkt het totaalbedrag
    if (isset($_SESSION['winkelmand'])) {
        if($_SESSION['winkelmand'] == NULL) {
            header("Location: winkelmand.php");
        }
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
        print("<p>Servicekosten: $serviceKostenText</p><br>");

        //Check of er producten limiet wordt overschreden
        if ($productUnits <= 500) {
            print ("<p style='color: darkolivegreen; font-weight: bold'>Totaal bedrag: €" . number_format(($productTotaal + $verzendkosten), 2) . "</p>");
            $_SESSION['totaalprijs'] = number_format(($productTotaal + $verzendkosten + $serviceKosten), 2);
            $_SESSION['producttotaal'] = $productTotaal;
            $_SESSION['verzendkosten'] = $verzendkosten;
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
    <br><br>

    <p class="solid1"></p>

    <div id="KlantGegegevens">
    <h1>Klantgegevens</h1><br>

    <input type="email" name="email" id="mail" placeholder="E-mailadres" required style="width: 60%; margin-left: 50px; margin-bottom: 20px"><br>
    <input type="text" name="voornaam" id="vnaam" placeholder="Voornaam" required style="width: 29.5%; margin-left: 50px; margin-bottom: 20px">
    <input type="text" name="achternaam" id="anaam" placeholder="Achternaam" required style="width: 29.5%; margin-left: 10px; margin-bottom: 20px">
    <input type="text" name="bedrijfsnaam" id="bnaam" placeholder="Bedrijfsnaam (Optioneel)" style="width: 60%; margin-left: 50px; margin-bottom: 20px"><br>
    <input type="text" name="telnummer"  id="telnummer" placeholder="Telefoonnummer" required style="width: 60%; margin-left: 50px; margin-bottom: 20px"><br>
    </div>
    <br>

    <p class="solid2"></p>

    <div id="Besteladres">
    <h1>Besteladres</h1><br>

    <input type="text" name="adres" id="adres" placeholder="Adres" required style="width: 21.5%; margin-left: 50px; margin-bottom: 20px">
    <input type="text" name="toevoeging" id="toevoeging" placeholder="Toevoeging (optioneel)" style="width: 15%; margin-left: 10px; margin-bottom: 20px">
        <input type="text" name="postcode" id="pcode" placeholder="Postcode" required style="width: 21.5%; margin-left: 10px; margin-bottom: 20px">
<br>
        <select required style="width: 60%; margin-left: 50px; margin-bottom: 20px">
        <option value="">Selecteer een land</option>
        <option>België</option>
        <option>Duitsland</option>
        <option>Frankrijk</option>
        <option>Luxemburg</option>
        <option>Nederland</option>
        <option>Oostenrijk</option>
      </select>

   <br><br>

    <input type="submit" name='knop' value="Verder naar betalen" required style="width: 30%; align-items: center; justify-content: center; margin-left: 30%">
    </div>
</form>

<?php
if(isset($_POST['knop'])){
    print("U wordt nu doorverwezen naar de betaalpagina");

    //$pattern = "/^[a-z]+$/i";

    $_SESSION['klant']['naam'] = ucfirst(strtolower($_POST['voornaam'])) . " " . ucfirst(strtolower($_POST['achternaam']));
    $_SESSION['klant']['adres'] = $_POST['adres'] . $_POST['toevoeging'];
    $_SESSION['klant']['postcode'] = $_POST['postcode'];
    $_SESSION['klant']['email'] = $_POST['email'];
    $_SESSION['klant']['telnummer'] = $_POST['telnummer'];

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
            header("location: resultaat.php?order=expired");
            break;
        case "failed":
            unset($_SESSION['payment_id']);
            header("location: resultaat.php?order=failed");
            break;
        case "canceled":
            unset($_SESSION['payment_id']);
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
            $_SESSION['paid'] = true;
            header("location: resultaat.php?order=paid");
            break;
    }
}
?>
<!-- plaats alles wat niet PHP is voorlopig onderaan -->
<style>
    #lijst {
        margin-left: 50px;
        width: 60%;
        padding: 10px;
        background-color: #2C2F33;
        /*box-shadow: 5px 5px 5px 15px #603980;*/
        margin-top: 10px;
        border-radius: 10px;
        width: 910px;
        height: 680px;
        overflow: auto;
    }

    #prijs {
        float: right;
        border-radius: 10px;
        background-color: #2C2F33;
        color: white;
        width: 18%;
        text-align: left;
        margin-right: 5%;
        margin-top: 5px;
        padding: 10px;
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
        margin-right: 150px;
        margin-bottom: 20px;
        margin-top: 100px;
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

    p.solid1 {
        border-style: solid;
        width: 60%;
        margin-left: 50px;
        color: purple;
    }

    p.solid2 {
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
    /*    width: 60%;*/
    /*    padding: 8px;*/
    /*    margin-bottom: 10px;*/
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
