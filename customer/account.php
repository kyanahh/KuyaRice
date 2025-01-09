<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["userid"])){
        $textaccount = $_SESSION["userid"];
        $useremail = $_SESSION["email"];
        $firstname = $_SESSION["firstname"];
        $lastname = $_SESSION["lastname"];
        $gender = $_SESSION["gender"];
        $phone = $_SESSION["phone"];
        $email = $_SESSION["email"];
        $homeaddress = $_SESSION["homeaddress"];

    }else{
        $textaccount = "Account";
    }

}else{
    $textaccount = "Account";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usersid = $_SESSION["userid"];
    $phone = $_POST["phone"];
    $newEmail = $_POST["email"];
    $homeaddress = $_POST["homeaddress"];

    $updateQuery = "UPDATE users SET email = ?, phone = ?, homeaddress = ? WHERE userid = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("sssi", $newEmail, $phone, $homeaddress, $usersid);
    $stmt->execute();

    // Refresh session variables with updated user data
    $result = $connection->query("SELECT * FROM users WHERE email = '$newEmail'");
    if ($result->num_rows > 0) {
        $updatedUser = $result->fetch_assoc();
        $_SESSION["phone"] = $updatedUser["phone"];
        $_SESSION["email"] = $updatedUser["email"];
        $_SESSION["homeaddress"] = $updatedUser["homeaddress"];
    }

    $_SESSION["successMessage"] = "Profile updated successfully";
    header("Location: account.php");
    exit();
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

    <div class="container my-5 d-flex justify-content-center">
        <div class="card col-sm-6">
            <div class="card-header bg-white py-4 fw-bold h4">
                My Profile
            </div>
            <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $firstname; ?>" placeholder="First Name" disabled>
                    </div>
                    <div class="col">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $lastname; ?>" placeholder="Last Name" disabled>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <label for="gender" class="form-label">Gender</label>
                        <input type="text" class="form-control" id="gender" name="gender" value="<?php echo $gender; ?>" placeholder="Gender" disabled>
                    </div>
                    <div class="col">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="Phone Number" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Email" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <label for="homeaddress" class="form-label">Address</label>
                        <input type="text" class="form-control" id="homeaddress" name="homeaddress" value="<?php echo $homeaddress; ?>" placeholder="Home Address" required>
                    </div>
                </div>
                <div class="row">
                        <div class="col d-grid gap-2">
                        <?php
                            if (!empty($successMessage)) {
                                echo "<p class='text-danger'>$successMessage</p>";
                            }
                        ?>
                            <button type="submit" class="btn btn-dark mt-3 fw-bold">Save</button>
                            <a href="changepass.php" class="btn btn-warning fw-bold">Change Password</a>
                        </div>
                    </div>
            </div>
            </form>
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

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php 
                    if (isset($_SESSION['successMessage'])) {
                        echo $_SESSION['successMessage'];
                        unset($_SESSION['successMessage']); // Clear the message after displaying
                    }
                    ?>
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
        document.addEventListener('DOMContentLoaded', function () {
            var successToast = document.getElementById('successToast');
            if (successToast) {
                var toast = new bootstrap.Toast(successToast);
                toast.show();
            }
        });
    </script>

</body>
</html>