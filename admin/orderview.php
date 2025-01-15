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

$orderid = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;

?>

<?php if (isset($_SESSION['toast_message'])): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <?php echo $_SESSION['toast_message']; ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['toast_message']); endif; ?>


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
    <div class="container my-5 pt-4 mt-5">
        <div class="card shadow-lg p-4 mt-5" style="height:400px;">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fs-5 m-0">Order Details</h2>
                <div class="d-flex">
                    <a href="orders.php" class="btn btn-dark text-white ms-3"><i class="bi bi-arrow-left"></i></a>
                </div>
            </div>

            <!-- Order Detail Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table id="menu-table" class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Menu Item</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php
                                    // Query the database to fetch user data
                                    $result = $connection->query("SELECT order_details.*, menu.menuitem 
                                    FROM order_details 
                                    LEFT JOIN menu 
                                    ON order_details.menuid = menu.menuid 
                                    WHERE order_details.orderid = $orderid");
                                    if ($result->num_rows > 0) {
                                        $count = 1; 
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>' . $count . '</td>';
                                            echo '<td>' . $row['orderid'] . '</td>';
                                            echo '<td>' . $row['menuitem'] . '</td>';
                                            echo '<td>' . $row['quantity'] . '</td>';
                                            echo '<td>' . $row['price'] . '</td>';
                                            echo '<td>' . $row['total_amount'] . '</td>';
                                            echo '</tr>';
                                            $count++; 
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center">No order detail found.</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Order Detail Table -->

            <!-- Search Results -->
            <div id="search-results" class="mt-4"></div>
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

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container">
        <div id="deleteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Order Detail deleted successfully.
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this order detail?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container">
        <div id="updateToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Order Detail updated successfully.
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
            // Check if the session has the update success flag set
            <?php if (isset($_SESSION['update_success'])): ?>
                var updateToast = new bootstrap.Toast(document.getElementById('updateToast'));
                updateToast.show();
                <?php unset($_SESSION['update_success']); // Clear the session variable after showing the toast ?>
            <?php endif; ?>
        });
    </script>

    <script>
        const toastLive = document.getElementById('liveToast');
        if (toastLive) {
            const toast = new bootstrap.Toast(toastLive);
            toast.show();
        }
    </script>

</body>
</html>