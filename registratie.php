<?php
ob_start();
include __DIR__ . "/header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
</head>
<body>

<form method="post">
    <h1 style="color: black">Registreren</h1><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Wachtwoord:</label>
    <input type="password" id="password" name="password" required>

    <label for="name">Voornaam:</label>
    <input type="text" id="name" name="name" required>

    <label for="surname">Achternaam:</label>
    <input type="text" id="surname" name="surname" required>

    <label for="company">Bedrijf (optioneel):</label>
    <input type="text" id="company" name="company">

    <label for="phone">Telefoonnummer:</label>
    <input type="tel" id="phone" name="phone" required>

    <div id="gebruikersAdres">
        <label for="address">Straatnaam:</label>
        <input type="text" id="address" name="address" required>

        <label for="toevoeging">Huisnummer:</label>
        <input type="number" id="toevoeging" name="toevoeging" required>

        <label for="postalcode">Postcode:</label>
        <input type="text" id="postalcode" name="postalcode" required>
    </div>

    <input type="submit" name="submitButton" id="submitButton">
</form>

</body>
<?php

if (isset($_POST['submitButton'])) {
    if (!ctype_alpha(str_replace(' ', '', $_POST['address']))) {
        generateErrorMessage("Vul een geldig adres in");
    } elseif (!ctype_digit($_POST['phone'])) {
        generateErrorMessage("Vul een geldig telefoonnummer in");
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        generateErrorMessage("Vul een geldig email in");
    } elseif (!preg_match('/^[1-9][0-9]{3}[A-Z]{2}$/', $_POST['postalcode'])) {
        generateErrorMessage("Vul een geldig postcode in (Zoals 1234AA).");
    } else {
        if (isNameUnique(ucfirst(strtolower($_POST['name'])) . " " . ucfirst(strtolower($_POST['surname'])), $databaseConnection)) {
            if (getCustomerByEmail($_POST['email'], $databaseConnection) == array()) {
                $_SESSION['account']['email'] = $_POST['email'];
                $_SESSION['account']['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $_SESSION['account']['name'] = ucfirst(strtolower($_POST['name'])) . " " . ucfirst(strtolower($_POST['surname']));
                $_SESSION['account']['company'] = $_POST['company'];
                $_SESSION['account']['phone'] = $_POST['phone'];
                $_SESSION['account']['address'] = $_POST['address'] . " " . $_POST['toevoeging'];
                $_SESSION['account']['toevoeging'] = $_POST['toevoeging'];
                $_SESSION['account']['postalcode'] = $_POST['postalcode'];
                registerCustomer($_SESSION['account'], $databaseConnection);
                generateSuccesMessage("Account is aangemaakt", 'login.php');
            } else {
                generateErrorMessage("Email is al in gebruik");
            }
        } else {
            generateErrorMessage("Account met deze naam bestaat al");
        }
    }
}
ob_end_flush()
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 80%;
        margin-left: 10%;
        margin-top: 2%;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: black;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-bottom: 12px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    #submitButton {
        background-color: #212529;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    #submitButton:hover {
        background-color: #343a40;
    }

    #gebruikersAdres {
        margin-top: 20px;
    }
</style>
</html>
