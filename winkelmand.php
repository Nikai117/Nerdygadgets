<?php
//onze session array heet "winkelmand". dus je roept het bijvoorbeeld zo een prijs op: $_SESSION['winkelmand'][0]['UnitPrice']
include __DIR__ . "/header.php";       

function checkEmpty($arr) {
    return !isset($arr[0]); //eerste element is niet null 
}

function removeRow($arr, $index) {
    unset($arr[$index]);//de index van het product in de session array
    array_splice($arr, $index, 0);//om de NULL row te verwijderen die je krijgt van unset()

    return $arr;
}

function findInArray($needle, $stack) {
    foreach($stack as $item) {
        if($item['StockItemID'] == $needle) {//needle is de ID dat je wilt vinden
            return true;
        }
    }

    return false;
}

function findIndexArray($needle, $stack) {
    foreach($stack as $key => $item) {
        if($item['StockItemID'] == $needle) {
            return $key;//het staat al vast dat de needle in de stack zit, we hebben alleen een preciese plek (index) nodig
        }
    }
}

function groupArray($sess_arr) {
    $new_arr = array();//nieuwe array initialiseren

    foreach($sess_arr as $item) {
        if($item['aantal'] == 1) {//de item is nog niet gegroupd/kan niet groupen
            if(findInArray($item['StockItemID'], $new_arr)) {
                $index = findIndexArray($item['StockItemID'], $new_arr);
                $new_arr[$index]['aantal']++;//increment het aantal van het gezochte product   
            } elseif(!findInArray($item['StockItemID'], $new_arr)) {//het zit nog niet in de array
                $new_arr[] = $item;//het product zit er niet dubbel in, dus voeg je deze simpelweg toe
            }
        } else {
            $new_arr[] = $item;
        }
    }

    return $new_arr;
}

if(checkEmpty($_SESSION['winkelmand'])) {
    print("Yarr, de winkelmand is leeg");
} else {
    if(isset($_POST['action'])) {
        $action = explode(" ", $_POST['action']);//action bestaat uit een command + een index

        if($action[0] == "remove") {//action is verwijderen
            if($_SESSION['winkelmand'][$action[1]]['aantal'] == 1)//bij 0 producten uit de mand verwijderen
                $_SESSION['winkelmand'] = removeRow($_SESSION['winkelmand'], $action[1]);//action[1] is de index van het product
            else
                $_SESSION['winkelmand'][$action[1]]['aantal']--;

            if(checkEmpty($_SESSION['winkelmand']))
                print("Yarr, de winkelmand is leeg");//beetje voor de UX        
        }

        elseif($action[0] == "add") {//action is toevoegen
            $_SESSION['winkelmand'][$action[1]]['aantal']++;
        }
    }
    
    $_SESSION['winkelmand'] = groupArray($_SESSION['winkelmand']);//om de elementen te groupen

    foreach($_SESSION['winkelmand'] as $key => $product) {
        print ('
        <div>
            <h5>'.$product['StockItemName'].'</h5>
            <p>€'.$product['UnitPrice'].'
            Aantal: '.$product['aantal'].'</p>
        </div>
       ');
        $StockItemImage = getStockItemImage($product['StockItemID'], $databaseConnection);
        if ($StockItemImage != null) {
            print ('<img src="Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'] . '" style="height: 120px;">');
        }
        print('
        <form method="post" action="winkelmand.php">
            <button type="submit" name="action" value="remove '. $key.'" style="background-color: #856404">-</button>'.//verwijderen van product
            '<button type="submit" name="action" value="add '. $key.'" style="background-color: green; ">+</button>'.//voeg 1 product toe
        '</form>
        ');//ik gebruik remove in value, zodat we wellicht later nog een andere functionaliteit kunnen toevoegen: toevoegen via de winkelmand - James
        print("<br>");
    }

    //checkt het totaalbedrag
    if (!checkEmpty($_SESSION['winkelmand'])) {
        $totaal = 0;

        foreach ($_SESSION['winkelmand'] as $product){
            $totaal += $product['UnitPrice'] * $product['aantal'];
        }
        print ("Totaal bedrag: $" . $totaal);
    }
    #hieronder zijn gewoon printjes voor testen
    //print_r($_SESSION['winkelmand'][0][0]);


    #einde
}
?>
