<?php
ob_start();
include __DIR__ . "/header.php";
require_once __DIR__ . "/mollie-api-php/vendor/autoload.php";
require_once __DIR__ . "/mollie-api-php/examples/functions.php";
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_fJJbkmF9gjs3JsrzaNapaAF68dVv9C");
?>

<br><h2>Winkelmand</h2>

    <div id="prijs">
        <h3>Overzicht</h3><br>
        <?php
        //checkt het totaalbedrag
        if (isset($_SESSION['winkelmand']) && isset($_SESSION['producttotaal'], $_SESSION['verzendkosten'], $_SESSION['servicekosten'])) {
            if ($_SESSION['winkelmand'] == NULL) {
                header("Location: winkelmand.php");
            }
            if ($_SESSION['producttotaal'] <= 300) {
                $verzendkostenText = "€" . $_SESSION['verzendkosten'];
            } else {
                $verzendkostenText = "gratis";
            }

            $kortingText = "";
            if(isset($_SESSION['prijsaftrek'])) {
                if($_SESSION['prijsaftrek'] > 0) {
                    $kortingText = "Korting: - €".$_SESSION['prijsaftrek']."";
                }
            }

            print("Productkosten: €" . $_SESSION['producttotaal'] . "<br><br>");
            print("Verzendkosten: $verzendkostenText<br><br>");
            print("Servicekosten: €".$_SESSION['servicekosten']."<br><br>");
            print($kortingText."<br>");

            $productUnits = 0;
            foreach ($_SESSION['winkelmand'] as $product) {
                $productUnits = $productUnits + $product['aantal'];
            }
?>
            <p class="solid3"></p>

            <?php
            //Check of er producten limiet wordt overschreden
            if ($productUnits <= 500) {
                print ("<p style='color: darkolivegreen; font-weight: bold'>Totaal bedrag: €" . number_format(($_SESSION['producttotaal'] + $_SESSION['verzendkosten'] + $_SESSION['servicekosten'] - $_SESSION['prijsaftrek']), 2) . "</p>");
            } else
                print ("Verzenden niet mogelijk door te hoog aantal producten");
            print ("<br><i>Inclusief BTW (21%)</i>");
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
                            <h5>' . $product['StockItemName'] . '</h5>' . $image . '
                        </a>
                        </div>
                        <p class="prijs">€' . number_format(round($product['SellPrice'], 2), 2, '.', '') . '</p>
                        <p class="aantal">' . $product['aantal'] . '</p>
                    ');
        }

        //Hier moet nog een andere manier komen van splitten
        function splitValueBySpace($string)
        {
            return explode(' ', $string);
        }

        if (isLoggedIn()) {
            $user = $_SESSION['activeUser'];
            $activeUserFullName = splitValueBySpace($user[0]['CustomerName']);
            $activeUserFirstName = $activeUserFullName[0];
            $activeUserLastName = $activeUserFullName[1];

            $activeUserFullAdres = splitValueBySpace($user[0]['DeliveryAddressLine1']);
            $activeUserAdres = $activeUserFullAdres[0];
            $activeUserAdresAdd = $activeUserFullAdres[1];

        } else {
            $user = "";
            $activeUserFirstName = "";
            $activeUserLastName = "";
            $activeUserAdres = "";
            $activeUserAdresAdd = "";
        }

        //Vult automatisch velden in als je ingelogd bent en als je niet ingelogd bent een lege string
        function autoFillIn($string)
        {
            if (isLoggedIn()) {
                $userData = $_SESSION['activeUser'];
                if ($userData[0][$string] == NULL) {
                    print ("");
                } else {
                    print ($userData[0][$string]);
                }
            } else {
                print ("");
            }
        }

        ?>
    </table>
</div>

