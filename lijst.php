<?php
include __DIR__ . "/header.php";

//klant moet ingelogd zijn
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
        $listExists = false;

        $lijstNamen = getWishlistNames($userID, $databaseConnection);

        //kijken of de waarde van list bestaat
        foreach($lijstNamen as $naam) {
            if($naam["WishlistName"] == $list) {
                $listExists = true;
            }
        }
        
        if(!$listExists) {
            header("Location: lijst.php?list=Standaard");
            exit();
        }
    }

    $producten = getWishlistContent($userID, $_GET['list'], $databaseConnection);
    ?>

    <!-- overlay div voor nieuwe lijstnaam formulier -->
    <div id="name-form-overlay">
        <div id="name-form-alert">
            <div id="alert-header">
                <h5>Maak een nieuwe lijstje aan</h5>
            </div>
            
            <div id="alert-body">
                <form action="" id="name-form">
                    <label for="list-name">Naam:</label>
                    <input type="text" id="list-name" placeholder="Voer een naam in..." required>
                    <input type="submit" id="submit-button" value="Verstuur naam">      
                    <input type="submit" id="cancel-button" value="Annuleer" onclick="closeNameForm()">
                </form>     
            </div>
        </div>
    </div>


    <div id="wishlist">
        <div id="verlanglijst-namen">
            <ul>
                <?php
                foreach($lijstNamen as $namen) {
                    if($namen["WishlistName"] == $list) {
                        $class = 'class="selected"';
                    } else {
                        $class = 'class="unselected"';
                    }

                    print('<li><a '.$class.' href="lijst.php?list='.$namen["WishlistName"].'">'.$namen["WishlistName"].'</a></li>');
                }
                ?>
                <br><li><a class="unselected" onclick="openNameForm()">(Maak nieuwe lijst)</a></li>
            </ul>
        </div>

        <form method="post" action="lijst.php?list=<?php echo $_GET['list'];?>" id="product-checkboxes">
            <div id="producten">
                <div id="producten-header">
                <?php
                if($_GET['list'] != "Standaard") {
                    echo '<button type="button" onclick="deleteWishlist()" id="delete-list">Verwijder lijst</button>';
                }?>
                <?php
                if(count($producten) < 1) {
                    echo '<h3 style="padding-top:5px;">Dit lijstje is leeg. Meer <a href="browse.php">toevoegen</a>?';
                } else {
                    echo '<button type="button" onclick="checkAll(event)" id="select-all">Selecteer allemaal</button>
                </div>';
                $x = 1;//hulp variabele zodat elke name uniek kan zijn
                foreach($producten as $product) { 
                    $StockItem = getStockItem($product['StockItemID'], $databaseConnection);
                    $StockItemImage = getStockItemImage($product['StockItemID'], $databaseConnection);
                    $BackupImage = getBackupImage($product['StockItemID'], $databaseConnection);

                    if ($StockItemImage != NULL) {
                        $image = '<img src="Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'] . '" class="itemimage"></a>';
                    } else { 
                        $image = '<img src="Public/StockGroupIMG/' . $BackupImage['ImagePath'] . '" class="itemimage"></a>';
                    }
                            
                    echo '
                    <div class="product">
                        <div class="product-info" data-name="'.$product['StockItemID'].'">
                            <h4>'.$image.'</h4><br>
                            <h5 style="float:left"><a href="view.php?id='.$product['StockItemID'].'">'.$StockItem['StockItemName'].'</a> |<h6 style="float:left;margin-left:5px;padding-top:5px;"onclick="removeFromWishlist(this)" class="remove-product"> Verwijder product</h6></h5>
                        </div>
                        <div class="product-check">
                            <input type="checkbox" class="product-keuze" name="product'.$x.'" value="'.$product['StockItemID'].'">
                        </div>
                    </div>
                    ';$x++;}
                ?>
            </div>    

            <div id="submit-knop">
                <input type="submit" value="Voeg toe aan winkelmand!">
            </div>
            <?php }?>
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

<!-- script voor client-side operaties -->
<script>
    function openNameForm() {
        document.getElementById("name-form-overlay").style.display = "block";
    }
    function closeNameForm() {
        window.location.href = window.location.href;
    }
    function checkAll(event) {
        event.preventDefault();//als de boxes gecheckt werden, dan werden deze automatisch gesubmit
        
        var form = document.getElementById("product-checkboxes");
        var checkboxes = form.querySelectorAll('input[type="checkbox"]');

        checkboxes.forEach(function(checkbox) {
            checkbox.checked = true;
        });
    }
