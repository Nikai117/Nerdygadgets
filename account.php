<?php
include __DIR__ . "/header.php";
$activeUser = $_SESSION['activeUser'][0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .account-container {
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .card h2 {
            color: #333;
        }

        .card p {
            color: #666;
        }
    </style>
</head>
<body>

<div class="account-container">
    <div class="card">
        <h2>Klantgegevens</h2>
        <p>Naam: <?php print ($activeUser['CustomerName'])?></p>
        <p>Email: <?php print ($activeUser['Email'])?></p>
    </div>

    <div class="card">
        <h2>Bestelgegevens</h2>
        <p>Adres: <?php print ($activeUser['DeliveryAddressLine1'])?></p>
        <p>Postcode: <?php print ($activeUser['DeliveryPostalCode'])?></p>
    </div>
</div>

</body>
</html>

