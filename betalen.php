<?php
include __DIR__ . "/header.php";
?>
<!DOCTYPE html>
<html lang="en">

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
            <input type="email" name="email" id="mail" placeholder="Emailadres" required><br><br>
    </div>
    <div id="Besteladres"></div>
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

        <input type="submit" name='knop' required>
    </div>
</form>

<?php
if(isset($_GET['knop'])){
    print("U wordt nu doorverwezen naar de betaalpagina");
}
?>
<br><li>
    <a href="winkelmand.php" class="HrefDecoration"><- Terug naar winkelmand</a>
</li><br>
