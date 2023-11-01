<?php
//onze session array heet "winkelmand". dus je roept het bijvoorbeeld zo een prijs op: $_SESSION['winkelmand'][0]['UnitPrice']
include __DIR__ . "/header.php";       

function checkEmpty($arr) {
    return !isset($arr[0]); //eerste element is null 
}

function removeRow($arr, $index) {
    unset($arr[$index]);//de index van het product in de session array
    array_splice($arr, $index, 0);//om de NULL row te verwijderen die je krijgt van unset()

    return $arr;
}

function findInArray($needle, $stack) {
    #met for-loop
    //for($i = 0; $i < $upBound; $i++) {
    //    if($stack[$i]['StockItemID'] == $needle) {
    //        print("gevonden match");
    //        return true;
    //    }
    //}
    #end

    #met foreach-loop
    foreach($stack as $item) {
        if($item['StockItemID'] == $needle) {//needle is de ID dat je wilt vinden
            return true;
        }
    }
    #end

    return false;
}

function findIndexArray($needle, $stack) {
    #met for-loop
    //for($i = 0; $i < $upBound; $i++) {
    //    if($stack[$i]['StockItemID'] == $needle) {
    //        print("gevonden index");
    //        return $i;
    //    }
    //}
    #end

    #met foreach-loop
    foreach($stack as $key => $item) {
        if($item['StockItemID'] == $needle) {//het staat al vast dat de needle in de stack zit, we hebben alleen een preciese plek (index) nodig. de index van de eerste match wordt gereturned
            return $key;
        }
    }
    #end
}

function groupArray($arr) {
    $new_arr = array();//nieuwe array initialiseren. alleen nodig bij het foreach-algoritme

    #met for-loop
    //for($i = 0; $i < count($arr); $i++) {//sess_arr is niet null, want deze functie wordt alleen opgeroepen mits sess_arr != null
    //    if($arr[$i]['aantal'] == 1 && $i > 0) {//dit niet bij het eerste element in de array
    //        if(findInArray($arr[$i]['StockItemID'], $arr, $i)) {//als true, element zat hiervoor al in de array
    //            $index = findIndexArray($arr[$i]['StockItemID'], $arr, $i);
    //            $arr[$index]['aantal']++;
    //            $arr = removeRow($arr, $i);
    //        }
    //    }
    //}
    #end

    #met foreach-loop.  findInArray en findIndexArray hun parameters aanpassen als deze gebruikt wordt
    foreach($arr as $item) {
        if($item['aantal'] == 1) {//het item is nog niet gegroupd/kan niet groupen
            if(findInArray($item['StockItemID'], $new_arr)) {//als true, element zat hiervoor al in de array
                $index = findIndexArray($item['StockItemID'], $new_arr);//index vinden van dat product

                $new_arr[$index]['aantal']++;//increment het aantal van het gezochte product   

            } elseif(!findInArray($item['StockItemID'], $new_arr)) {//het zit nog niet in de array

                $new_arr[] = $item;//het product zit er niet dubbel in, dus voeg je deze simpelweg toe

            }
        } else {
            $new_arr[] = $item;//als het item hiervoor gegroupd is, dan direct toevoegen
        }
    }
    #end

    return $new_arr;
}

if(isset($_POST['clear']) && !checkEmpty($_SESSION['winkelmand'])) {//het winkelmandje leegmaken
    $_SESSION['winkelmand'] = array();
}

if(checkEmpty($_SESSION['winkelmand'])) {
    print('<h1 style="text-align: center">Yarr, de winkelmand is leeg</h1>');
} else {
    print('
    <form method="post" action="winkelmand.php">
        <button type="submit" name="clear" value=" " style="background-color: red">Clear mandje</button>'.//knop om het winkelmandje te legen
    '</form>
    ');

    if(isset($_POST['action'])) {
        $action = explode(" ", $_POST['action']);//action bestaat uit een command + een index

        if(isset($action[1]) && $action[1] >= 0 && $action[1] < count($_SESSION['winkelmand'])) {//assertion voor de actieknoppen op de winkelmand pagina

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
        ');
        print("<br>");
    }

    //checkt het totaalbedrag
    if (!checkEmpty($_SESSION['winkelmand'])) {
        $totaal = 0;

        foreach ($_SESSION['winkelmand'] as $product){
            $totaal += $product['UnitPrice'] * $product['aantal'];//prijs van een enkel product * het aantal dat het in de winkelmand zit. exclusief BTW
        }

        print ("Totaal bedrag: €" . $totaal);
    }
    #hieronder zijn gewoon printjes voor testen
    //print_r($_SESSION['winkelmand'][0][0]);


    #einde
}
?>
