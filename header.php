<!-- de inhoud van dit bestand wordt bovenaan elke pagina geplaatst -->
<?php
session_start();
include "database.php";
if(!isset($_SESSION['winkelmand'])) {
    $_SESSION['winkelmand'] = array();//maak de winkelmand session
}
if(!isset($_SESSION['klant'])) {
    $_SESSION['klant'] = array();
}
if (!isset($_SESSION['account'])) {
    $_SESSION['account'] = array();
}
if (!isset($_SESSION['activeUser'])) {
    $_SESSION['activeUser'] = array();
}

if(!isset($_SESSION['verlanglijst'])) {
    $_SESSION['verlanglijst'] = array();
}
if (!isset($_SESSION['payment_id'])) {
    $_SESSION['payment_id'] = NULL;
}

function removeRow($arr, $index)
{
    unset($arr[$index]);
    array_splice($arr, $index, 0);//om de NULL row te verwijderen die je krijgt van unset()

    return $arr;
}

function generateErrorMessage($errorMessage) {
    echo "<script>alert('Error: $errorMessage');</script>";
}

function generateSuccesMessage($succesMessage, $location) {
    echo "<script>
    alert('Succes:$succesMessage');
    window.location.href= '$location';
    </script>";
}

function isLoggedIn() {
    if ($_SESSION['activeUser'] == array()) {
        return false;
    } else {
        return true;
    }
}

$databaseConnection = connectToDatabase();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>NerdyGadgets</title>

    <!-- Javascript -->
    <script src="Public/JS/fontawesome.js"></script>
    <script src="Public/JS/jquery.min.js"></script>
    <script src="Public/JS/bootstrap.min.js"></script>
    <script src="Public/JS/popper.min.js"></script>
    <script src="Public/JS/resizer.js"></script>

    <!-- Style sheets-->
    <link rel="stylesheet" href="Public/CSS/style.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/typekit.css">
</head>
<body>
<div class="Background">
    <div class="row" id="Header">
        <div class="col-2"><a href="./" id="LogoA">
                <div id="LogoImage"></div>
            </a></div>
        <div class="col-8" id="CategoriesBar">
            <ul id="ul-class">
                <?php
                $HeaderStockGroups = getHeaderStockGroups($databaseConnection);

                foreach ($HeaderStockGroups as $HeaderStockGroup) {
                    ?>
                    <li>
                        <a href="browse.php?category_id=<?php print $HeaderStockGroup['StockGroupID']; ?>"
                           class="HrefDecoration"><?php print $HeaderStockGroup['StockGroupName']; ?></a>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <a href="categories.php" class="HrefDecoration">Alle categorieën</a>
                </li>



                <!-- Add a button for Stocksales -->
                <li>
                    <a href="Stocksales.php" class="HrefDecoration">Voorraad opruiming</a>
                </li>



            </ul>
        </div>
<!-- code voor US3: zoeken -->

        <ul id="ul-class-navigation">
            <?php
            if ($_SESSION['activeUser'] == array()){
                echo ' <li>
                <a href="registratie.php">
                    Registreer
                </a>
            </li>
            |
            <li>
                <a href="login.php">
                    Login
                </a>
            </li>';
            } else {
                echo '
                <li>
                    <a href="account.php" style="color: white">', $_SESSION['activeUser'][0]['CustomerName'],'</a>
                </li>
                <li>
                   <a href="logout.php" style="margin-right: 20px">| Log uit</a>
                </li>
                ';
            }
            ?>
            <li>
                <a href="lijst.php?list=Standaard">
                    <img alt="Verlanglijstje" src="Public/Img/Hart.png" width="23" height="20">
                </a>
            <li>
                <a href="winkelmand.php">
                    <img alt="Winkelmandje" src="Public/Img/Winkelmandje.png" width="23" height="20">
                </a>
            </li>
            <li>
                <a href="browse.php" class="HrefDecoration"><i class="fas fa-search search"></i> Zoeken</a>
            </li>





        </ul>



<!-- einde code voor US3 zoeken -->
    </div>
    <div class="row" id="Content">
        <div class="col-12">
            <div id="SubContent">


