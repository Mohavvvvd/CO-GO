<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/ico.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/index.css">
    <title>co&go</title>


</head>
<body>
    <nav class="navbar navbar-expand-lg ">
        <div class="container-fluid">
          <a class="navbar-brand" href=""><img src="./assets/logo.png" alt="logo" class="logo"></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" target="display" href="./teamplate/home.html">Home</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" target="display" href="" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Services
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="./teamplate/meeting.php" target="display">Meeting room</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="./teamplate/private.php" target="display">Private room</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="./teamplate/public.php" target="display">Public space</a></li>
             
                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link" target="display" href="./teamplate/Events.php">Check Up</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" target="display" href="./teamplate/contact.php">Contact us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" target="display" href="./teamplate/about.php">About</a>
              </li>
              
            </ul>
            <?php 
session_start();
if (!empty($_SESSION['user_id'])): ?>
    <span class="me-2"><a id="usercol" href="./teamplate/info.html" target="display" style="text-decoration-none"> <i class="bi bi-person-fill stat-icon"></i> <?= htmlspecialchars($_SESSION['username']) ?> </a></span>
    <a href="./controller/Userlogout.php" class="btn btn-outline-danger" onclick="setTimeout(() => location.reload(), 1000);">Log Out</a>
<?php else: ?>
    <button class="btn" data-bs-toggle="modal" data-bs-target="#signInModal">Sign In</button>
    <button class="btn" data-bs-toggle="modal" data-bs-target="#signUpModal" id="btn2">Sign Up</button>
<?php endif; ?>
  <div class="modal fade" id="signInModal" aria-hidden="true" aria-labelledby="signInModalLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-3">
        <div class="modal-header">
          <h5 class="modal-title" id="signInModalLabel">Sign In</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="modal-body" method="POST" id="logInForm" onsubmit="return LogInCheck()">
          <button class="btn btn-primary w-100 mb-3">Continue with Facebook</button>
          <button class="btn btn-light w-100 mb-3 border">
            <img src="https://www.google.com/images/branding/googleg/1x/googleg_standard_color_32dp.png" alt="Google" class="me-2" style="width: 20px;">
            Continue with Google
          </button>

          <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1 me-2">or<hr class="flex-grow-1 ms-2">
          </div>
          <div id="loginmsg" class="row g-2 mb-3"></div>
          <input type="email" name="email" class="form-control mb-3" placeholder="Email" id="email1">
          <input type="password" name="password"class="form-control mb-2" placeholder="Password" id="password1">
          <div class="d-flex justify-content-between mb-3">
            <div><input type="checkbox" id="remember1"> <label for="remember1">Remember me</label></div>
            <a href="#" class="text-decoration-none">Forgot Password?</a>
          </div>
          <button class="btn btn-primary w-100 mb-2" type="submit">Log In</button>
          <p class="text-center">Are you new to Convergimmob?</p>
          <button class="btn btn-outline-primary w-100" type="button" onclick="switchModal('#signInModal', '#signUpModal')">Sign Up</button>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="signUpModal" aria-hidden="true" aria-labelledby="signUpModalLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-3">
        <div class="modal-header">
          <h5 class="modal-title" id="signUpModalLabel">Sign Up</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="modal-body" method="POST" id="signupForm" onsubmit="return SignUpCheck()">
          <button class="btn btn-primary w-100 mb-3">Continue with Facebook</button>
          <button class="btn btn-light w-100 mb-3 border">
            <img src="https://www.google.com/images/branding/googleg/1x/googleg_standard_color_32dp.png" alt="Google" class="me-2" style="width: 20px;">
            Continue with Google
          </button>

          <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1 me-2">or<hr class="flex-grow-1 ms-2">
          </div>  
          <div class="row g-2 mb-3" id="signupmsg">
            <div class="col">
              <input type="text" class="form-control" name="First_Name" placeholder="First Name">
            </div>
            <div class="col">
              <input type="text" class="form-control" name="Last_Name" placeholder="Last Name">
            </div>
            <div class="col">
              <input type="text" class="form-control" name="user_name" placeholder="Username">
            </div>
          </div>
          <input type="number" class="form-control mb-3" name="phone" placeholder="Phone number">
          <input type="email" class="form-control mb-3" name="email" id="email2" placeholder="Email">
          <input type="password" class="form-control mb-2" placeholder="Password" id="password2" name="password" maxlength="8">
          <div class="d-flex justify-content-between mb-3">
            <div><input type="checkbox" id="remember2" name="remember"> <label for="remember2">Remember me</label></div>
            <a href="#" class="text-decoration-none">Forgot Password?</a>
          </div>
          <button class="btn btn-primary w-100 mb-2" type="submit">Sign Up</button></form>
          <p class="text-center">Already have an account?</p>
          <button class="btn btn-outline-primary w-100" type="button" onclick="switchModal('#signUpModal', '#signInModal')">Sign In</button>

        </form>
      </div>
    </div>
  </div>
  <script src=".\js\index.js"></script> 
    </div>
      </nav>
    <iframe name="display" src="./teamplate/home.html"></iframe>
    
</body>
</html>