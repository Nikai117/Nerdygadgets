<!-- dit bestand bevat alle code die verbinding maakt met de database -->
<?php

function connectToDatabase()
{
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
        $Connection = mysqli_connect("localhost", "root", "", "nerdygadgets");
        mysqli_set_charset($Connection, 'latin1');
        $DatabaseAvailable = true;
    } catch (mysqli_sql_exception $e) {
        $DatabaseAvailable = false;
    }
    if (!$DatabaseAvailable) {
        ?><h2>Website wordt op dit moment onderhouden.</h2><?php
        die();
    }

    return $Connection;
}

function getHeaderStockGroups($databaseConnection)
{
    $Query = "
                SELECT StockGroupID, StockGroupName, ImagePath
                FROM stockgroups 
                WHERE StockGroupID IN (
                                        SELECT StockGroupID 
                                        FROM stockitemstockgroups
                                        ) AND ImagePath IS NOT NULL
                ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $HeaderStockGroups = mysqli_stmt_get_result($Statement);
    return $HeaderStockGroups;
}

function getStockGroups($databaseConnection)
{
    $Query = "
            SELECT StockGroupID, StockGroupName, ImagePath
            FROM stockgroups 
            WHERE StockGroupID IN (
                                    SELECT StockGroupID 
                                    FROM stockitemstockgroups
                                    ) AND ImagePath IS NOT NULL
            ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $StockGroups = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    return $StockGroups;
}

function getStockItem($id, $databaseConnection)
{
    $Result = null;

    $Query = " 
           SELECT SI.StockItemID, 
            (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, 
            StockItemName,
            CONCAT('Voorraad: ',QuantityOnHand)AS QuantityOnHand,
            SearchDetails, 
            (CASE WHEN (RecommendedRetailPrice*(1+(TaxRate/100))) > 50 THEN 0 ELSE 6.95 END) AS SendCosts, MarketingComments, CustomFields, SI.Video,
            (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath   
            FROM stockitems SI 
            JOIN stockitemholdings SIH USING(stockitemid)
            JOIN stockitemstockgroups ON SI.StockItemID = stockitemstockgroups.StockItemID
            JOIN stockgroups USING(StockGroupID)
            WHERE SI.stockitemid = ?
            GROUP BY StockItemID";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    if ($ReturnableResult && mysqli_num_rows($ReturnableResult) == 1) {
        $Result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC)[0];
    }

    return $Result;
}

