<!-- dit bestand bevat alle code voor de pagina die één product laat zien -->
<?php
include __DIR__ . "/header.php";

$StockItem = getStockItem($_GET['id'], $databaseConnection);
$StockItemImage = getStockItemImage($_GET['id'], $databaseConnection);

$voorraad = getQuantity($_GET['id'], $databaseConnection);
$QOH = $voorraad[0]["QOH"];//quantity on hand; voorraad
// $QPO = $voorraad[0]["QPO"];//quantity per outer; hoeveelheid per doos die je gaat shippen

$userID = 234;//nog aanpassen met Nikai
$lijstNamen = getWishlistNames($userID, $databaseConnection);
?>
<div id="alert-overlay">
    <div id="wishlist-alert">
        <div id="alert-header">
            <?php
            echo '<h5 style="float:left;padding-left:5px;">Voeg het product toe aan een verlanglijstje</h5>';
            ?>
            <button type="button" onclick="closeAlert()">X</button>
        </div>
        <div id="alert-body">
            <?php
            foreach($lijstNamen as $namen) {
                #stuur php variabele naar javascript mbv data-name
                print('
                    <div class="wishlist" data-name="'.$namen["WishlistName"].'">
                        '.$namen["WishlistName"].'      
                        <button class="add-to-wishlist" type="button" onclick="insertToWishlist(this)">+</button>
                    </div>
                ');
            }
            ?>
        </div>
        <div id="alert-footer">
            <button type="button" id="continue-button" onclick="closeAlert()">Verder met winkelen?</button>
            <button type="button" id="cart-button" onclick="openCart()">Winkelmand openen</button>
        </div>
    </div>
</div>

<div id="CenteredContent">
    <?php
    if ($StockItem != null) {
        ?>
        <?php
        if (isset($StockItem['Video'])) {
            ?>
            <div id="VideoFrame">
                <?php print $StockItem['Video']; ?>
            </div>
        <?php }
        ?>


        <div id="ArticleHeader">
            <?php
            if ($StockItemImage != NULL) {
                // één plaatje laten zien
                if (count($StockItemImage) == 1) {
                    ?>
                    <div id="ImageFrame"
                         style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                    <?php
                } else if (count($StockItemImage) >= 2) { ?>
                    <!-- meerdere plaatjes laten zien -->
                    <div id="ImageFrame">
                        <div id="ImageCarousel" class="carousel slide" data-interval="false">
                            <!-- Indicators -->
                            <ul class="carousel-indicators">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <li data-target="#ImageCarousel"
                                        data-slide-to="<?php print $i ?>" <?php print (($i == 0) ? 'class="active"' : ''); ?>></li>
                                    <?php
                                } ?>
                            </ul>

                            <!-- slideshow -->
                            <div class="carousel-inner">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <div class="carousel-item <?php print ($i == 0) ? 'active' : ''; ?>">
                                        <img src="Public/StockItemIMG/<?php print $StockItemImage[$i]['ImagePath'] ?>">
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- knoppen 'vorige' en 'volgende' -->
                            <a class="carousel-control-prev" href="#ImageCarousel" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#ImageCarousel" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div id="ImageFrame"
                     style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
                <?php
            }
            ?>


            <h1 class="StockItemID">Artikelnummer: <?php print $StockItem["StockItemID"]; ?></h1>
            <h2 class="StockItemNameViewSize StockItemName">
                <?php print $StockItem['StockItemName']; ?>
            </h2>
            <div class="QuantityText"><?php print $StockItem['QuantityOnHand']; ?></div>
            <div id="StockItemHeaderLeft">
                <div class="CenterPriceLeft">
                    <div class="CenterPriceLeftChild">
                        <p class="StockItemPriceText"><b><?php print sprintf("€ %.2f", $StockItem['SellPrice']); ?></b></p>
                        <h6> Inclusief BTW </h6>
                        <!-- code voor de knop om producten toe te voegen aan het winkelmandje-->
                        <?php 
                        //als er minder units in te voorraad zitten dan er nodig zijn om een doos te vullen
                        if($QOH < 1) {?>
                            <p class="notEnough">Niet genoeg units om te shippen!</p>
                        <?php } else {?>

                        <form method="post" action="view.php?id=<?php print $_GET['id']; ?>" id="cart-button">
                            <input type="submit" value="Bestel nu!" name="product">
                        </form>
                        <?php }
                        if(isset($_POST['product'])) {
                            $new = addToCart($_GET['id'], $databaseConnection);

                            // $new is 2-dimensionaal, dus gebruik foreach voordat je een product toevoegt
                            foreach($new as $row) {
                                if(!isset($_SESSION['winkelmand'][$row["StockItemID"]])) {// als het item nog niet in de winkelmand zit
                                    $_SESSION['winkelmand'][$row["StockItemID"]] = $row;
                                    $_SESSION['winkelmand'][$row["StockItemID"]]['aantal'] = 1;
                                    print('<p class="bestelling"><b><i>Product toegevoegd aan <a href="winkelmand.php">winkelmand</a>!</i></b></p>');
                                } else {
                                    if($_SESSION['winkelmand'][$row["StockItemID"]]['aantal'] < $QOH) {
                                        $_SESSION['winkelmand'][$row["StockItemID"]]['aantal']++;       
                                        print('<p class="bestelling"><b><i>Product toegevoegd aan <a href="winkelmand.php">winkelmand</a>!</i></b></p>');
                                    } else {
                                        print('<p class="bestelling"><b><i>Mislukt! Niet genoeg in voorraad.</i></b></p>');
                                    }
                                }
                            }
                        }
                        ?>
                        <!-- einde code -->

                        <!-- code voor verlanglijst -->
                        <button type="button" onclick="addToWishlist()" id="addToWishlist-button">
                            In verlanglijstje
                        </button>
                        <!-- einde code -->
                    </div>
                </div>
            </div>
        </div>

        <div id="StockItemDescription">
            <h3>Artikel beschrijving</h3>
            <p><?php print $StockItem['SearchDetails']; ?></p>
        </div>
        <div id="StockItemSpecifications">
            <h3>Artikel specificaties</h3>
            <?php
            $CustomFields = json_decode($StockItem['CustomFields'], true);
            if (is_array($CustomFields)) { ?>
                <table>
                <thead>
                <th>Naam</th>
                <th>Data</th>
                </thead>
                <?php
                foreach ($CustomFields as $SpecName => $SpecText) { ?>
                    <tr>
                        <td>
                            <?php print $SpecName; ?>
                        </td>
                        <td>
                            <?php
                            if (is_array($SpecText)) {
                                foreach ($SpecText as $SubText) {
                                    print $SubText . " ";
                                }
                            } else {
                                print $SpecText;
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </table><?php
            } else { ?>

                <p><?php print $StockItem['CustomFields']; ?>.</p>
                <?php
            }
            ?>
        </div>
        <?php
    } else {
        ?><h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2><?php
    } ?>
</div>

<!-- script met functies voor client-side operaties -->
<script>
    function addToWishlist() {
        document.getElementById("alert-overlay").style.display = "block";
    }
    function closeAlert() {
        document.getElementById("alert-overlay").style.display = "none";
    }
    function openCart() {
        window.location.href = 'winkelmand.php';
    }
</script>

<!-- script voor het sturen van php variabelen dmv ajax -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function insertToWishlist(button) {
    var wishlistName = button.parentElement.dataset.name; //data van de div
    var StockItemID = '<?php echo $_GET["id"]; ?>';
    
    $.ajax({
        url: 'lijst_insert.php',
        type: 'POST',
        data: { wishlistName: wishlistName, StockItemID: StockItemID},
        success: function(response) {
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}
</script>

<style>
    #addToWishlist-button {
        border: none;
        outline: none;
        margin-top: 5px;
    }
    #alert-overlay {
        position: fixed;
        /*display: none;*/
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 1999;
    }
    #wishlist-alert {
        /*display: none;*/
        position: absolute;
        width: 30%;
        height: 65%;
        margin: auto;
        left: 35%;
        top: 5%;
        background-color: black;
        color: white;
        border: 2px solid;
        box-shadow: 0 4px 8px 0 rgba(111, 65, 148, 2), 0 6px 20px 0 rgba(111, 65, 148, 1);
        z-index: 2000;
    }
    #alert-header {
        height: 15%;
        width: 100%;
        position: absolute;
        background-color: red;
        top: 0;
    }
    #alert-header button {
        border: none;
        outline: none;
        background-color: blue;
        color: #fff;
        float: right;
    }
    #alert-header button:hover {
        background-color: darkblue;
    }
    #alert-body {
        height: 70%;
        width: 100%;
        position: absolute;
        background-color: blue;
        top: 15%;
        overflow: auto;
    }
    #alert-body .wishlist {
        background-color: black;
        border: 1px solid;
        height: 20%;
    }
    #alert-body .add-to-wishlist {
        border: none;
        outline: none;
    }
    #alert-body .add-to-wishlist:hover {
        background-color: lightgray;
    }
    #alert-footer{
        height: 15%;
        width: 100%;
        position: absolute;
        top: 85%;
    }
    #alert-footer button {
        width: 20%;
        margin-left: 20%;
        margin-top: 2%;
        border: none;
        outline: none;
    }
    #alert-footer #continue-button {
        background-color: Blue;
        color: #fff;
    }
    #alert-footer #continue-button:hover {
        background-color: darkblue;
    }
    #alert-footer #cart-button:hover {
        background-color: lightgray;
    }
</style>