</script>
<!-- script voor sturen van data naar de database -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script voor verlanglijst aanmaken in de database -->
<script>
    var nameForm = document.getElementById("name-form");

    nameForm.addEventListener("submit", (e) => {
        e.preventDefault();

        var listName = document.getElementById("list-name");
        var listNameValue = listName.value;
        var operation = "maken_lijst";

        //ajax request om de variabele(n) van hierboven te sturen naar lijst_operaties.php
        $.ajax({
            url: 'lijst_operaties.php',
            type: 'POST',
            data: { newListName: listNameValue, operation: operation},
            success: function(response) {

                try {
                    const responseObj = JSON.parse(response);

                    if (responseObj.success === true) {                                     
                        closeNameForm();
                    } else {
                        //vermeldt dat het aanmaken niet geslaagd is
                        const h5Element = document.querySelector('#alert-header h5');
                        h5Element.textContent = responseObj.message;
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });             

</script>
<!-- script voor verlanglijst verwijderen uit de database -->
<script>
    function deleteWishlist() {
        try {
            var wishlistName = '<?php echo $_GET['list'];?>';
            var operation = "verwijderen_lijst";

            $.ajax({
                url: 'lijst_operaties.php',
                type: 'POST',
                data: { wishlistName: wishlistName, operation: operation},
                success: function(response) {
                    console.log(response);
                    window.location.href = window.location.href;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } catch (error) {
            console.error(error);
        }
    }
</script>
<!-- script voor product verwijderen uit verlanglijst -->
<script>
    function removeFromWishlist(obj) {
        try {
            var StockItemID = obj.closest('.product-info').dataset.name;
            var wishlistName = '<?php echo $_GET['list'];?>';
            var operation = "verwijderen_product";

            $.ajax({
                url: 'lijst_operaties.php',
                type: 'POST',
                data: { wishlistName: wishlistName, StockItemID: StockItemID, operation: operation},
                success: function(response) {
                    console.log(response);
                    window.location.href = window.location.href;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } catch (error) {
            console.error(error);
        }
    }
</script>

<!-- stijl voor de overlay div van de naam formulier -->
<style>
    #name-form-overlay {
        position: fixed;
        display: none;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 1999;
    }
    #name-form-alert {
        position: absolute;
        width: 30%;
        height: 30%;
        margin: auto;
        left: 35%;
        top: 15%;
        background-color: rgb(35, 35, 47, 0.97);
        color: white;
        border: 4px solid darkblue;
        box-shadow: 0 4px 8px 0 rgba(111, 65, 148, 2), 0 6px 20px 0 rgba(111, 65, 148, 1);
        z-index: 2000;
    }
    #alert-header {
        height: 30%;
        width: 100%;
        position: absolute;
        top: 0;
        text-align: center;
        padding-top: 5%;
    }
    #alert-body {
        height: 70%;
        width: 100%;
        position: absolute;
        top: 30%;
        overflow: auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #alert-body {
        border-top: 4px solid darkblue;
        padding: 5px;
        width: 90%;
        margin-left: 5%;
    }
    #alert-body #submit-button, #alert-body #submit-button {
        width: 30%;
        border: none;
        outline: none;
    }
    #alert-body #submit-button {
        background-color: Blue;
        color: #fff;
        float: left;
        margin-top: 5%;
    }
    #alert-body #submit-button:hover {
        filter: brightness(85%);
    }
    #alert-body #cancel-button {
        background-color: gray;
        float: right;
        margin-top: 5%;
    }
    #alert-body #cancel-button:hover {
        filter: brightness(80%);
    }
    #list-name {
        width: 80%;
        
    }
</style>

<!-- stijl voor de verlanglijstjes-->
<style>
    .itemimage {
        width: 80px;
        height: 80px;
        margin-top: 3%;
        margin-bottom: -3%;
    }
    #wishlist {
        width: 50%;
        height: 75%;
        margin: auto;
        top: 22%;
        left: 25%;
        background-color: rgb(35, 35, 47, 0.97);
        color: white;
        border: 4px solid darkblue;
        box-shadow: 0 4px 8px 0 rgba(111, 65, 148, 2), 0 6px 20px 0 rgba(111, 65, 148, 1);
        position: fixed;
    }
    #verlanglijst-namen {
        width: 20%;
        height: 75%;
        overflow: auto;
        border-right: 4px solid darkblue;
        position: absolute;
        top: 2%;
        padding-top: 10px;
    }
    #verlanglijst-namen ul {
        list-style-type: none;
        margin-left: -5%;
    }
    #verlanglijst-namen a:hover {
        cursor: pointer;
        color: lightgray;
    }
    #producten-header {
        height: 10%;
        padding-top: 2%;
        padding-left: 2%;
    }
    #producten-header button {
        border: none;
        outline: none;
        color: #fff;
    }
    #producten-header #delete-list {
        background-color: darkred;
    } 
    #producten-header #select-all {
        background-color: green;
        float: right;
        margin-right: 5%;
    }
    #producten-header button:hover {
        filter: brightness(80%);
    }
    #producten {
        width: 79%;
        height: 80%;
        overflow: auto;
        left: 21%;
        position: absolute;
    }
    .remove-product {
        color: magenta;
    }
    .remove-product:hover {
        cursor: pointer;
        filter: brightness(80%);
    }
    .product {
        border-top: 4px solid darkblue;
        padding-left: 2%;
        padding-bottom: 2%;
        position: relative;
        display: flex;
        width: 95%;
        margin-left: 2%;
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
        border-top: 4px solid darkblue;
        top: 85%;
        height: 15%;
        width: 90%;
        left: 5%;
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
        filter: brightness(80%);
    }
    input[type="submit"] {
        display: block;
        margin: auto;
        width: 30%;
        background-color: Blue;
        color: #fff;
        border: none;
        cursor: pointer;
        margin-top: 3%;
    }

    input[type="submit"]:hover {
        background-color: darkblue;
    }
</style>