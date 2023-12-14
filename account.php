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
            width: 30%;
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

        .order-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .order {
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .order:last-child {
            border-bottom: none;
        }

        .order h2 {
            margin-bottom: 10px;
            color: #333;
        }

        .order p {
            margin: 0;
            color: #666;
        }

        .delivery-date {
            font-weight: bold;
            color: #3498db;
        }

        .products {
            margin-top: 10px;
        }

        .price {
            margin-top: 10px;
            font-size: 1.2em;
            color: #27ae60;
        }
    </style>
</head>
<body>
<?php
    $orders = getOrderDetails($_SESSION['activeUser'][0]['Email'], $databaseConnection)
?>

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
<div class="order-container">
    <?php
    foreach ($orders as $order) {
       $orderLines =  getOrderLineDetails($order['OrderID'], $databaseConnection);
    echo '
    <div class="order">
        <h2>Order #12345</h2>
        <p class="delivery-date">Verwachte lever datum: ',$order['ExpectedDeliveryDate'],'</p>
        ';
        foreach ($orderLines as $orderLine) {
            echo '<div class="products">
            <p><strong>Products:</strong> ',$orderLine['Description'],'</p>
        </div>
        <p class="price">Total Price: $150.00</p>
    </div>';
        }
    }
    ?>
    <div class="order">
        <h2>Order #67890</h2>
        <p class="delivery-date">Expected Delivery Date: February 15, 2023</p>
        <div class="products">
            <p><strong>Products:</strong> Product X, Product Y</p>
        </div>
        <p class="price">Total Price: $75.50</p>
    </div>
</div>


</body>
</html>

