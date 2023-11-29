<?php
include __DIR__ . "/header.php";
?>
<!DOCTYPE html>
<html lang="en">

<form>
    <div id="VerzendMethode">
        <label for="verzenden">Verzendmethode:</label>
        <input type="radio" id="postnl" name="verzenden" value="postnl" required>
        <label>PostNL</label>
        <input type="radio" id="dhl" name="verzenden" value="dhl" required>
        <label>DHL</label>
        <input type="radio" id="dpd" name="verzenden" value="dpd" required>
        <label>DPD</label>
        <input type="radio" id="ups" name="verzenden" value="ups" required>
        <label>UPS</label>
    </div>

    <div id="KlantGegevens">
        <h1>Klant gegevens</h1><br>
        <label for="mail">Email:</label>
            <input type="email" name="email" id="mail" placeholder="E-mailadres" required><br><br>
    </div>

    <div id="Besteladres">
        <h1>Besteladres</h1><br>
        <label for="vnaam">Voornaam:</label><input type="text" name="voornaam" id="vnaam" required>
        <label for="anaam">Achternaam:</label><input type="text" name="achternaam" id="anaam" required>
        <label for="bnaam">Bedrijfsnaam:</label><input type="text" name="bedrijfsnaam" id="bnaam" placeholder="(Optioneel)">
        <label for="adres">Adres:</label><input type="text" name="adres" id="adres" required>
        <label for="flat">Flat:</label><input type="text" name="flat" id="flat" placeholder="(Optioneel)"><br>

        <label for="land">Land:</label><select>
            <option value="">Selecteer een land</option>
        </select>
        <label for="provincie">Provincie:</label><select>
            <option value="">Selecteer een provincie</option>
        </select>

        <label for="pcode">Postcode:</label><input type="text" name="postcode" id="pcode" required><br><br>

        <input type="submit" name='knop' value="Verder naar betalen" required>
    </div>
</form>

<?php
if(isset($_GET['knop'])){
    print("U wordt nu doorverwezen naar de betaalpagina");
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

<br><li>
    <a href="winkelmand.php" class="HrefDecoration"><- Terug naar winkelmand</a>
</li><br>
