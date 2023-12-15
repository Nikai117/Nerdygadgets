<?php
include __DIR__ . "/header.php";

if($_SESSION['activeUser'] == NULL) {
    echo '<h1 style="text-align: center"><a href="login.php">Log in</a> om verlanglijstjes te gebruiken!</h1>
            <h2 style="text-align: center">Of <a href="registratie.php">registeer</a></h2>';
} else {
    $userID = $_SESSION['activeUser'][0]['userID'];
    if(!isset($_GET['list'])) {
        header("Location: lijst.php?list=Standaard");
        exit();
    } else {
        $list = $_GET['list'];
    }

    $lijstNamen = getWishlistNames($userID, $databaseConnection);
    $producten = getWishlistContent($userID, $_GET['list'], $databaseConnection);
    ?>
    <div id="wishlist">
        <div id="verlanglijst-namen">
            <ul><?php
                foreach($lijstNamen as $namen) {
                    if($namen["WishlistName"] == $list) {
                        $class = 'class="selected"';
                    } else {
                        $class = 'class="unselected"';
                    }

                    print('<li><a '.$class.' href="lijst.php?list='.$namen["WishlistName"].'">'.$namen["WishlistName"].'</a></li>');
                }
            ?></ul>
        </div>

        <form method="post" action="lijst.php?list=<?php echo $_GET['list'];?>">
        <div id="producten">
            <?php
            if(count($producten) > 0) {
            $x = 1;
            foreach($producten as $product) { echo '
                <div class="product">
                    <div class="product-info">
                        <h4>'.$product['StockItemID'].'</h4><br>
                        <h5>Lorem ipsum nogwattes</h5>
                    </div>
                    <div class="product-check">
                        <input type="checkbox" class="product-keuze" name="product'.$x.'" value="'.$product['StockItemID'].'">
                    </div>
                </div>
                ';$x++;}
            } else {
                echo '<h3>Dit lijstje is leeg. Meer <a href="browse.php">toevoegen</a>?';
            }
            ?>
        </div>    

        <div id="submit-knop">
            <input type="submit" value="Voeg toe aan winkelmand!">
        </div>
        </form>
    </div>

    <?php
    if($_POST != NULL) {
        foreach($_POST as $product) {
            $new = addToCart($product, $databaseConnection);

            $voorraad = getQuantity($product, $databaseConnection);
            $QOH = $voorraad[0]["QOH"];

            // $new is 2-dimensionaal, dus gebruik foreach voordat je een product toevoegt
            foreach($new as $row) {
                if(!isset($_SESSION['winkelmand'][$row["StockItemID"]])) {// als het item nog niet in de winkelmand zit
                    $_SESSION['winkelmand'][$row["StockItemID"]] = $row;
                    $_SESSION['winkelmand'][$row["StockItemID"]]['aantal'] = 1;
                } else {
                    if($_SESSION['winkelmand'][$row["StockItemID"]]['aantal'] < $QOH) {
                        $_SESSION['winkelmand'][$row["StockItemID"]]['aantal']++;       
                    } else {
                        print('<p class="bestelling"><b><i>Mislukt! Niet genoeg in voorraad.</i></b></p>');
                    }
                }
            }
        }
    }
}
?>

<style>
    #wishlist {
        width: 50%;
        height: 75%;
        margin: auto;
        top: 22%;
        left: 25%;
        background-color: black;
        color: white;
        border: 2px solid;
        box-shadow: 0 4px 8px 0 rgba(111, 65, 148, 2), 0 6px 20px 0 rgba(111, 65, 148, 1);

        position: fixed;
    }
    #verlanglijst-namen {
        width: 20%;
        height: 80%;
        overflow: auto;
        background-color: blue;
        float: left;
        border-right: 1px solid;
        position: absolute;
    }
    #verlanglijst-namen ul {
        list-style-type: none;
    }
    #verlanglijst-namen a:hover {
        cursor: pointer;
    }
    #producten {
        width: 79%;
        height: 80%;
        overflow: auto;
        background-color: red;
        left: 21%;
        position: absolute;
        border-left: 1px solid;
    }
    .product {
        border: 1px solid;
        padding-left: 2%;
        padding-bottom: 2%;
        position: relative;
        display: flex;
    }
    .product-info {
        width: 70%;
    }
    .product-check {
        padding-left: 15%;
        padding-top: 3%;
    }
    .product-keuze {
        width: 16px;
        margin: auto;
    }
    #submit-knop {
        position: absolute;
        background-color: white;
        top: 80%;
        height: 20%;
        width: 100%;
    }
    .selected {
        color: white;
        text-decoration: underline;
    }
    .selected:hover {
        color: lightgray;
    }
    .unselected {
        color: white;
    }
    .unselected:hover {
        color: lightgray;
        text-decoration: underline;
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
        margin-top: 5%;
    }

    input[type="submit"]:hover {
        background-color: darkblue;
    }
</style>