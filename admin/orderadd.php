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

// Before using $orderid, make sure it is initialized
if (isset($_GET['orderid'])) {
    $orderid = $_GET['orderid'];
} elseif (isset($_SESSION['orderid'])) {
    $orderid = $_SESSION['orderid'];
} else {
    // Handle case where orderid is not available
    $orderid = null; // or redirect to a relevant page if necessary
}


// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Set as an empty array
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $menuid = $_POST['menuid'];   // Get the menuid
        $menuitem = $_POST['menuitem'];
        $quantity = (int)$_POST['quantity'];

        // Query the menu table to get the price
        $query = "SELECT price FROM menu WHERE menuid = $menuid";
        $result = $connection->query($query);
        $menu = $result->fetch_assoc();

        if ($menu) {
            // Assign the price from the database
            $price = $menu['price'];

            // Add the item to the cart with menuid included
            $_SESSION['cart'][] = [
                'menuid' => $menuid,
                'menuitem' => $menuitem,  // Name or description of the item
                'price' => $price,  // Price of the item
                'quantity' => $quantity  // Quantity of the item
            ];
        }
    }

    // Handle Update Quantity
    if (isset($_POST['update_cart'])) {
        $menuid = $_POST['menuid'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity > 0) {
            $_SESSION['cart'][$menuid]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$menuid]);
        }
    }

    // Handle Remove Item
    if (isset($_POST['remove_item'])) {
        $menuid = $_POST['menuid'];
        unset($_SESSION['cart'][$menuid]);

        // Clear cart if empty
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
    }
}


$totalAmount = 0;
if (!empty($_SESSION['cart'])) {
    $totalAmount = array_reduce($_SESSION['cart'], function ($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tapsihan ni Kuya Rice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    #floatingButton {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    /* When scrolled down, show down arrow */
    .scrolled #floatingButton .bi-arrow-down-short {
        display: inline-block;
    }

    /* Optional: Hide cart icon when scrolling down */
    .scrolled #floatingButton .bi-cart {
        display: none;
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
                            <a class="nav-link" href="transactions.php"><i class="bi bi-clipboard2 me-2"></i>Order Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php"><i class="bi bi-box-seam me-2"></i>Inventory</a>
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

    <div class="container mt-5 pt-5">

            <div class="row">
                <div class="col-sm-10">
                    <h3 class="mt-5" id="cartSection">Cart</h3>
                </div>
                <div class="col-sm-2">
                    <h4 class="mt-5" id="cartSection">Order # 
                        <?php 
                            // Check if orderid is set in the URL and display it
                            if (isset($_GET['orderid'])) {
                                echo $_GET['orderid'];
                            } else {
                                echo 'Not Available';
                            }
                        ?>
                    </h4>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $menuid => $item) {
                                echo '
                                <tr>
                                    <td>' . htmlspecialchars($item['menuitem']) . '</td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="menuid" value="' . $menuid . '">
                                            <input type="number" name="quantity" value="' . $item['quantity'] . '" class="form-control" min="1" style="width: 60px;">
                                            <button type="submit" name="update_cart" class="btn btn-warning btn-sm mt-2">Update</button>
                                        </form>
                                    </td>
                                    <td>₱' . number_format($item['price'], 2) . '</td>
                                    <td>₱' . number_format($item['price'] * $item['quantity'], 2) . '</td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="menuid" value="' . $menuid . '">
                                            <button type="submit" name="remove_item" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center">Your cart is empty.</td></tr>';
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                            <td><strong>₱<?php echo number_format($totalAmount, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <form action="ordercheckout.php" method="GET">
                    <input type="hidden" name="orderid" value="<?php echo htmlspecialchars($orderid); ?>">
                    <button type="submit" class="btn btn-success mt-3">Proceed to Checkout</button>
                </form>
            </div>

        <hr>

        <h2 class="mb-4 mt-5">Menu</h2>
        <div class="row">
            <?php
            $query = "SELECT * FROM menu WHERE available = 'Available'";
            $result = $connection->query($query);

            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="col-md-4 mb-4">
                    <div class="card d-flex flex-column h-100">
                        <div class="card-body">
                            <h5 class="card-title">'.$row['menuitem'].'</h5>
                            <p class="card-text">'.$row['descrip'].'</p>
                            <p class="card-text">Price: ₱'.$row['price'].'</p>
                            <form method="POST" action="orderadd.php">
                                <input type="hidden" name="menuid" value="'.$row['menuid'].'">
                                <input type="hidden" name="menuitem" value="'.$row['menuitem'].'">
                                <div class="mb-3">
                                    <label for="quantity'.$row['menuid'].'" class="form-label">Quantity</label>
                                    <input type="number" name="quantity" id="quantity'.$row['menuid'].'" class="form-control" min="1" value="1">
                                </div>
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>

    <!-- Floating Button -->
    <button id="floatingButton" class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-3" style="width: 60px; height: 60px; background-color: black; border: none; display: flex; justify-content: center; align-items: center;">
        <div class="position-relative">
            <i class="bi bi-cart fs-3"></i> <!-- Cart icon -->
        </div>
    </button>

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

    <script>
        window.addEventListener("scroll", function() {
            const button = document.getElementById("floatingButton");
            if (window.scrollY > 100) {  // Adjust this value based on when you want the icon to change
                button.classList.add("scrolled");
            } else {
                button.classList.remove("scrolled");
            }
        });

        document.getElementById("floatingButton").addEventListener("click", function() {
            document.getElementById("cartSection").scrollIntoView({ behavior: "smooth" });
        });

        function Checkout(orderid) {
            if (orderid) {
                window.location.href = "ordercheckout.php?orderid=" + encodeURIComponent(orderid);
            } else {
                alert("Order ID is missing!");
            }
        }
    </script>

</body>
</html>
