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

$currentpass = $newpass = $confirmpass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentpass = $_POST["currentpass"];
    $newpass = $_POST["newpass"];
    $confirmpass = $_POST["confirmpass"];

    $result = $connection->query("SELECT password FROM users WHERE userid = '$textaccount'");
    $record = $result->fetch_assoc();
    $stored_password = $record["password"];

    if ($currentpass === $stored_password) {
        if ($newpass === $confirmpass) {
            $updatePasswordQuery = "UPDATE users SET password = ? WHERE userid = ?";
            $stmt = $connection->prepare($updatePasswordQuery);
            $stmt->bind_param("ss", $newpass, $textaccount);
            $stmt->execute();

            $successMessage = "Password successfully changed.";
            $toastType = "success";
            $currentpass = $newpass = $confirmpass = "";
        } else {
            $successMessage = "New password and confirmation do not match.";
            $toastType = "danger";
            $currentpass = $newpass = $confirmpass = "";
        }
    } else {
        $successMessage = "Old password does not match.";
        $toastType = "danger";
        $currentpass = $newpass = $confirmpass = "";
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

    <nav class="navbar navbar-dark bg-black py-3 fixed-top">
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
                            <a class="nav-link" href="userlogs.php"><i class="bi bi-person-lines-fill me-2"></i>User Logs</a>
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
    <div class="container my-5 d-flex justify-content-center pt-5">
        <div class="card col-sm-6 shadow mt-4">
            <div class="card-header bg-white py-4 fw-bold h4">
                Change Password
            </div>
            <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <input type="password" class="form-control" id="currentpass" name="currentpass" value="<?php echo $currentpass; ?>" placeholder="Current Password" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <p>Please enter your new password below.</p>
                    <div class="col">
                        <input type="password" class="form-control" id="newpass" name="newpass" value="<?php echo $newpass; ?>" placeholder="New Password" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <input type="password" class="form-control" id="confirmpass" name="confirmpass" value="<?php echo $confirmpass; ?>" placeholder="Confirm Password" required>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password
                    </div>
                </div>
                <div class="row">
                        <div class="col d-grid gap-2">
                            <button type="submit" class="btn btn-dark mt-3 fw-bold">Save</button>
                            <a href="profile.php" class="btn btn-danger fw-bold">Back to Profile</a>
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

    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="successToast" class="toast bg-<?php echo $toastType; ?> text-white" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-white">
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?php echo $successMessage ?? ""; ?>
                </div>
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
            var passwordField = document.getElementById("currentpass");
            var newPassword = document.getElementById("newpass");
            var confirmPassword = document.getElementById("confirmpass");

            // Check the type of any field and toggle all
            var newType = passwordField.type === "password" ? "text" : "password";

            passwordField.type = newType;
            newPassword.type = newType;
            confirmPassword.type = newType;
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toastElement = document.getElementById("successToast");
            if (toastElement.querySelector(".toast-body").textContent.trim() !== "") {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        });
    </script>

</body>
</html>
