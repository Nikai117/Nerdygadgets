<?php
include __DIR__ . "/header.php";
//        function generateDiscountCode() {
//            return md5(uniqid(mt_rand(), true));
//        }

// Genereer een kortingscode of voer een code in
//        $discountCode = generateDiscountCode();
// Handmatig een code invoeren
//$handmatigeCode = "TESTKORTING"; //Eigen kortingscode bedenken
//
//// Sla de kortingscode op in de database
//$query = "INSERT INTO kortingscodes (code, korting_percentage, geldig_tot) VALUES ('$handmatigeCode', 10, '2023-12-31')";
//// Voeg ook de handmatige code toe als het beschikbaar is (voor gegenereerd code)
//// $query .= ", ('$handmatigeCode', 15, '2023-12-31')";
//$result = mysqli_query($Connection, $query);
//
//if ($result != null) {
//print "Kortingscode succesvol toegevoegd in de database.";
//} else {
//print "Er is een fout opgetreden bij het toevoegen van de kortingscode: " . mysqli_error($Connection);
//}
//
//// Voorbeeld: Controleer de geldigheid van de kortingscode tijdens het afrekenen
if (ISSET($_POST['kortingscode'])) {


    $ingevuldeCode = $_POST['kortingscode']; // Hiermee neem je aan dat de kortingscode wordt ingevoerd

// Controleer de geldigheid van de kortingscode
    $result = discountCodeCheck($ingevuldeCode, $databaseConnection);
    $code = $result["code"];
    $korting_percentage = $result["korting_percentage"];
//Wordt nog verranderd

    if ($result != NULL) {
        echo json_encode(['success' => true, 'code' => $code, 'korting' => $korting_percentage]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Niet gelukt']);
    }
}
?>