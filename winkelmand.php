<?php
//onze session array heet "winkelmand". dus je roept het bijvoorbeeld zo op: $_SESSION['winkelmand'][0][0]['UnitPrice']
include __DIR__ . "/header.php";       

function checkEmpty($arr) {
    if(!isset($arr[0]) || $arr[0] == NULL) {
        print ("Yarr, de winkelmand is leeg."); 
        return true;//het eerste element is leeg -> de hele winkelmand is leeg
    }

    return false; //eerste element is niet null 
}

if(!checkEmpty($_SESSION['winkelmand'])) {
    if(isset($_POST['action'])) {
        $action = explode(" ", $_POST['action']);
        if($action[0] == "remove") {//mogelijke functionaliteit om via de winkelwagen producten toe te voegen  
            unset($_SESSION['winkelmand'][0][$action[1]]);//$action[1] is de index van het product in de session array
            array_splice($_SESSION['winkelmand'], $action[1], 1);//om de NULL row te verwijderen die je krijgt van unset()
            
            
            header("Location: winkelmand.php");//refresh zodat de user          
        }
    }

    for($i = 0; $i < count($_SESSION['winkelmand']); $i++) {
        print_r($_SESSION['winkelmand'][$i][0]);
        print('
        <form method="post" action="">
            <button type="submit" name="action" value="remove '. $i.'">Verwijder</button>
        </form>
        ');//ik gebruik remove in value, zodat we wellicht later nog een andere functionaliteit kunnen toevoegen: toevoegen via de winkelmand - James
        print("<br>");
    }

    #hieronder zijn gewoon printjes voor testen
    //print_r($_SESSION['winkelmand'][0][0]);


    #einde
}
?>
