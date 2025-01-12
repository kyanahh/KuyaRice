<?php
session_start();
require("../server/connection.php");

// Handle session-based user information
if (isset($_SESSION["logged_in"])) {
    if (isset($_SESSION["userid"])) {
        $textaccount = $_SESSION["userid"];
        $fname = $_SESSION["firstname"];
        $lname = $_SESSION["lastname"];
        $useremail = $_SESSION["email"];
    } else {
        $textaccount = "Account";
    }
} else {
    $textaccount = "Account";
}

if (isset($_GET['orderid'])) {
    $orderid = $_GET['orderid'];
} else {
    echo "<script>alert('Order ID is missing!'); window.location.href='orders.php';</script>";
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
    $orderid = $_POST['orderid'];
    $paid_amount = floatval($_POST['paid_amount']);
    $payment_method = $_POST['payment_method'];

    // Using totalAmount from the cart, since it should represent the accurate value
    if ($paid_amount >= $totalAmount) {
        $change_amount = $paid_amount - $totalAmount;
        $orderstatus = 'In The Kitchen';

        // Start transaction
        $connection->begin_transaction();

        try {
            // Update orders table
            $update_order = $connection->prepare("UPDATE orders SET paid_amount = ?, change_amount = ?, total_amount = ?, orderstatus = ? WHERE orderid = ?");
            $update_order->bind_param("dddsi", $paid_amount, $change_amount, $totalAmount, $orderstatus, $orderid);
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
            
                // Insert order details
                $insert_order_details = $connection->prepare("INSERT INTO order_details (orderid, menuid, quantity, price, total_amount) VALUES (?, ?, ?, ?, ?)");
                $insert_order_details->bind_param("iiidd", $orderid, $menuid, $quantity, $price, $total_item_amount);
                $insert_order_details->execute();
            }            

            // Commit transaction
            $connection->commit();
            echo "<script>alert('Order placed successfully!'); window.location.href='orders.php';</script>";
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $connection->rollback();
            echo "<script>alert('Error updating order: " . $e->getMessage() . "'); window.location.href='orders.php';</script>";
        }
    } else {
        echo "<script>alert('Insufficient payment!');</script>";
    }
}

$cartItems = $_SESSION['cart'];


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
        #gcash_qr {
            display: none;
        }
    </style>
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
                            <a class="nav-link" href="transactions.php"><i class="bi bi-clipboard2 me-2"></i>Transactions</a>
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

    <!-- MAIN -->
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

                <!-- Payment Section -->
                <h5>Payment</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="paid_amount" class="form-label">Paid Amount</label>
                        <input type="number" class="form-control" id="paid_amount" name="paid_amount" value="<?php echo $paid_amount ?>" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="change_amount" class="form-label">Change</label>
                        <input type="text" class="form-control" id="change_amount" name="change_amount" readonly>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>

                    <!-- GCash QR Code -->
                    <div id="gcash_qr" class="mb-3" style="display: none;">
                        <label class="form-label">GCash QR Code</label>
                        <img src="path/to/gcash_qr_code.png" alt="GCash QR Code" class="img-fluid">
                    </div>

                    <input type="hidden" name="orderid" value="<?php echo $orderid; ?>">
                    <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
                </form>
            </div>

            <div class="col-md-4">
                <h5>Payment Summary</h5>
                <p><strong>Total:</strong> ₱<?php echo number_format($totalAmount, 2); ?></p>
                <p><strong>Paid:</strong> ₱<span id="paid_display">0.00</span></p>
                <p><strong>Change:</strong> ₱<span id="change_display">0.00</span></p>
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
        // Live computation for change
        document.getElementById('paid_amount').addEventListener('input', function() {
            let totalAmount = <?php echo $totalAmount; ?>;
            let paidAmount = parseFloat(this.value) || 0;
            let changeAmount = paidAmount >= totalAmount ? paidAmount - totalAmount : 0;

            document.getElementById('change_amount').value = changeAmount.toFixed(2);
            document.getElementById('change_display').textContent = changeAmount.toFixed(2);
            document.getElementById('paid_display').textContent = paidAmount.toFixed(2);
        });

        // Show GCash QR code if selected
        document.getElementById('payment_method').addEventListener('change', function() {
            if (this.value === 'gcash') {
                document.getElementById('gcash_qr').style.display = 'block';
            } else {
                document.getElementById('gcash_qr').style.display = 'none';
            }
        });
    </script>

</body>
</html>