function getStockItemImage($id, $databaseConnection)
{

    $Query = "
                SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

//zelf gemaakt
function getBackupImage($id, $databaseConnection)
{

    $Query = "
                SELECT SG.ImagePath
                FROM stockgroups SG
                JOIN stockitemstockgroups SI USING (StockGroupID)
                WHERE SI.StockItemID = ?
                LIMIT 1";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC)[0];

    return $R;
}

//zelfgemaakt
function getQuantity($id, $databaseConnection)
{

    $Query = "
    SELECT QuantityOnHand AS QOH, QuantityPerOuter AS QPO
    FROM stockitemholdings
    INNER JOIN stockitems USING (StockItemID)
    WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $result = mysqli_stmt_get_result($Statement);
    $Quantity = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $Quantity;
}

//zelfgemaakt
function addToCart($id, $databaseConnection)
{

    $Query = "
        SELECT StockItemID, StockItemName, (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, TaxRate, UnitPrice
        FROM stockitems
        WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $result = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $R;
}

function updateStocks($id, $amount, $databaseConnection)
{

    $Query = "
        UPDATE stockitemholdings
        SET LastStocktakeQuantity = QuantityOnHand,
            QuantityOnHand = QuantityOnHand - ?
        WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "ii", $amount, $id);
    mysqli_stmt_execute($Statement);
}

function getAccountById($id, $databaseConnection) {
    $Query = "
        SELECT *
        FROM accounts
        WHERE userID = ?
    ";

    $statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($statement, "i", $id);
    mysqli_stmt_execute($statement);
    $R = mysqli_stmt_get_result($statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function registerCustomer($accountArray, $databaseConnection)
{
    $customer = getCustomerByEmail($accountArray['email'], $databaseConnection);



    if ($customer == NULL) {
        $Query1 = "INSERT INTO accounts VALUES (NULL, ?, ?)";

        $Statement1 = mysqli_prepare($databaseConnection, $Query1);
        mysqli_stmt_bind_param($Statement1, "ss", $accountArray['email'], $accountArray['password']);
        mysqli_stmt_execute($Statement1);
        
        $last_id = mysqli_insert_id($databaseConnection);

        $Query2 = "
        INSERT INTO customers
        VALUES ( NULL,
            ?,
            1,
            9,
            null,
            1,
            1,
            1,
            1,
            1,
            0.00,
            '2023-01-01',
            0.00,
            0,
            0,
            30,
            ?,
            '(201) 555-0101',
            1,
            1,
            'http://www.example.com',
            ?,
            null,
            ?,
            'TestDeliveryLocation',
            'TestPostalAddressLine1',
            'TestPostalAddressLine2',
            'TestPostalPostalCode',
            1,
            '2023-01-01 00:00:00',
            '9999-12-31 23:59:59',
            ?,
            ?
        );
    ";

        $Statement2 = mysqli_prepare($databaseConnection, $Query2);
        mysqli_stmt_bind_param($Statement2, "sssssi", $accountArray['name'], $accountArray['phone'], $accountArray['address'], $accountArray['postalcode'], $accountArray['email'], $last_id);
        mysqli_stmt_execute($Statement2);
    }

    else if ($customer['accountID'] == NULL) {
        $Query = "
        INSERT INTO accounts
        VALUES(
               NULL,
               ?,
               ?
        );";
            $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_bind_param($Statement, "ss", $accountArray['email'], $accountArray['password']);
        mysqli_stmt_execute($Statement);
    } else {
        print("De ingevoerde email is al in gebruik");
    }
}

//klant toevoegen
function addCustomer($klantArray, $databaseConnection)
{
    $klant = getCustomerByEmail($klantArray['email'], $databaseConnection);

    if ($klant == NULL) {
        $Query = "
        INSERT INTO customers
        VALUES ( NULL,
            ?,
            1,
            9,
            null,
            1,
            1,
            1,
            1,
            1,
            0.00,
            '2023-01-01',
            0.00,
            0,
            0,
            30,
            ?,
            '(201) 555-0101',
            1,
            1,
            'http://www.example.com',
            ?,
            null,
            ?,
            'TestDeliveryLocation',
            'TestPostalAddressLine1',
            'TestPostalAddressLine2',
            'TestPostalPostalCode',
            1,
            '2023-01-01 00:00:00',
            '9999-12-31 23:59:59',
            ?
        );";

        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_bind_param($Statement, "sssss", $klantArray['naam'], $klantArray['telnummer'], $klantArray['adres'], $klantArray['postcode'], $klantArray['email']);
        mysqli_stmt_execute($Statement);

        return "";
    } else {
        return $klant;
    }
}

function checkLogin($email, $password, $databaseConnection) {

    $Query = "
        SELECT *
        FROM accounts
        WHERE email = ? AND password = ?;
    ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "ss", $email, $password);
    mysqli_stmt_execute($Statement);
    $result = mysqli_stmt_get_result($Statement);
    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $result;
}

function getCustomerByAccountID($id, $databaseConnection) {

    $Query = "
    SELECT *
    FROM customers
    WHERE userID = ?
    LIMIT 1;
    ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $result = mysqli_stmt_get_result($Statement);
    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $result;
}

function getCustomerID($email, $databaseConnection)
{

    $Query = "
        SELECT CustomerID
        From customers
        WHERE email = ?
        LIMIT 1;";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "s", $email);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC)[0]["CustomerID"];

    return $R;
}

function getAccountByEmail($email, $databaseConnection) {
    $Query = "
        SELECT *
        FROM accounts
        WHERE email = ?;
    ";

    $statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($statement, "s", $email);
    mysqli_stmt_execute($statement);
    $R = mysqli_stmt_get_result($statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function getCustomerByEmail($email, $databaseConnection)
{

    $Query = "
    SELECT email 
    FROM customers
    WHERE email = ?;
    ";

    $statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($statement, "s", $email);
    mysqli_stmt_execute($statement);
    $R = mysqli_stmt_get_result($statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function addOrder($email, $databaseConnection)
{
    $customerID = getCustomerID($email, $databaseConnection);

    $vandaag = date("Y-m-d");
    $morgen = date('Y-m-d', strtotime($vandaag . ' + 1 days'));
    $nu = date("Y-m-d H:i:s");

    $Query = "
        INSERT INTO orders 
        VALUES (NULL, ?, '2', NULL, '3032', NULL, ?, ?, NULL, '1', NULL, NULL, NULL, NULL, '1', ?);";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "isss", $customerID, $vandaag, $morgen, $nu);
    mysqli_stmt_execute($Statement);
}

function getOrderId($email, $databaseConnection)
{
    $customerID = getCustomerID($email, $databaseConnection);

    $Query = " 
       SELECT OrderID
       From orders
       WHERE CustomerID = ?
       ORDER BY OrderID DESC
       LIMIT 1";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $customerID);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC)[0]["OrderID"];

    return $R;

}

function getOrderDetails($email, $databaseConnection) {
    $customerID = getCustomerID($email, $databaseConnection);

    $Query = " 
       SELECT *
       From orders
       WHERE CustomerID = ?
       ORDER BY OrderID DESC";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $customerID);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function getOrderLineDetails($orderID, $databaseConnection) {

    $Query = " 
       SELECT *
       From orderlines
       WHERE orderID = ?
       ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $orderID);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function addOrderLine($email, $databaseConnection, $product)
{
    $orderID = getOrderId($email, $databaseConnection);
    $stockItemID = $product['StockItemID'];
    $stockItemDesc = $product['StockItemName'];//description voert die verkeerd in; nog naar kijken
    $quantity = $product['aantal'];
    $price = $product['UnitPrice'];
    $taxrate = $product['TaxRate'];
    $nu = date("Y-m-d H:i:s");

    $Query = "
        INSERT INTO orderlines
        VALUES (NULL,
            $orderID,
            $stockItemID,
            ?,
            7,
            $quantity,
            $price,
            $taxrate,
            0,
            NULL,
            1,
            ?
        );";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "ss", $stockItemDesc, $nu);
    mysqli_stmt_execute($Statement);
}

function getDeliveryDate($email, $databaseConnection)
{
    $orderID = getOrderId($email, $databaseConnection);

    $Query = "
            SELECT ExpectedDeliveryDate
            FROM orders
            WHERE OrderID = ?;";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $orderID);
    mysqli_stmt_execute($Statement);

    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC)[0]["ExpectedDeliveryDate"];

    return $R;
}

function getWishlistNames($userID, $databaseConnection) {
    
    $Query = "
            SELECT WishlistName   
            FROM wishlist
            WHERE userID = ?
            ORDER BY WishlistID;";

        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_bind_param($Statement, "i", $userID);
        mysqli_stmt_execute($Statement);
    
        $R = mysqli_stmt_get_result($Statement);
        $R = mysqli_fetch_all($R, MYSQLI_ASSOC);
    
        return $R;
}

function getWishlistID($lijstNaam, $databaseConnection) {

    $Query = "
            SELECT WishlistID   
            FROM wishlist
            WHERE WishlistName = ?";

        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_bind_param($Statement, "s", $lijstNaam);
        mysqli_stmt_execute($Statement);

        $R = mysqli_stmt_get_result($Statement);
        $R = mysqli_fetch_all($R, MYSQLI_ASSOC)[0]["WishlistID"];

        return $R;
}

function getWishlistContent($userID, $lijstNaam, $databaseConnection) {
    
    $Query = "
            SELECT StockItemID
            FROM wishlist_content
            WHERE userID = ?
            AND WishlistID IN
                (SELECT WishlistID
                FROM wishlist
                WHERE userID = ?
                AND WishlistName = ?);";

        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_bind_param($Statement, "iis", $userID, $userID, $lijstNaam);
        mysqli_stmt_execute($Statement);
    
        $R = mysqli_stmt_get_result($Statement);
        $R = mysqli_fetch_all($R, MYSQLI_ASSOC);
    
        return $R;
}

function insertToWishlist($userID, $lijstNaam, $itemID, $databaseConnection) {
    try {
    $lijstID = getWishlistID($lijstNaam, $databaseConnection);
    if($lijstID !== null ) {
            $Query = "
            INSERT INTO wishlist_content
            VALUES (?, ?, ?)";

        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_bind_param($Statement, "iii", $userID, $lijstID, $itemID);
        mysqli_stmt_execute($Statement);

        return true;
    } else {
        return false;
    }
    } catch (Exception $e) {
        //returns false als inserten niet gelukt is
        return false;
    }
}