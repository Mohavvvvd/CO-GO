<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/about.css">
    <title>co&go</title>
    <style>
        #Footer{
    width: 100%;
    height: 370px;
    overflow: hidden;
}
    </style>
</head>
<body>
    <div class="hero_section">
        <h1>
            Find Your Perfect Workspace-Anytime, Anywhere At Co&Go
        </h1>
        <p>Work smarter. Move faster. Welcome to the future of co-working.</p>
    </div>
    <div class="about_section">
        <div class="left">
            <h2>About CO&GO</h2>
            <p>Welcome to Co&Go, the smart way to book and manage co-working spaces. Whether you're a freelancer, entrepreneur, or part of a remote team, we connect you with the perfect workspace-whenever and wherever you need it.

                With AI-powered recommendations, real-time availability, and seamless booking for both spaces and essential equipment (like projectors and screens), Co&Go makes work flexible and stress-free.
                Work smarter, move faster-because productivity should always be on the go.</p>
        </div>
        <img src="../assets/roman-bozhko-PypjzKTUqLo-unsplash.jpg" alt="image">
    </div>
    <!--*********************************************************************************-->
    <div class="container">
        <div id="teamCarousel" class="position-relative">
            <h2 class="text-center mb-5">Our Team</h2>
            
            <div class="row" id="teamRow">
                <!-- Team members will be rendered here by JavaScript -->
                 
                 <div class="social">
                    <a href="https://www.facebook.com/" target="_blank"><img src="../assets/facebook.png" alt="Facebook"></a>
                    <a href="https://www.instagram.com/" target="_blank"><img src="../assets/instagram.png" alt="Instagram"></a>
                    <a href="https://www.whatsapp.com/" target="_blank"><img src="../assets/whatsapp.png" alt="whatsapp"></a>
                 </div>
            </div>
            
           <div class="row2">
             <!-- Indicators -->
             <div class="carousel-indicators">
            </div>
             <!-- Carousel Controls -->
             <div class="carousel-controls">
                <button type="button" id="prevBtn" aria-label="Previous">
                    <span class="carousel-control-prev-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.3 1.293a1 1 0 0 1 1.414 1.414L6.414 8l6.3 6.293a1 1 0 0 1-1.414 1.414l-7-7a1 1 0 0 1 0-1.414l7-7z"/>
                        </svg>
                    </span>
                </button>
                <button type="button" id="nextBtn" aria-label="Next">
                    <span class="carousel-control-next-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.7 1.293a1 1 0 0 0-1.414 1.414L9.586 8l-6.3 6.293a1 1 0 0 0 1.414 1.414l7-7a1 1 0 0 0 0-1.414l-7-7z"/>
                        </svg>
                    </span>
                </button>
            </div>
            
            
           
           </div>
        </div>
    </div>
    
    <iframe src="./footer.html" frameborder="0" id="Footer"></iframe>
    <script src="../js/script.js"></script>
</body>
</html>