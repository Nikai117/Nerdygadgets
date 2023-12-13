<?php
include __DIR__ . "/header.php";
?>
<div id="wishlist">
    <div id="verlanglijst-namen">
        <ul>
            <li><a class="selected" href="lijst.php">Mijn verlanglijstje</a></li>
            <li><a class="unselected" href="lijst.php?list=test">Test</a></li>
        </ul>
    </div>

    <form method="post">
    <div id="producten">
        <?php
        for($x = 0; $x < 3; $x++) {?>
            <div class="product">
                <div class="product-info">
                    <h4>Product</h4><br>
                    <h5>Lorem ipsum nogwattes</h5>
                </div>
                <div class="product-check">
                    <input type="checkbox" class="product-keuze" value="PLACEHOLDER">
                </div>
            </div>
        <?php }
        ?>
    </div>    

    <div id="submit-knop">
        <input type="submit" value="Voeg toe aan winkelmand!">
    </div>
    </form>
</div>

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