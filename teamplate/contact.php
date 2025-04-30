<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <link rel="stylesheet" href="../css/contact.css" />
</head>

<body>
    <div class="main-contact">
        <div class="contact">
            <h1>Contact Us</h1>
            <p>Send us a message</p>
            <form id="contactForm">
                <?php if (empty($_SESSION['user_id'])) { ?>
                <div class="name-input">
                    <label>Name</label>
                    <input name="username" type="text" required />
                </div>
                <div class="email-input">
                    <label>Email</label>
                    <input name="email" type="email" class="email" required />
                </div>
                <?php } else { ?>
                    <p>Welcome, <?php echo $_SESSION['username']; ?>. <br> fil the message bellow to contact.</p>
                <?php } ?>
                <div class="message-input">
                    <label>Message</label>
                    <textarea name="message" placeholder="Type your message..." required></textarea>
                </div>
                <div class="accept-term">
                    <span>
                        <input type="checkbox" required />
                        <p>I accept the terms</p>
                    </span>
                    <button type="submit">Submit</button>
                </div>
            </form>
        </div>

        <div class="image">
            <img src="../assets/contact-image.jpg" alt="Couldn't load image" />
        </div>
    </div>

    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <p class="modal-message" id="text"></p>
        </div>
    </div>

    <div class="contact-means">
        <div class="email">
            <box-icon name="envelope"></box-icon>
            <h2>Email</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <p>hello@relume.io</p>
        </div>
        <div class="live-chat">
            <box-icon name="message-dots"></box-icon>
            <h2>Live Chat</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <p>hello@relume.io</p>
        </div>
        <div class="phone">
            <box-icon name="phone"></box-icon>
            <h2>Phone</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <p>hello@relume.io</p>
        </div>
    </div>

    <div class="location">
        <div class="location-content">
            <div class="title">
                <h1>Locations</h1>
                <p>Better environment to work!</p>
            </div>
            <div class="map">
                <div class="location-desc">
                    <div class="sousse">
                        <h2>Sousse</h2>
                        <p>123 Sample St, Sydney NSW 2000 AU</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="img1" style="height: 300px; width: 500px;" >
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3233.959928072591!2d10.594861175258345!3d35.84998612068625!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd8a6826eca57b%3A0xf698bd059d8e2d13!2s%C3%89cole%20sup%C3%A9rieure%20des%20sciences%20et%20de%20la%20technologie%20de%20Hammam%20Sousse!5e0!3m2!1sfr!2stn!4v1745765020574!5m2!1sfr!2stn" width="500" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

    <iframe src="./footer.html" frameborder="0" id="Footer"></iframe>

    <script src="../js/contact.js"></script>
</body>

</html>
