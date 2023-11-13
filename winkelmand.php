<?php
include __DIR__ . "/header.php";

function removeRow($arr, $index)
{
    unset($arr[$index]);
    array_splice($arr, $index, 0);//om de NULL row te verwijderen die je krijgt van unset()

    return $arr;
}

// function findInArray($needle, $stack) {
//     foreach($stack as $item) {
//         if($item['StockItemID'] == $needle) {//needle is de ID dat je wilt vinden
//             return true;
//         }
//     }

//     return false;
// }

// function findIndexArray($needle, $stack) {
//     foreach($stack as $key => $item) {
//         if($item['StockItemID'] == $needle) {//het staat al vast dat de needle in de stack zit, we hebben alleen een preciese plek (index) nodig. de index van de eerste match wordt gereturned
//             return $key;
//         }
//     }
// }

// function groupArray($arr) {
//     $new_arr = array();//nieuwe array initialiseren. 

//     foreach($arr as $item) {
//         if($item['aantal'] == 1) {//het item is nog niet gegroupd/kan niet groupen
//             if(findInArray($item['StockItemID'], $new_arr)) {//als true, element zat hiervoor al in de array
//                 $index = findIndexArray($item['StockItemID'], $new_arr);//index vinden van dat product

//                 $new_arr[$index]['aantal']++;//increment het aantal van het gezochte product   

//             } else {//het zit nog niet in de array

//                 $new_arr[] = $item;//het product zit er niet dubbel in, dus voeg je deze simpelweg toe

//             }
//         } else {
//             $new_arr[] = $item;//als het item hiervoor gegroupd is, dan direct toevoegen
//         }
//     }

//     return $new_arr;
// }

if (!isset($_SESSION['winkelmand']) || $_SESSION['winkelmand'] == NULL) {
    print('<h1 style="text-align: center">Yarr, de winkelmand is leeg</h1>
            <h2 style="text-align: center"><a class="redirect" href="browse.php"> Op zoek naar producten?</a></h2>');
} else {

    if ($_POST != NULL) {//actionTarget kan zijn meegegeven, maar mag niet NULL zijn

        //declareert de key van het product waarop de actie zal worden uitgevoerd
        foreach ($_POST as $value) {
            $key = $value;
        }

        if (isset($_SESSION['winkelmand'][$key])) {//pre-conditie: $actionTarget moet een array key zijn

            //loopt een keer door post om action te krijgen en target. alleen maar else-ifs voor als bijv. iemand met inspect de name heeft aangepast
            foreach ($_POST as $action => $target) {

                if ($action == "remove") {
                    if ($_SESSION['winkelmand'][$target]['aantal'] == 1)//bij 0 producten uit de mand verwijderen
                        $_SESSION['winkelmand'] = removeRow($_SESSION['winkelmand'], $target);
                    else
                        $_SESSION['winkelmand'][$target]['aantal']--;
                    header("Location: winkelmand.php");

                } elseif ($action == "add") {//nog een limiet stellen
                    $_SESSION['winkelmand'][$target]['aantal']++;
                    header("Location: winkelmand.php");

                } elseif ($action == "clear") {
                    $_SESSION['winkelmand'] = removeRow($_SESSION['winkelmand'], $target);
                }
            }

            if ($_SESSION['winkelmand'] == NULL)
                header("Location: winkelmand.php");//beetje voor de UX
        }
    }
    ?>

    <div id="prijs">
        <?php


        //checkt het totaalbedrag
        if (isset($_SESSION['winkelmand'])) {
            $productTotaal = 0;
            $productUnits = 0;

            foreach ($_SESSION['winkelmand'] as $product) {
                $productTotaal += $product['SellPrice'] * $product['aantal'];
                $productUnits = $productUnits + $product['aantal'];

            }

//            verzendkosten berekenen
            if ($productTotaal <= 300) {
                //gebruik je voor berekeningen
                $verzendkosten = number_format(35.50, 2);
                //gebruik je voor tekst en UI
                $verzendkostenText = "€" . $verzendkosten;
            } else {
                $verzendkosten = 0;
                $verzendkostenText = "gratis";
            }

            //service kosten berekenen
            if ($productUnits <= 5) {
                //gebruik je voor berekeningen
                $serviceKosten = number_format(22.50, 2);
                //gebruik je voor tekst en UI
            } else {
                $serviceKostenBerekening = 22.50 + (2.50 * $productUnits);
                $serviceKosten = number_format($serviceKostenBerekening, 2);
            }
            $serviceKostenText = "€" . $serviceKosten;

            //zodat afgeronde getallen altijd 2 decimalen hebben (9.6 => 9.60)
            $productTotaal = number_format(round($productTotaal, 2), 2, '.', '');

            print("Productkosten: €" . $productTotaal . "<br>");
            print("Verzendkosten: $verzendkostenText,-<br>");
            print("Servicekosten: $serviceKostenText,-<br>");

            //Check of er producten limiet wordt overschreden
            if ($productUnits <= 500) {
                print ("Totaal bedrag: €" . $productTotaal + $verzendkosten + $serviceKosten . "<br>");
            } else
                print ("Verzenden niet mogelijk door te hoog aantal producten, bel service desk a.u.b.");
            print ("<br><i>Inclusief BTW</i>");
        } ?>
    </div>

    <div id="lijst">
        <table id="winkelmand">

            <?php foreach ($_SESSION['winkelmand'] as $key => $product) {
                print ('<tr><td>
                    <div>
                        <a href="view.php?id=' . $product['StockItemID'] . '">
                            <h5>' . $product['StockItemName'] . '</h5>
                        </a>
                        <p>€' . number_format(round($product['SellPrice'], 2), 2, '.', '') . '
                        Aantal: ' . $product['aantal'] . '</p>
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
                        <button type="submit" name="remove" value="' . $key . '" class="removebutton">-</button>' .//verwijderen van product
                    '<button type="submit" name="add" value="' . $key . '" class="addbutton">+</button>' .//voeg 1 product toe
                    '<button type="submit" name="clear" value="' . $key . '" class="bin"><img alt="Prullenbak" src="Public/Img/Prullenbak.png" width="20" height="20"></button>' .
                    '</form>
                    ');
                print("<br></td></tr>");
            } ?>


        </table>
    </div>
<?php }
?>

<!-- plaats alles wat niet PHP is voorlopig onderaan-->
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

    .bin {
        background-color: #f2f2f2;
    }

    .bin:hover {
        background-color: #d6d6d6;
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