<?php

require("server/connection.php");

$usertype = "Customer";

$firstname = $lastname = $phone = $gender = $homeaddress = $email = $password = $confirmpassword = $errorMessage = $successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname =  ucwords($_POST["firstname"]);
    $lastname =  ucwords($_POST["lastname"]);
    $phone = $_POST["phone"];
    $gender = ucwords($_POST["gender"]);
    $email = $_POST["email"];
    $password = $_POST["password"];
    $homeaddress = ucwords($_POST["homeaddress"]);

    if (empty($firstname) || empty($lastname) || empty($homeaddress) || empty($phone) || empty($gender) || 
    empty($email) || empty($password)) {
        $errorMessage = "All fields are required";
    } else {
        // Check if the email already exists in the database
        $emailExistsQuery = "SELECT * FROM users WHERE email = '$email'";
        $emailExistsResult = $connection->query($emailExistsQuery);

        if ($emailExistsResult->num_rows > 0) {
            $errorMessage = "User already exists";
        } else {
            // Insert the user data into the database
            $insertQuery = "INSERT INTO users (firstname, lastname, phone, gender, email, homeaddress, 
            password, usertype) VALUES ('$firstname', '$lastname', '$phone', '$gender', '$email', 
            '$homeaddress', '$password', '$usertype')";
            $result = $connection->query($insertQuery);

            if (!$result) {
                $errorMessage = "Invalid query " . $connection->error;
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toastEl = document.getElementById('successToast');
                        const toast = new bootstrap.Toast(toastEl);
                        toast.show();
            
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 3000); // Redirect after 3 seconds
                    });
                </script>";
            }
            
        }
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
<body class="bg-black">
    
    <nav class="navbar navbar-expand-lg py-3 bg-black text-white">
        <div class="container-fluid">
            <img src="img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
          <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav grid gap-3">

              <!-- HOME -->
              <li class="nav-item">
                <a class="nav-link text-white fw-bold" aria-current="page" href="home.html">Home</a>
              </li>

              <!-- MENU -->
              <li class="nav-item text-white">
                <a class="nav-link text-white fw-bold" aria-current="page" href="login.php">Menu</a>
              </li>

              <!-- ORDERS -->
              <li class="nav-item text-white">
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
    <div class="container-fluid">
        <div class="card mt-5 col-md-6 mx-auto">
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
                <h4 class="card-title fw-bold text-center my-3">Sign Up</h4>
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $firstname; ?>" placeholder="First Name" required>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $lastname; ?>" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <select id="gender" name="gender" class="form-select" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male" <?php echo ($gender === 'Male') ? "selected" : ""; ?>>Male</option>
                                <option value="Female" <?php echo ($gender === 'Female') ? "selected" : ""; ?>>Female</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="Phone Number" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Email Address" required>
                        </div>
                        <div class="col">
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="text" class="form-control" id="homeaddress" name="homeaddress" value="<?php echo $homeaddress; ?>" placeholder="Home Address" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Passwords
                        </div>
                    </div>

                    <div class="row">
                        <div class="col d-grid gap-2">
                            <?php
                            if (!empty($successMessage)) {
                                echo "
                                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                    <strong>$successMessage</strong>
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>
                                ";
                            }
                            ?>
                            <button type="submit" class="btn btn-dark mt-3 fw-bold">Sign Up</button>
                        </div>
                    </div>

                    <div class="row d-grid gap-2">
                        <p class="text-center mt-2">Already have an account?<a href="login.php" class="text-decoration-none"> Login here.</a></p>
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

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Account Successfully Created
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function togglePassword() {
            var password = document.getElementById("password");
            if (password.type === "password") {
                password.type = "text";
            } else {
                password.type = "password";
            }
        }
    </script>

</body>
</html>