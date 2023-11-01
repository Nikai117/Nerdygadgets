<?php
//onze session array heet "winkelmand". dus je roept het bijvoorbeeld zo een prijs op: $_SESSION['winkelmand'][0]['UnitPrice']
include __DIR__ . "/header.php";       

function checkEmpty($arr) {
    return !isset($arr[0]); //eerste element is niet null 
}

if(checkEmpty($_SESSION['winkelmand'])) {
    print("Yarr, de winkelmand is leeg");
} else {
    if(isset($_POST['action'])) {
        $action = explode(" ", $_POST['action']);

        if($action[0] == "remove") {//action is verwijderen
            unset($_SESSION['winkelmand'][$action[1]]);//$action[1] is de index van het product in de session array
            array_splice($_SESSION['winkelmand'], $action[1], 0);//om de NULL row te verwijderen die je krijgt van unset()
            
            
            if(checkEmpty($_SESSION['winkelmand']))
                print("Yarr, de winkelmand is leeg");//beetje voor de UX        
        }

        elseif($action[0] == "add") {//action is toevoegen
            $_SESSION['winkelmand'][] = $_SESSION['winkelmand'][$action[1]];
        }
    }

    foreach($_SESSION['winkelmand'] as $key => $product) {
        print ('
        <div>
            <h5>'.$product['StockItemName'].'</h5>
            <p>$'.$product['UnitPrice'].'</p>
        </div>
       ');
        $StockItemImage = getStockItemImage($product['StockItemID'], $databaseConnection);
        if ($StockItemImage != null) {
            print ('<img src="Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'] . '" style="height: 120px;">');
        }
        print('
        <form method="post" action="winkelmand.php">
            <button type="submit" name="action" value="remove '. $key.'" style="background-color: #856404">-</button>
            <button type="submit" name="action" value="add '. $key.'" style="background-color: green; ">+</button>
        </form>
        ');//ik gebruik remove in value, zodat we wellicht later nog een andere functionaliteit kunnen toevoegen: toevoegen via de winkelmand - James
        print("<br>");
    }

    //checkt het totaalbedrag
    $totaal = 0;
    foreach ($_SESSION['winkelmand'] as $product['aantal']){
        $totaal += $product['UnitPrice'] * $aantal;
    }
    if (!checkEmpty($_SESSION['winkelmand'])) {
        print ("Totaal bedrag: $" . $totaal);
    }
    #hieronder zijn gewoon printjes voor testen
    //print_r($_SESSION['winkelmand'][0][0]);


    #einde
}
?>
