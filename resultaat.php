<?php
include __DIR__ . "/header.php";

  //paid, canceled, expired, failed,
  if (isset($_GET["order"])){
    if($_GET["order"] == "paid"){
        if(isset($_SESSION['paid'])) {
?>
    <br><h1>Betaling is succesvol!</h1><br>
    <?php 
        $deliveryDate = getDeliveryDate($_SESSION['klant']['email'], $databaseConnection);
        print('<br><h2>Verwachte levering: ' . $deliveryDate . '</h2><br>');

        print('<br><h2>Totaalprijs producten: €'. $_SESSION['producttotaal'] . ' | Uw bestelling:</h2><br>');
    ?>
    
<div id="lijst">
<table id="bestelling">
    <!-- maak tabelvakje aan per product -->
    <?php if(isset($_SESSION['winkelmand'])) {
     foreach ($_SESSION['winkelmand'] as $key => $product) {

        print('<div>
                    <h5>' . $product['StockItemName'] . ' | Artikelnummer: ' . $product['StockItemID'] .' | €' . number_format(round($product['SellPrice'], 2), 2, '.', '') . ' x '. $product['aantal'] . '</h5><br>
                </div>');
            
        print("<br></div></td></tr>");
    }

    $_SESSION['winkelmand'] = array();
    $_SESSION['klant'] = array();
    unset($_SESSION['paid']);
    print('<h2>Prijs + verzendkosten: €' . $_SESSION['totaalprijs']. '</h2><br>');
    print('<button type="button" onclick="redirectToHome()" id="href">Naar hoofdpagina</button>');
    } else {
        header("Location: index.php");
    } ?>
</table>
</div>

<?php
} else {
    print('<br><h1>U hoort hier niet te zijn.</h1><br>');
    print('<button type="button" onclick="redirectToHome()" id="href">Naar hoofdpagina</button>');
}

}
    elseif ($_GET["order"] == "canceled"){
    ?>
        <br><h1>Betaling is geannuleerd.</h1><br>
<?php
    print('<button type="button" onclick="redirectToHome()" id="href">Naar hoofdpagina</button>');
}
    elseif ($_GET["order"] == "expired"){
    ?>
        <br><h1>Betaling is verlopen, probeer opnieuw.</h1><br>
<?php
    print('<button type="button" onclick="redirectToPayment()" id="href">Naar betalen</button>');    
}
    elseif ($_GET["order"] == "failed"){
    ?>
        <br><h1>Betaling is helaas niet gelukt, probeer opnieuw.</h1><br>
<?php
    print('<button type="button" onclick="redirectToPayment()" id="href">Naar betalen</button>'); 
}
        else {header("location: winkelmand.php");}
        }
else {header("location: winkelmand.php");}
?>
<br>
<script>
    function redirectToHome() {
        // Hier kun je eventueel wat JavaScript-code toevoegen voordat je doorverwijst
        window.location.href = 'index.php';
    }
    function redirectToPayment() {
        window.location.href = 'Betaalgegevens.php';
    }
</script>

<style>
#href {
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
    }

#href:hover {
    background-color: darkblue;
}
</style>