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

<!-- script voor client-side operaties -->
<script>
    function openNameForm() {
        document.getElementById("name-form-overlay").style.display = "block";
    }
    function closeNameForm() {
        window.location.href = window.location.href;
    }
</script>

<!-- script voor sturen van data naar de database -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var nameForm = document.getElementById("name-form");

    nameForm.addEventListener("submit", (e) => {
        e.preventDefault();

        var listName = document.getElementById("list-name");
        var listNameValue = listName.value;

        $.ajax({
            url: 'lijst_maken.php',
            type: 'POST',
            data: { newListName: listNameValue},
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
    #alert-body button, #alert-body #submit-button {
        width: 30%;
        margin-top: 2%;
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
        filter: brightness(80%);
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