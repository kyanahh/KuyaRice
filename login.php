<?php

session_start();

require("server/connection.php");

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $result = $connection->query("SELECT * FROM users  
    WHERE email = '$email' AND password = '$password'");

    if ($result->num_rows === 1) {
        $record = $result->fetch_assoc();

        // Fetch the usertype for the user
        $usertype = $record["usertype"];

        // Set session variables
        $_SESSION["userid"] = $record["userid"];
        $_SESSION["firstname"] = $record["firstname"];
        $_SESSION["lastname"] = $record["lastname"];
        $_SESSION["phone"] = $record["phone"];
        $_SESSION["gender"] = $record["gender"];
        $_SESSION["email"] = $record["email"];
        $_SESSION["homeaddress"] = $record["homeaddress"];
        $_SESSION["usertype"] = $record["usertype"];
        $_SESSION["logged_in"] = true;
        $_SESSION["show_toast"] = true; // Set the flag to show the toast

        $userid = $record["userid"];
        $logtime = date("Y-m-d H:i:s");
        $connection->query("INSERT INTO userlogs (logtime, userid) VALUES ('$logtime', '$userid')");

        // Redirect users based on usertypeid
        if ($usertype == 'Admin') {
            header("Location: /kuyarice/admin/adminhome.php");
        } elseif ($usertype == 'Staff') {
            header("Location: /kuyarice/staff/staffhome.php");
        } elseif ($usertype == 'Customer') {
          header("Location: /kuyarice/customer/customerhome.php");
      }
    } else {
        $errorMessage = "Incorrect email or password";
    }
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
</head>
<body>
    
    <nav class="navbar navbar-expand-lg py-3 bg-black text-white fixed-top">
        <div class="container-fluid">
            <img src="img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
          <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav grid gap-3">

              <!-- HOME -->
              <li class="nav-item">
                <a class="nav-link text-white fw-bold" aria-current="page" href="home.html">Home</a>
              </li>

              <!-- MENU -->
              <li class="nav-item">
                <a class="nav-link text-white fw-bold" aria-current="page" href="login.php">Menu</a>
              </li>

              <!-- ORDERS -->
              <li class="nav-item">
                <a class="nav-link text-white fw-bold" aria-current="page" href="login.php">Orders</a>
              </li>

              <!-- SIGN UP -->
              <li class="nav-item">
                <a class="nav-link text-white fw-bold" href="register.php" role="button">
                  Sign Up 
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link text-white fw-bold">
                  |
                </a>
              </li>

              <!-- LOGIN -->
              <li class="nav-item me-3">
                <a class="nav-link text-white fw-bold" href="login.php" role="button">
                    Login
                  </a>
              </li>
              
            </ul>
          </div>
        </div>
      </nav>

    <!-- MAIN -->
    <div class="container-fluid mt-5 pt-5">
        <div class="card mt-5 col-md-4 mx-auto shadow">
            <div class="card-body">
                <?php
                if (!empty($errorMessage)) {
                    echo "
                    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <strong>$errorMessage</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                    ";
                }
                ?>
                <h4 class="card-title fw-bold text-center my-3">Login</h4>
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
                    <div class="row mt-2">
                        <div class="col input-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email address" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-grid gap-2">
                            <button type="submit" class="btn btn-dark mt-3 fw-bold">Login</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-grid gap-2">
                            <p class="text-center mt-2">Don't have an account yet?<a href="register.php" class="text-decoration-none"> Sign up here.</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
      
    <!-- FOOTER -->
      <footer class="bg-black d-flex align-items-center mt-5" style="height: 200px;">
        <div class="container-fluid row m-2 text-white">
            <div class="row">
                <div class="col-sm-5">
                    <img src="img/logobg.png" alt="KUYA RICE" class="w-50">
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
                        <a href="#" class="text-decoration-none text-white mb-3">FAQs</a>
                    </div>
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
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>

</body>
</html>