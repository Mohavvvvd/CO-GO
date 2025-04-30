<?php
include("../config/connect.php");
session_start();
$idUser = $_SESSION['user_id'];
$montant = 0;
$taxRate = 0.13;
    


$stmt = $conx->prepare("select numLocal, typeLocal, capacite, surface, rating, prix, nbLocal from local");
    if (!$stmt->execute()) {
        sendResponse(500, "Erreur d'exécution de la requête : " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) { 
        sendResponse(500, "Erreur de récupération des résultats : " . $stmt->error);
    }


    $row = $result->fetch_assoc();
    $numLocal = $row['numLocal'];
    $typeLocal = $row['typeLocal'];
    $capacite = $row['capacite'];
    $rating = $row['rating'];
    $surface = $row['surface'];
    $prixLocal = $row['prix'];
    $nbLocal = $row["nbLocal"];
    $stmt->close();
        
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CO&GO - Coworking Space Booking</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/reservation.css">
    <link rel="stylesheet" href="../css/home.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Left Column - Room Details and Booking Form -->
            <div class="col-lg-8 left-column">
                <!-- Room Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="../assets/elena-baidak-pz69kY0UQuQ-unsplash.jpg" alt="Meeting Room"
                                    class="img-fluid rounded room-image">
                            </div>
                            <div class="col-md-8">
                                <h3><?php echo $typeLocal ?></h3>
                                <div class="mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                    <span class="ms-2">
                                        <?php echo $rating ?>
                                    </span>
                                    <small class="text-muted">(146 Reviews)</small>
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <span><i class="fas fa-door-closed me-2"></i>
                                        <?php echo $typeLocal ?>
                                    </span>
                                    <span><i class="fas fa-users me-2"></i>
                                        <?php echo $capacite ?>
                                    </span>
                                    <span><i class="fas fa-expand-arrows-alt me-2"></i>
                                        <?php echo $surface ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Preferences -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="mb-4">Booking Preferences</h4>
                        <form id="bookingForm" action="reservation.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Day</label>
                                    <input type="datetime-local" class="form-control" id="startDate" name="startDate" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Day</label>
                                    <input type="datetime-local" class="form-control" id="endDate" name="endDate" required>
                                </div>
                                
                            </div>

                            <!-- Equipment Section -->
                            <div class="mt-4">
                                <h5>Equipment</h5>
                                <div class="equipment-list">
                                    <div class="flex-equipement">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="whiteboard"
                                                name="equipement[]" value="whiteboard">
                                            <label class="form-check-label" for="whiteboard">Whiteboard walls</label>
                                        </div>
                                        <input type="number" id="nbEquipement" min="0" max="3" name="nbwhiteboard" class="whiteboard" disabled >
                                    </div>

                                <div class="flex-equipement">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="VRH"
                                            name="equipement[]" value="VRH">
                                        <label class="form-check-label" for="VRH">VR Headset</label>
                                    </div>
                                    <input type="number" id="nbEquipement" min="0" max="3" name="nbVRH" class="VRH" disabled>
                                </div>

                                <div class="flex-equipement">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="projector"
                                            name="equipement[]" value="projector">
                                        <label class="form-check-label" for="projector">Projector</label>
                                    </div>
                                    <input type="number" id="nbEquipement" min="0" max="3" name="nbprojector" class="projector" disabled>
                                </div>
                                
                                <div class="flex-equipement">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="printer" name="equipement[]"
                                            value="printer">
                                        <label class="form-check-label" for="printer">Printer</label>
                                    </div>
                                    <input type="number" id="nbEquipement" min="0" max="3" name="nbprinter" class="printer" disabled>
                                </div>

                                <div class="flex-equipement">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="screen" name="equipement[]"
                                            value="screen">
                                        <label class="form-check-label" for="screen">Screen</label>
                                    </div>
                                    <input type="number" id="nbEquipement" min="0" max="3" name="nbscreen" class="screen" disabled>
                                </div>

                                <div class="flex-equipement">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="microphone"
                                            name="equipement[]" value="microphone">
                                        <label class="form-check-label" for="microphone">High-quality microphone</label>
                                    </div>
                                    <input type="number" id="nbEquipement" min="0" max="3" name="nbmicrophone" class="microphone" disabled>
                                </div>
                                
                                <div class="flex-equipement">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="speaker" name="equipement[]"
                                            value="speaker">
                                        <label class="form-check-label" for="speaker">Speaker system</label>
                                    </div>
                                    <input type="number" id="nbEquipement" min="0" max="3" name="nbspeaker" class="speaker" disabled>
                                </div>

                                <div class="flex-equipement">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="camera" name="equipement[]"
                                            value="camera">
                                        <label class="form-check-label" for="camera">Video conferencing camera</label>
                                        
                                    </div>
                                    <input type="number" id="nbEquipement" min="0" max="3" name="nbcamera" class="camera" disabled>
                                </div>
                                </div>
                            </div>

                            <div class="col-12">
                                    <label class="form-label">Guests</label>
                                    <input type="number" class="form-control" id="guests" name="guests"
                                        placeholder="Number of Guests" min="1" max="16" required>
                                </div>

                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mb-4">Payment Details</h4>
                                    <div class="row g-3">

                                        <div id="cardPaymentFields">
                                            <div class="col-12">
                                                <label class="form-label">Cardholder Name</label>
                                                <input type="text" class="form-control" id="cardholderName"
                                                    name="cardholderName" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Card Number</label>
                                                <input type="text" class="form-control" id="cardNumber"
                                                    placeholder="Card Number" name="cardNumber" required maxlength="19">
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Expiry</label>
                                                    <input type="text" class="form-control" id="expiry"
                                                        placeholder="MM/YY" name="expiry" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">CVV</label>
                                                    <input type="text" class="form-control" id="cvv" placeholder="CVV"
                                                        name="cvv" required maxlength="3">
                                                </div>

                                            </div>
                                        </div>
                                        <div id="cashPaymentMessage" class="col-12 d-none">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Please pay the total amount in cash at the reception desk when you
                                                arrive.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="total" id="totalInput">
                            </div>
                            <button type="button" class="btn btn-primary w-100 mt-4" id="bookButton">Book</button>
                        </form>
                    </div>


                </div>
            </div>

            <div id="successModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <p>Message sent successfully!</p>
                </div>
            </div>

            <!-- Right Column - Booking Details -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-4">Booking details</h4>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Check in</label>
                                <div class="fw-bold" id="summaryCheckIn">--</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Check out</label>
                                <div class="fw-bold" id="summaryCheckOut">--</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Total length of stay</label>
                                <div class="fw-bold" id="summaryDuration">--</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Equipment</label>
                                <ul class="list-unstyled" id="summaryEquipment">

                                </ul>
                            </div>
                        </div>

                        <hr>

                        <h5>Your Pricing Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span id="roomSummary">Meeting Room A (1 Guests):</span>
                            <span id="basePrice">$0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Extras:</span>
                            <span id="extrasPrice">$0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>TVA:</span>
                            <span id="taxAmount">$3.90</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Price</span>
                            <span class="text-primary" id="totalPrice">$0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <iframe src="./footer.html" frameborder="0" id="Footer"></iframe>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../js/reservation.js"></script>
    <script>
        function showModal() {
            document.getElementById("successModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("successModal").style.display = "none";
        }
    </script>
    <!-- <script src="js/equipement.js"></script> -->
</body>

</html>

<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['startDate'], $_POST['endDate'], $_POST['guests'], $_POST['cardholderName'], $_POST['cardNumber'], $_POST['expiry'], $_POST['cvv']) &&
        !empty($_POST['startDate']) &&
        !empty($_POST['endDate']) &&
        !empty($_POST['guests']) &&
        !empty($_POST['cardholderName']) &&
        !empty($_POST['cardNumber']) &&
        !empty($_POST['expiry']) &&
        !empty($_POST['cvv'])
    ) {
        $dt = new DateTime($_POST['startDate']);
        $startDate = $dt->format('Y-m-d H:i:s');
        $dt1 = new DateTime($_POST['endDate']);
        $endDate = $dt1->format('Y-m-d H:i:s');
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        $interval = $start->diff($end);
        $days = $interval->days;
        $guests = htmlspecialchars($_POST['guests']);
        $cardholderName = htmlspecialchars($_POST['cardholderName']);
        $cardNumber = trim(htmlspecialchars($_POST['cardNumber']));
        $expiry = htmlspecialchars($_POST['expiry']);
        $cvv = htmlspecialchars($_POST['cvv']);
        $extra = 0.0;

        $equipementData = [];
        $resEq = [];
        if (!empty($_POST['equipement'])) {
            $equipement = $_POST['equipement'];
            foreach ($equipement as $equip) {
                $nbEquip = $_POST["nb".$equip];
                // echo $nbEquip;
                    $equipementData[] = [   
                        $equip => $nbEquip
                    ];
                    $resEq[] = $equip ;

                // $nbEquipement->close();
                $stock = $conx->prepare("UPDATE equipement SET nbEquipement = nbEquipement - ? WHERE nomEquipment = ? AND nbEquipement >= 0");
                $stock->bind_param("is",$nbEquip, $equip);
                $stock->execute();
                $price = $conx->prepare("select prix from equipement where nomEquipment = ? ");
                $price->bind_param("s", $equip);
                $price->execute();
                $result = $price->get_result();
                $row = $result->fetch_assoc();
                $price = $row['prix'];
                if($nbEquip > 0){
                    $extra = $extra + ($price * $nbEquip);
                }
                
            }
            $x = json_encode($resEq);
        }

        $base = $days * $prixLocal;
        $newBase = $base * $guests;
        $subTotal = $newBase + $extra;
        $tax = $subTotal * $taxRate;
        $montant = $subTotal + $tax;

        $equipements = json_encode($equipementData);

            
            $idReservation = 'reservation' . uniqid();
            $idFact = 'facture' . uniqid();
            $insertPayment = $conx-> prepare('insert into paiment(idFacture, idUser, numCreditCard, montant, idReservation, creditCardHolder) values(?,?,?,?,?,?)' );
            $insertPayment->bind_param("sssdss", $idFact, $idUser,$cardNumber, $montant, $idReservation, $cardholderName);

            
            if($insertPayment->execute()) {
                $insertReservation = $conx->prepare('INSERT INTO reservation (idReservation, idUser, numLocal, startDate, endDate, nbPeople, montant, equipement) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $insertReservation->bind_param('ssissids',$idReservation, $idUser, $numLocal, $startDate, $endDate, $guests, $montant, $x);
                $insertReservation->execute();
                
                $updateLocal = $conx->prepare("UPDATE local set nbLocal = nbLocal - 1 WHERE numLocal = ?");
                $updateLocal -> bind_param("i", $numLocal);
                $updateLocal->execute();
                $insertReservation->close();
                $insertPayment->close();
                $updateLocal->close();

            }
            

                
    }
}
else{
    echo $_SERVER["REQUEST_METHOD"];
}
?>