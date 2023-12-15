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
            background-color: #1e1e1e; /* Dark background color */
            color: #fff; /* Light text color */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
        }

        .account-container,
        .order-container {
            background-color: #292929; /* Darker gray background */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
            margin: 20px;
            padding: 20px;
            width: 30%;
        }

        .card {
            text-align: left;
            margin-bottom: 20px;
            background-color: #292929;
        }

        .card h2 {
            color: #4fa3d1; /* Light blue heading color */
            border-bottom: 2px solid #4fa3d1; /* Light blue underline */
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .card p {
            color: #ccc; /* Light gray text color */
            margin: 10px 0;
        }

        .order-container {
            max-width: 600px;
            margin: 50px 20px;
        }

        .order {
            background-color: #333; /* Dark background for orders */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
            padding: 20px;
            margin-bottom: 20px;
        }

        .order h2 {
            color: #ddd; /* Light heading color */
            margin-bottom: 10px;
        }

        .delivery-date {
            font-weight: bold;
            color: #6e64b4; /* Light green delivery date */
        }

        .products p {
            margin: 8px 0;
        }

        .price {
            font-size: 1.2em;
            color: #6dbd63; /* Light green price color */
        }

    </style>
</head>
<body>
<?php
    $orders = getOrderDetails($_SESSION['activeUser'][0]['Email'], $databaseConnection)
?>
<div class="container">
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
    <?php
    if ($orders == array()) {
        print ("");
    } else {
        foreach ($orders as $order) {
            $orderLines = getOrderLineDetails($order['OrderID'], $databaseConnection);
            echo '
<div class="order-container">
    <div class="order">
        <h2>Order ', $order['OrderID'], '</h2>
        <p class="delivery-date">Verwachte lever datum: ', $order['ExpectedDeliveryDate'], '</p>
        <div class="products">
        ';
            foreach ($orderLines as $orderLine) {
                echo '
            <p> ', $orderLine['Description'], '</p>';
            }
            echo '
        </div>
        </div>
        ';
        }
    }
    ?>
</div>
</div>


</body>
</html>

