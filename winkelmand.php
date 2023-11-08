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
    foreach($stack as $item) {
        if($item['StockItemID'] == $needle) {//needle is de ID dat je wilt vinden
            return true;
        }
    }

    return false;
}

function findIndexArray($needle, $stack) {
    foreach($stack as $key => $item) {
        if($item['StockItemID'] == $needle) {//het staat al vast dat de needle in de stack zit, we hebben alleen een preciese plek (index) nodig. de index van de eerste match wordt gereturned
            return $key;
        }
    }
}

function groupArray($arr) {
    $new_arr = array();//nieuwe array initialiseren. 

    foreach($arr as $item) {
        if($item['aantal'] == 1) {//het item is nog niet gegroupd/kan niet groupen
            if(findInArray($item['StockItemID'], $new_arr)) {//als true, element zat hiervoor al in de array
                $index = findIndexArray($item['StockItemID'], $new_arr);//index vinden van dat product

                $new_arr[$index]['aantal']++;//increment het aantal van het gezochte product   

            } else {//het zit nog niet in de array

                $new_arr[] = $item;//het product zit er niet dubbel in, dus voeg je deze simpelweg toe

            }
        } else {
            $new_arr[] = $item;//als het item hiervoor gegroupd is, dan direct toevoegen
        }
    }

    return $new_arr;
}?>

<style>
    .redirect {
        all: unset;
    }
    .redirect:hover {
        color: #a08ee6;
        cursor: pointer;
    }
    #lijst {
        margin: auto;
        width: 25%;
        padding: 10px;
        filter: drop-shadow(0 0 0.75rem #413a5e);
    }
    #winkelmand {
        border: 5px solid;
        filter: drop-shadow(0 0 0.75rem #413a5e);
    }
    #winkelmand tr {
        border: 5px solid;
    }
    #winkelmand tr:hover {
        background-color: #4e437a;
    }
    #winkelmand a {
        all: unset;
    }
    #winkelmand a:hover {
        cursor: pointer;
        color: #a08ee6;
    }
    .itemimage {
        height: 120px;
        float: right;
    }
    .removebutton {
        background-color: #856404;
    }
    .removebutton:hover {
        background-color: #735602;
    }
    .addbutton {
        background-color: #02c205;
    }
    .addbutton:hover {
        background-color: #018203;
    }
    #prijs {
        float: right;
        border: 5px solid;
        width: 15%;
        text-align: left;
        padding-left: 10px;
        margin-right: 5%;
        margin-top: 10px;
    }
</style>

<?php
if(checkEmpty($_SESSION['winkelmand'])) {
    print('<h1 style="text-align: center">Yarr, de winkelmand is leeg</h1>
            <h2 style="text-align: center"><a class="redirect" href="browse.php"> Op zoek naar producten?</a></h2>');
} else {
    if(isset($_POST['remove'])) {
        $actionTarget = $_POST['remove'];//geef de value die meegegeven is via de post aan een variabele
    } elseif(isset($_POST['add'])) {
        $actionTarget = $_POST['add'];//actionTarget is de key van de winkelmand array
    }

    if(isset($actionTarget) && $actionTarget != NULL) {//actionTarget kan zijn meegegeven, maar mag niet NULL zijn
        //$action = explode(" ", $_POST['action']);//action bestaat uit een command + een index

        if($actionTarget >= 0 && $actionTarget < count($_SESSION['winkelmand'])) {//assertion voor de actieknoppen op de winkelmand pagina

            if(isset($_POST['remove'])) {//action is verwijderen
                if($_SESSION['winkelmand'][$actionTarget]['aantal'] == 1)//bij 0 producten uit de mand verwijderen
                    $_SESSION['winkelmand'] = removeRow($_SESSION['winkelmand'], $actionTarget);//actionTarget is de index van het product
                else
                    $_SESSION['winkelmand'][$actionTarget]['aantal']--;

                if(checkEmpty($_SESSION['winkelmand']))
                    header("Location: winkelmand.php");//beetje voor de UX        
            }

            elseif(isset($_POST['add'])) {//nog een limiet stellen
                $_SESSION['winkelmand'][$actionTarget]['aantal']++;
            }
        }  
    }
    
    $_SESSION['winkelmand'] = groupArray($_SESSION['winkelmand']);?>

    <div id="prijs">
        <?php
        //checkt het totaalbedrag
        if (!checkEmpty($_SESSION['winkelmand'])) {
            $productTotaal = 0;

            foreach ($_SESSION['winkelmand'] as $product){
                $productTotaal += $product['SellPrice'] * $product['aantal'];
            }

            $productTotaal = round($productTotaal, 2);

            print("Productkosten: €" . $productTotaal . "<br>");
            print("Verzendkosten: €500,-<br>");
            print("Servicekosten: €22222,-<br>");
            print ("Totaal bedrag: €" . $productTotaal + 500 + 22222 . "<br>");
            print ("<br><i>Inclusief BTW</i>");
        }?>
    </div>

    <div id="lijst">
        <table id="winkelmand">
            
                <?php foreach($_SESSION['winkelmand'] as $key => $product) {
                    print ('<tr><td>
                    <div>
                        <a href="view.php?id='.$product['StockItemID'].'">
                            <h5>'.$product['StockItemName'].'</h5>
                        </a>
                        <p>€'.round($product['SellPrice'], 2).'
                        Aantal: '.$product['aantal'].'</p>
                    </div>
                    ');
            
                    $StockItemImage = getStockItemImage($product['StockItemID'], $databaseConnection);
                    if ($StockItemImage != null) {
                        print ('
                                <img src="Public/StockItemIMG/' . $StockItemImage[0]['ImagePath'] . '" class="itemimage">
                                </a>');
                    }
                    print('
                    <form method="post" action="winkelmand.php">
                        <button type="submit" name="remove" value="'.$key.'" class="removebutton">-</button>'.//verwijderen van product
                        '<button type="submit" name="add" value="'.$key.'" class="addbutton">+</button>'.//voeg 1 product toe
                    '</form>
                    ');
                    print("<br></td></tr>");
                }?>
            
        </table>
    </div>
<?php }
?>
