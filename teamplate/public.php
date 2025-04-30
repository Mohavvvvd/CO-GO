<?php 
    include("../config/connect.php");
    session_start();

    $stmt = $conx->prepare("select numLocal, capacite, surface, rating, prix, nbLocal from local where typeLocal = 'public' ");
    if (!$stmt->execute()) {
        sendResponse(500, "Erreur d'exécution de la requête : " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) { 
        sendResponse(500, "Erreur de récupération des résultats : " . $stmt->error);
    }


    $row = $result->fetch_assoc();
    $numLocal = $row['numLocal'];
    $capacite = $row['capacite'];
    $rating = $row['rating'];
    $surface = $row['surface'];
    $prixLocal = $row['prix'];
    $stmt->close();
        
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/public.css">
    <link rel="stylesheet" href="../css/meeting.css">
    <style>
        #Footer{
    width: 100%;
    height: 370px;
    overflow: hidden;
}

    .btn2{
        text-decoration: unset;
        align-items: center;
        text-decoration: none;text-align: center;
        padding-top: 15px;
    }

    </style>
</head>

<body>
    <p id="title">Services > <span>Meeting Room</span></p>
    <div class="image-container">
        <div class="grid-container">
            <div class="box1">
                <img src="../assets/public1.jpg">
            </div>
            <div class="box">
                <img src="../assets/public2.jpg">
            </div>
            <div class="box">
                <img src="../assets/public3.jpg">
            </div>
            <div class="box">
                <img src="../assets/public4.jpg">
            </div>
        </div>
    </div>
    
    <div class="image-container-responsive">
        <div class="carousel">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../assets/img1.jpg" class="d-block w-100 carousel-img" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="../assets/img2.jpg" class="d-block w-100 carousel-img" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="../assets/img3.jpg" class="d-block w-100 carousel-img" alt="...">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>
    
    <div class="content">
        <div class="description">
            <div class="info">
                <div class="info1">
                    <img src="../assets/icon1.png">
                    <p>Public Space</p>
                </div>

                <div class="info1">
                    <img src="../assets/icon2.png">
                    <p><?php echo $capacite ?> guests</p>
                </div>

                <div class="info1">
                    <img src="../assets/icon3.png">
                    <p><?php echo $prixLocal ?> Dinar</p>
                </div>

                <div class="info1">
                    <img src="../assets/icon4.png">
                    <p><?php echo $surface ?></p>
                </div>
            </div>
            <div class="desc">
                <h3>Description</h3>
                <p>The public room is a spacious, shared area designed for large group meetings, workshops, and presentations. It features a modular setup with flexible seating arrangements for up to 20 participants, a large presentation screen, wireless screen-sharing tools, and a premium sound system. The room is equipped with high-speed Wi-Fi, microphones, and a whiteboard for collaborative sessions. Open yet thoughtfully designed to minimize distractions, the conference room offers a professional atmosphere enhanced by natural light and modern décor. It is ideal for seminars, team briefings, networking events, and training sessions, and can be booked by the hour or for full-day events</p>
            </div>
        </div>
        <div class="request">
            <div class="box-choice">
            <?php 
                    if (isset($_SESSION['user_id'])) {
                        echo '<a class="btn2" target="display" href="../teamplate/reservation.php">book now</a>';
                    } else {
                        echo '<div class="alert">You need to sign up to make a reservation.</div>';
                    }               
                ?>
            </div>
        </div>
    </div>
    <div class="offer">
    <h2>What this place offers</h2>
    <div class="offer-grid">
        <div class="off">
            <img src="../assets/equip1.png" alt="">
            <p>TV</p>
        </div>

        <div class="off" id="e2">
            <img src="../assets/equip2.png" alt="">
            <p>Wifi</p>
        </div>

        <div class="off" id="e1">
            <img src="../assets/equip3.png" alt="">
            <p>Air conditioning</p>
        </div>

        <div class="off" id="e1">
            <img src="../assets/equip4.png" alt="">
            <p>Orthopedic chair</p>
        </div>

        <div class="off" id="e1">
            <img src="../assets/equip5.png" alt="">
            <p>Coffee machines</p>
        </div>

        <div class="off">
            <img src="../assets/equip6.png" alt="">
            <p>Kitchen</p>
        </div>
    </div>
</div>
<div class="equipement">
    <h2 style="margin-top: 30px; margin-left: 140px;">Equipement</h2>
    <div class="equipement-grid">
        <div class="equip" id="equip0">
            <img src="../assets/equipement1.png" alt="">
            <p>Video conferencing camera</p>
        </div>

        <div class="equip" id="equip1">
            <img src="../assets/equipement2.png" alt="">
            <p>Speaker System</p>
        </div>

        <div class="equip" id="equip2">
            <img src="../assets/equipement3.png" alt="">
            <p>Screen</p>
        </div>

        <div class="equip" id="equip3">
            <img src="../assets/equipement4.png" alt="">
            <p>Whiteboard walls</p>
        </div>

        <div class="equip" id="equip4">
            <img src="../assets/equipement5.png" alt="">
            <p>Projector</p>
        </div>

        <div class="equip" id="equip5">
            <img src="../assets/equipement6.png" alt="">
            <p>Printer</p>
        </div>

        <div class="equip" id="equip6">
            <img src="../assets/equipement7.png" alt="">
            <p>High-quality microphone</p>
        </div>

        <div class="equip" id="equip7">
            <img src="../assets/equipement8.png" alt="">
            <p>VR headset setup</p>
        </div>
    </div>
</div>
<iframe src="./footer.html" frameborder="0" id="Footer"></iframe>
</body>

</html>