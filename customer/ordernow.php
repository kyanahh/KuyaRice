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

$currentDateTime = date("Y-m-d H:i:s");

if (!isset($_SESSION['orderid'])) {
    // Check if there's an existing order for the user in the database
    $checkOrderQuery = $connection->prepare("SELECT orderid FROM orders WHERE userid = ? AND orderstatus = 'Confirmed' ORDER BY ordercreated DESC LIMIT 1");
    $checkOrderQuery->bind_param("i", $textaccount);
    $checkOrderQuery->execute();
    $result = $checkOrderQuery->get_result();
    $existingOrder = $result->fetch_assoc();

    if ($existingOrder) {
        $_SESSION['orderid'] = $existingOrder['orderid']; // Use the existing order
    } else {
        // No existing order; create a new one
        $result = $connection->query("INSERT INTO orders (userid, ordercreated, orderstatus, staffid) VALUES ('$textaccount',  $currentDateTime, 'Confirmed', '$textaccount')");
        if ($result) {
            $_SESSION['orderid'] = $connection->insert_id;
        } else {
            die("Error creating order: " . $connection->error);
        }
    }
}

$orderid = $_SESSION['orderid'];


// Fetch the order details based on the orderid
if (isset($_GET['orderid'])) {
    $orderid = $_GET['orderid'];

    $order_query = $connection->prepare("SELECT * FROM orders WHERE orderid = ?");
    $order_query->bind_param("i", $orderid);
    $order_query->execute();
    $order_result = $order_query->get_result();
    $order = $order_result->fetch_assoc();

    if ($order) {
        $order_id_display = $order['orderid'];
        $order_total_amount = number_format($order['total_amount'], 2);
    } else {
        $order_id_display = 'Not Available';
        $order_total_amount = 'N/A';
    }
} else {
    $order_id_display = 'Not Available';
    $order_total_amount = 'N/A';
}
    
// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Set as an empty array
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $menuid = $_POST['menuid'];
        $menuitem = $_POST['menuitem'];
        $quantity = (int)$_POST['quantity'];

        $query = "SELECT price FROM menu WHERE menuid = $menuid";
        $result = $connection->query($query);
        $menu = $result->fetch_assoc();

        if ($menu) {
            $price = $menu['price'];

            // Check if the item is already in the cart
            if (array_key_exists($menuid, $_SESSION['cart'])) {
                // Update the quantity for the existing item
                $_SESSION['cart'][$menuid]['quantity'] += $quantity;
            } else {
                // Add a new item to the cart
                $_SESSION['cart'][$menuid] = [
                    'menuid' => $menuid,
                    'menuitem' => $menuitem,
                    'price' => $price,
                    'quantity' => $quantity,
                ];
            }

        }
    }

    // Handle Update Quantity
    if (isset($_POST['update_cart'])) {
        $menuid = $_POST['menuid'];
        $quantity = (int)$_POST['quantity'];

        foreach ($_SESSION['cart'] as &$item) {
            if ($item['menuid'] == $menuid) {
                if ($quantity > 0) {
                    $item['quantity'] = $quantity;
                } else {
                    unset($item);
                }
                break;
            }
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }

    // Handle Remove Item
    if (isset($_POST['remove_item'])) {
        $menuid = $_POST['menuid'];
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['menuid'] == $menuid) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
        
    }

     // Handle Clear Cart
     if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']); // Remove all items from the cart
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown:hover .dropdown-toggle {
            background-color: #000000;
        }
        
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
    
    <nav class="navbar navbar-expand-lg py-3 bg-black text-white">
        <div class="container-fluid">
            <a href="customerhome.php">
                <img src="../img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
            </a>          
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

      <div class="container">

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
                                                <input type="hidden" name="menuid" value="' . htmlspecialchars($item['menuid']) . '">
                                                <input type="number" name="quantity" value="' . $item['quantity'] . '" class="form-control" min="1" style="width: 60px;">
                                                <button type="submit" name="update_cart" class="btn btn-warning btn-sm mt-2">Update</button>
                                            </form>
                                        </td>
                                        <td>₱' . number_format($item['price'], 2) . '</td>
                                        <td>₱' . number_format($item['price'] * $item['quantity'], 2) . '</td>
                                        <td>
                                            <form method="POST" action="">
                                                <input type="hidden" name="menuid" value="' . htmlspecialchars($item['menuid']) . '">
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

                        <div class="d-flex justify-content-end">
                            <!-- Clear Cart Button -->
                            <form method="POST" action="">
                                <button type="submit" name="clear_cart" class="btn btn-danger">Clear Cart</button>
                            </form>
                        </div>
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
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php
            $query = "SELECT menu.*, IFNULL(SUM(inventory.quantity), 0) AS available_quantity 
                    FROM menu
                    LEFT JOIN inventory ON menu.menuid = inventory.menuid
                    GROUP BY menu.menuid";
            $result = $connection->query($query);

            while ($row = $result->fetch_assoc()) {
                $available_quantity = $row['available_quantity']; // Get available quantity
                $is_available = $available_quantity > 0; // Check if available quantity is greater than 0
                $button_class = $is_available ? 'btn-primary' : 'btn-danger'; // Set button color based on availability
                $button_text = $is_available ? 'Add to Cart' : 'Not Available'; // Set button text based on availability
                $disabled = !$is_available ? 'disabled' : ''; // Disable the button if not available

                echo '
                <div class="col">
                    <div class="card h-100">
                        <img src="data:image/jpeg;base64,' . base64_encode($row['menupic']) . '" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($row['menuitem']) . '</h5>
                            <p class="card-text">' . htmlspecialchars($row['descrip']) . '</p>
                            <p class="card-text">Price: ₱' . number_format($row['price'], 2) . '</p>
                            <form method="POST" action="">
                                <input type="hidden" name="menuid" value="' . htmlspecialchars($row['menuid'], ENT_QUOTES, 'UTF-8') . '">
                                <input type="hidden" name="menuitem" value="' . htmlspecialchars($row['menuitem'], ENT_QUOTES, 'UTF-8') . '">
                                <div class="mb-3">
                                    <label for="quantity' . htmlspecialchars($row['menuid'], ENT_QUOTES, 'UTF-8') . '" class="form-label">Quantity</label>
                                    <input type="number" name="quantity" id="quantity' . htmlspecialchars($row['menuid'], ENT_QUOTES, 'UTF-8') . '" class="form-control" min="1" value="1">
                                </div>
                                <button type="submit" name="add_to_cart" class="btn ' . $button_class . '" ' . $disabled . '>' . $button_text . '</button>
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