<form method="post">
    <div id="VerzendMethode">
        <p><span style="font-weight:bold;">Verzendmethode:</span></p>
        <input type="radio" id="postnl" name="verzenden" value="postnl" required checked>
        <label for="postnl">PostNL</label>
        <input type="radio" id="dhl" name="verzenden" value="dhl" required>
        <label for="dhl">DHL</label>
        <input type="radio" id="dpd" name="verzenden" value="dpd" required>
        <label for="dpd">DPD</label>
        <input type="radio" id="ups" name="verzenden" value="ups" required>
        <label for="ups">UPS</label>
    </div>
    <br><br>

    <p class="solid1"></p>

    <div id="KlantGegegevens">
        <h1>Klantgegevens</h1><br>
        <?php
        //If you are logged in, you are not required to fill in personal information
        if (!isLoggedIn()) {
            echo '
    <input type = "email" name = "email" id = "mail" placeholder = "E-mailadres" value = "', autoFillIn('Email'), '" required style = "width: 60%; margin-left: 50px; margin-bottom: 20px" ><br >
    <input type = "text" name = "voornaam" id = "vnaam" placeholder = "Voornaam" value = "', $activeUserFirstName, '" required style = "width: 29.5%; margin-left: 50px; margin-bottom: 20px" >
    <input type = "text" name = "achternaam" id = "anaam" placeholder = "Achternaam" value = " ', $activeUserLastName, '" required style = "width: 29.5%; margin-left: 10px; margin-bottom: 20px" >
    <input type = "text" name = "bedrijfsnaam" id = "bnaam" placeholder = "Bedrijfsnaam (Optioneel)" style = "width: 60%; margin-left: 50px; margin-bottom: 20px" ><br >
    <input type = "text" name = "telnummer"  id = "telnummer" placeholder = "Telefoonnummer" value = "', autoFillIn('PhoneNumber'), '" required style = "width: 60%; margin-left: 50px; margin-bottom: 20px" ><br >
    </div >
    ';
        } else {
            echo '
        <input type = "email" name = "email" id = "mail" placeholder = "E-mailadres" value = "', autoFillIn('Email'), '" required style = "width: 60%; margin-left: 50px; margin-bottom: 20px" readonly><br >
    <input type = "text" name = "voornaam" id = "vnaam" placeholder = "Voornaam" value = "', $activeUserFirstName, '" required style = "width: 29.5%; margin-left: 50px; margin-bottom: 20px"  readonly>
    <input type = "text" name = "achternaam" id = "anaam" placeholder = "Achternaam" value = " ', $activeUserLastName, '" required style = "width: 29.5%; margin-left: 10px; margin-bottom: 20px"  readonly>
    <input type = "text" name = "bedrijfsnaam" id = "bnaam" placeholder = "Bedrijfsnaam (Optioneel)" style = "width: 60%; margin-left: 50px; margin-bottom: 20px" readonly><br >
    <input type = "text" name = "telnummer"  id = "telnummer" placeholder = "Telefoonnummer" value = "', autoFillIn('PhoneNumber'), '" required style = "width: 60%; margin-left: 50px; margin-bottom: 20px" readonly><br >
    </div >
     ';
        }
        ?>
        <br>

        <p class="solid2"></p>

        <div id="Besteladres">
            <h1>Besteladres</h1><br>

            <input type="text" name="adres" id="adres" placeholder="Straatnaam" value="<?php print ($activeUserAdres); ?>"
                   required style="width: 21.5%; margin-left: 50px; margin-bottom: 20px">
            <input type="text" name="toevoeging" id="toevoeging" placeholder="Huisnummer"
                   value="<?php print ($activeUserAdresAdd); ?>"
                   style="width: 15%; margin-left: 10px; margin-bottom: 20px">
            <input type="text" name="postcode" id="pcode" placeholder="Postcode"
                   value="<?php autoFillIn('DeliveryPostalCode'); ?>" required
                   style="width: 21.5%; margin-left: 10px; margin-bottom: 20px">
            <br>
            <select required style="width: 60%; margin-left: 50px; margin-bottom: 20px">
                <option>Nederland</option>
                <option>België</option>
                <option>Duitsland</option>
                <option>Frankrijk</option>
                <option>Luxemburg</option>
                <option>Oostenrijk</option>
            </select>

            <br><br>

    <input type="submit" name='knop' value="Verder naar betalen" required style="width: 26.5%; align-items: center; justify-content: center;">
    </div>
</form>

<?php
if (isset($_POST['knop'])) {
    print("U wordt nu doorverwezen naar de betaalpagina");
    $_SESSION['klant']['naam'] = ucfirst(strtolower($_POST['voornaam'])) . " " . ucfirst(strtolower($_POST['achternaam']));
    $_SESSION['klant']['adres'] = $_POST['adres'] . " " . $_POST['toevoeging'];
    $_SESSION['klant']['postcode'] = $_POST['postcode'];
    $_SESSION['klant']['email'] = $_POST['email'];
    $_SESSION['klant']['telnummer'] = $_POST['telnummer'];

    $totPrijs = $_SESSION['totaalprijs'];
    $orderNum = "Order " . random_int(1, 400);
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
    } catch (Exception $exception) {
        print ($exception);
    }
}

if ($_SESSION['payment_id'] != NULL) {
    $paymentId = $_SESSION['payment_id'];
    $payment = $mollie->payments->get($paymentId);

    switch ($payment->status) {
        case "open":
            header("Location: " . $payment->getCheckoutUrl());
            break;
        case "expired":
            $_SESSION['payment_id'] = NULL;
            header("location: resultaat.php?order=expired");
            break;
        case "failed":
            $_SESSION['payment_id'] = NULL;
            header("location: resultaat.php?order=failed");
            break;
        case "canceled":
            $_SESSION['payment_id'] = NULL;
            header("location: resultaat.php?order=canceled");
            break;
        case "paid":
            $_SESSION['payment_id'] = NULL;

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
        width: 27%;
        text-align: left;
        margin-right: 3%;
        padding: 20px;
    }

    h3 {
        /*color: #333;*/
        font-size: 25px;
        margin-bottom: 15px;
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
        margin-top: 20px;
        margin-left: 5%;
    }

    #VerzendMethode p {
        font-weight: bold;
    }

    #VerzendMethode input[type="radio"] {
        display: none;
    }

    #VerzendMethode label {
        display: inline-block;
        padding: 10px;
        margin: 5px;
        font-size: 14px;
        cursor: pointer;
        background-color: #3498db;
        color: #fff;
        border-radius: 4px;
        width: 100px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    #VerzendMethode input[type="radio"]:checked + label {
        background-color: #000000;
    }

    /* Optional: Hover effect */
    #VerzendMethode label:hover {
        background-color: #2075c0;
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

    p.solid3 {
        border-style: solid;
        width: 100%;
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

    h2 {
        /*color: #333;*/
        font-size: 50px;
        margin-left: 60px;
        margin-bottom: 30px;
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
    </style>