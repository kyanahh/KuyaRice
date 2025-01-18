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

// Check if orderid is set in the URL, if yes, update the session
if (isset($_GET['orderid'])) {
    $orderid = $_GET['orderid'];
    $_SESSION['orderid'] = $orderid;
} elseif (isset($_SESSION['orderid'])) {
    // If not, use the orderid from the session
    $orderid = $_SESSION['orderid'];
} else {
    // Handle the case where no orderid is provided or found
    $_SESSION['error_message'] = 'Order ID is missing!';
    header('Location: orders.php');
    exit;
}

$totalAmount = 0;

if (!empty($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
    $totalAmount = array_reduce($cartItems, function ($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    foreach ($cartItems as $item) {
            $menuid = $item['menuid'];
            $quantity = $item['quantity'];

            // Check available stock for each item
            $inventory_query = $connection->prepare("SELECT SUM(quantity) AS stock FROM inventory WHERE menuid = ?");
            $inventory_query->bind_param("i", $menuid);
            $inventory_query->execute();
            $inventory_result = $inventory_query->get_result();

            if ($inventory_result->num_rows > 0) {
                $row = $inventory_result->fetch_assoc();
                $available_stock = $row['stock'] ?? 0;

                // If stock is insufficient, set an error and redirect
                if ($available_stock < $quantity) {
                    $_SESSION['error_message'] = "Product '{$item['menuitem']}' is out of stock or has insufficient stock.";
                    header('Location: orders.php');
                    exit;
                }
            } else {
                $_SESSION['error_message'] = "Failed to check inventory for '{$item['menuitem']}'.";
                header('Location: orders.php');
                exit;
            }
        }

        // Start transaction
        $connection->begin_transaction();

        try {
            // Update orders table
            $update_order = $connection->prepare("UPDATE orders SET total_amount = ? WHERE orderid = ?");
            $update_order->bind_param("di", $totalAmount, $orderid);
            $update_order->execute();

            // Insert order details
            foreach ($cartItems as $item) {
                // Ensure menuid is set
                if (!isset($item['menuid']) || empty($item['menuid'])) {
                    throw new Exception('Menu ID is missing for one or more items!');
                }

                $menuid = $item['menuid'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $total_item_amount = $price * $quantity;
                $transaction_date = date("Y-m-d H:i:s");
                $staffid = intval($textaccount); // Convert staff ID to an integer
                
                // Store the negative quantity in a variable
                $negative_quantity = -$quantity;

                // Insert order details
                $insert_order_details = $connection->prepare("INSERT INTO order_details (orderid, menuid, quantity, price, total_amount) VALUES (?, ?, ?, ?, ?)");
                $insert_order_details->bind_param("iiidd", $orderid, $menuid, $quantity, $price, $total_item_amount);
                $insert_order_details->execute();

                // Insert inventory
                $insert_inventory = $connection->prepare("INSERT INTO inventory (menuid, quantity, transaction_date, staffid) VALUES (?, ?, ?, ?)");
                $insert_inventory->bind_param("iisi", $menuid, $negative_quantity, $transaction_date, $staffid);
                $insert_inventory->execute();
            }            

            // Commit transaction
            if ($connection->commit()) {
                unset($_SESSION['orderid']); // Clear the order ID from the session
                $_SESSION['success_message'] = 'Order placed successfully!';
                header('Location: orders.php');
                exit;
            }                        
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $connection->rollback();
            $_SESSION['error_message'] = 'Error updating order: ' . $e->getMessage();
            header('Location: orders.php'); // Redirect to orders page with error message
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

      <!-- MAIN -->
    <div class="container mt-5">
        <h3 class="mb-4">Checkout Confirmation</h3>

        <div class="row">
            <div class="col-md-8">
                <h4>Order #<?php echo $orderid; ?></h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cartItems)): ?>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['menuitem']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No items found for this order.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h5>Order Summary</h5>
                <p><strong>Total Amount:</strong> ₱<?php echo number_format($totalAmount, 2); ?></p>

                <form method="POST">
                    <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
                </form>
            </div>

            <div class="col-md-4">
                <h5>Payment Summary</h5>
                <p><strong>Total:</strong> ₱<?php echo number_format($totalAmount, 2); ?></p>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <?php if (isset($_SESSION['error_message'])): ?>
            <div id="errorToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto text-danger">Error</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?php echo $_SESSION['error_message']; ?>
                </div>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
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

    <script>
        $(document).ready(function () {
            var toastElement = document.getElementById('errorToast');
            if (toastElement) {
                setTimeout(function () {
                    var toast = new bootstrap.Toast(toastElement);
                    toast.hide();
                }, 3000); // Toast hides after 3 seconds
            }
        });
    </script>

</body>
</html>