<?php
include __DIR__ . "/header.php";

  //paid, canceled, expired, failed,
    if (isset($_GET["order"])){
        if($_GET["order"] == "paid"){
    ?>
        <br><h1>Betaling is succesvol!</h1><br>
<?php
}
    elseif ($_GET["order"] == "canceled"){
    ?>
        <br><h1>Betaling is geannuleerd.</h1><br>
<?php
}
    elseif ($_GET["order"] == "expired"){
    ?>
        <br><h1>Betaling is verlopen, probeer opnieuw.</h1><br>
<?php
}
    elseif ($_GET["order"] == "failed"){
    ?>
        <br><h1>Betaling is helaas niet gelukt, probeer opnieuw.</h1><br>
<?php

}
        else {header("location: winkelmand.php");}
        }
else {header("location: winkelmand.php");}
?>