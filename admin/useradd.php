<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["userid"])){
        $textaccount = $_SESSION["userid"];
        $fname = $_SESSION["firstname"];
        $lname = $_SESSION["lastname"];
        $useremail = $_SESSION["email"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

$firstname = $lastname = $phone = $gender = $email = $homeaddress =  $usertype = $password = $errorMessage = $successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname =  ucwords($_POST["firstname"]);
    $lastname =  ucwords($_POST["lastname"]);
    $homeaddress =  ucwords($_POST["homeaddress"]);
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $usertype = $_POST["usertype"];

    
    // Check if the email already exists in the database
    $emailExistsQuery = "SELECT * FROM users WHERE email = '$email'";
    $emailExistsResult = $connection->query($emailExistsQuery);

    if ($emailExistsResult->num_rows > 0) {
        $errorMessage = "User already exists";
    } else {
        $insertQuery = "INSERT INTO users (firstname, lastname, gender, phone, homeaddress, email, password, usertype) 
                        VALUES ('$firstname', '$lastname', '$gender', '$phone', '$homeaddress', '$email', '$password', '$usertype')";
        $result = $connection->query($insertQuery);

        if (!$result) {
            $errorMessage = "Invalid query " . $connection->error;
        } else {
            $_SESSION['toast_message'] = "Account successfully created";
            header("Location: users.php");
            exit();
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
<body>

    <nav class="navbar navbar-dark bg-black py-3">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBlackNavbar" aria-controls="offcanvasBlackNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <img src="../img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
            </div>
            <div class="offcanvas offcanvas-start bg-black text-white" tabindex="-1" id="offcanvasBlackNavbar" aria-labelledby="offcanvasBlackNavbarLabel">
                <div class="offcanvas-header">
                    <img src="../img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="adminhome.php"><i class="bi bi-bar-chart me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php"><i class="bi bi-people me-2"></i>Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="menu.php"><i class="bi bi-book me-2"></i>Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php"><i class="bi bi-cart-check me-2"></i>Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="userlogs.php"><i class="bi bi-person-lines-fill"></i>User Logs</a>
                        </li>
                    </ul>
                    <div class="dropup py-sm-4 py-1 mt-sm-auto ms-auto ms-sm-0 flex-shrink-1">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../img/nopf.jpg" alt="hugenerd" width="28" height="28" class="rounded-circle">
                            <span class="d-none d-sm-inline mx-2"><?php echo $fname . " " . $lname ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark px-0 px-sm-2 text-center text-sm-start" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item px-1" href="profile.php"><i class="fs-6 bi-person"></i><span class="d-none d-sm-inline ps-1">Profile</span></a></li>
                            <li><a class="dropdown-item px-1" href="settings.php"><i class="fs-6 bi-gear"></i><span class="d-none d-sm-inline ps-1">Settings</span></a></li>
                            <li><a class="dropdown-item px-1" href="../logout.php"><i class="fs-6 bi-power"></i><span class="d-none d-sm-inline ps-1">Logout</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN -->
    <div class="container my-4 pt-3">
        <div class="card shadow p-3">
            <h4 class="text-center mb-3 fw-bold">Add New User</h4>
            <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label small">First Name</label>
                        <input type="text" class="form-control form-control-sm" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label small">Last Name</label>
                        <input type="text" class="form-control form-control-sm" id="lastname" name="lastname" value="<?php echo $lastname; ?>" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="homeaddress" class="form-label small">Home Address</label>
                    <input type="text" class="form-control form-control-sm" id="homeaddress" name="homeaddress" value="<?php echo $homeaddress; ?>" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label for="phone" class="form-label small">Phone</label>
                        <input type="tel" class="form-control form-control-sm" id="phone" name="phone" value="<?php echo $phone; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="gender" class="form-label small">Gender</label>
                        <select class="form-select form-select-sm" id="gender" name="gender" required>
                            <option selected disabled>Select an option</option>
                            <option value="Male" <?php echo ($gender === "Male") ? "selected" : ""; ?>>Male</option>
                            <option value="Female" <?php echo ($gender === "Female") ? "selected" : ""; ?>>Female</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label for="email" class="form-label small">Email</label>
                        <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label small">Password</label>
                        <input type="password" class="form-control form-control-sm" id="password" name="password" value="<?php echo $password; ?>" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="usertype" class="form-label small">User Type</label>
                    <select class="form-select form-select-sm" id="usertype" name="usertype" required>
                        <option selected disabled>Select an option</option>
                        <option value="Admin" <?php echo ($usertype === "Admin") ? "selected" : ""; ?>>Admin</option>
                        <option value="Staff" <?php echo ($usertype === "Staff") ? "selected" : ""; ?>>Staff</option>
                        <option value="Customer" <?php echo ($usertype === "Customer") ? "selected" : ""; ?>>Customer</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-dark btn-sm px-5 py-2">Add User</button>
                    <a href="users.php" class="btn btn-danger btn-sm px-5 py-2">Cancel</a>
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

</body>
</html>
