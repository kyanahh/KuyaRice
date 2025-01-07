<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["userid"])){
        $textaccount = $_SESSION["userid"];
    }else{
        $textaccount = "Account";
    }
    // Check if it's the first visit after login
    $showToast = isset($_SESSION["show_toast"]) && $_SESSION["show_toast"] === true;
    unset($_SESSION["show_toast"]); // Clear the toast flag after showing
}else{
    $textaccount = "Account";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tapsihan ni Kuya Rice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown:hover .dropdown-toggle {
            background-color: #000000;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg py-3 bg-black text-white">
        <div class="container-fluid">
            <img src="../img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
          <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav grid gap-3">

              <!-- HOME -->
              <li class="nav-item">
                <a class="nav-link text-white fw-bold" aria-current="page" href="customerhome.php">Home</a>
              </li>

              <!-- MENU -->
              <li class="nav-item text-white">
                <a class="nav-link text-white fw-bold" aria-current="page" href="menu.php">Menu</a>
              </li>

              <!-- ORDERS -->
              <li class="nav-item text-white">
                <a class="nav-link text-white fw-bold" aria-current="page" href="orders.php">Orders</a>
              </li>

              <!-- MY ACCOUNT -->
              <li class="nav-item me-3">
                    <div class="dropdown">
                        <button class="btn btn-black text-white dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            My Account
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="account.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
              </li>
              
              
            </ul>
          </div>
        </div>
      </nav>

      <div class="d-flex justify-content-center mt-5">
        <div class="col-sm-8">
            <div class="d-flex justify-content-center">
                <img src="../img/logobg.png" alt="KUYA RICE">
            </div>
            <div class="d-flex justify-content-center">
                <h3 class="fw-bold my-5">Available Branches in Muntinlupa, Dasmariñas, Cabuyao</h3>
            </div>
            <h2 class="fw-bold">Menu</h2>
            <p class="fs-5">What are you craving for today?</p>
            <div class="d-flex justify-content-center">
                <img src="../img/menufinal.png" alt="MENU" class="w-75">
            </div>
            <div class="d-flex justify-content-center">
                <a href="menu.php" class="btn btn-danger py-3 px-5 mt-3 fw-bold">ORDER NOW</a>
            </div>
            <h2 class="fw-bold mt-5">Featured</h2>
            <p class="fs-5">Discover your new favorites here!</p>
            <div class="d-flex justify-content-center">
                <img src="../img/featured.jpg" alt="Featured" class="w-75">
            </div>
            <div class="d-flex justify-content-center">
                <a href="menu.php" class="btn btn-danger py-3 px-5 mt-3 fw-bold">ORDER NOW</a>
            </div>
        </div>
      </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="loginToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    Successfully logged in!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
      
    <!-- FOOTER -->
      <footer class="bg-black d-flex align-items-center mt-5" style="height: 200px;">
        <div class="container-fluid row m-2 text-white">
            <div class="row">
                <div class="col-sm-5">
                    <img src="../img/logobg.png" alt="KUYA RICE" class="w-50">
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">About</a>
                    </div>
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">Contact Us</a><br>
                    </div>
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">Careers</a><br>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">Terms & Conditions</a>
                    </div>
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white">Privacy Policy</a>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="row">
                        <div class="d-flex align-items-center gap-3">
                            <a href="https://www.facebook.com/profile.php?id=100064204447018" class="text-decoration-none text-white">
                                <i class="bi bi-facebook fs-5"></i>
                            </a>
                            <a href="m.me/100355241697170" class="text-decoration-none text-white">
                                <i class="bi bi-messenger fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </footer>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Show the toast automatically if `$showToast` is true
        <?php if ($showToast): ?>
        const loginToast = new bootstrap.Toast(document.getElementById('loginToast'));
        loginToast.show();
        <?php endif; ?>
    </script>

</body>
</html>