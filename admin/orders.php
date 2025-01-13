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
    <div class="container my-5 pt-4 mt-5">
        <div class="card shadow-lg p-4 mt-5" style="height:400px;">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fs-5 m-0">Orders</h2>
                <div class="d-flex">
                    <input type="text" class="form-control me-2" id="searchOrderInput" placeholder="Search" aria-label="Search" oninput="searchOrder()">
                </div>
            </div>

            <!-- Order Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table id="order-table" class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th scope="col">Order #</th>
                                    <th scope="col">User ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Order Status</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Order Created</th>
                                    <th scope="col">Staff ID</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php
                                    // Query the database to fetch user data
                                    $result = $connection->query("SELECT orders.*, users.firstname, users.lastname 
                                    FROM orders INNER JOIN users 
                                    ON orders.userid = users.userid ORDER BY orderid DESC");

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>' . $row['orderid'] . '</td>';
                                            echo '<td>' . $row['userid'] . '</td>';
                                            echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
                                            echo '<td>' . $row['orderstatus'] . '</td>';
                                            echo '<td>' . $row['total_amount'] . '</td>';
                                            echo '<td>' . $row['ordercreated'] . '</td>';
                                            echo '<td>' . $row['staffid'] . '</td>';
                                            echo '<td>';
                                            echo '<div class="d-flex justify-content-center gap-2">';

                                            // Pending
                                            if ($row['orderstatus'] == 'Pending') {
                                                echo '<button class="btn btn-sm btn-success" onclick="openConfirmModal(' . $row['orderid'] . ')">Confirm</button>';
                                                echo '<button class="btn btn-sm btn-danger" onclick="cancelOrder(' . $row['orderid'] . ')">Cancel</button>';

                                            }
                                            
                                            // Confirmed
                                            if ($row['orderstatus'] == 'Confirmed') {
                                                echo '<button class="btn btn-sm btn-info" onclick="addOrder(' . $row['orderid'] . ')">Add Order</button>';
                                            }

                                            // In The Kitchen
                                            if ($row['orderstatus'] == 'In The Kitchen') {
                                                echo '<button class="btn btn-sm btn-success" onclick="openServeModal(' . $row['orderid'] . ')">Serve Now</button>';
                                            }

                                            // Currently Serving
                                            if ($row['orderstatus'] == 'Currently Serving') {
                                                echo '<button class="btn btn-sm btn-success" onclick="openDoneModal(' . $row['orderid'] . ')">Done</button>';
                                            }

                                            echo '<button class="btn btn-sm btn-danger" onclick="deleteOrder(' . $row['orderid'] . ')">Delete</button>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center">No order record found.</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Order Table -->

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
                Order details deleted successfully.
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
                Are you sure you want to delete this order record?
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
                Order information updated successfully.
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to confirm this order?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Modal -->
    <div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="processModalLabel">Processing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to process this order?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="processButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Serve Modal -->
    <div class="modal fade" id="serveModal" tabindex="-1" aria-labelledby="serveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serveModalLabel">Order Completed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to serve this order?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="serveButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Done Modal -->
    <div class="modal fade" id="doneModal" tabindex="-1" aria-labelledby="doneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="doneModalLabel">Order Completed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure this order is served successfully? 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="doneButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel this order?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="cancelButton">Yes, Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
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
        //---------------------------Search Order Results---------------------------//
        function searchOrder() {
            const query = document.getElementById("searchOrderInput").value;

            // Make an AJAX request to fetch search results
            $.ajax({
                url: 'search_order.php', // Replace with the actual URL to your search script
                method: 'POST',
                data: { query: query },
                success: function(data) {
                    // Update the user-table with the search results
                    $('#order-table tbody').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error during search request:", error);
                }
            });
        }

        //---------------------------Delete Order---------------------------//
        let orderIdToDelete = null;

        function deleteOrder(orderid) {
            orderIdToDelete = orderid; // Store the user ID to delete
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show(); // Show the modal
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (orderIdToDelete) {
                $.ajax({
                    url: 'delete_order.php',
                    method: 'POST',
                    data: { orderid: orderIdToDelete },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            showDeleteToast();
                            setTimeout(function () {
                                location.reload();
                            }, 3000); // Wait 3 seconds before refreshing
                        } else {
                            alert(response.error);
                        }
                    },
                    error: function () {
                        alert('Error deleting order details');
                    }
                });
            }
        });

        function showDeleteToast() {
            const deleteToast = new bootstrap.Toast(document.getElementById('deleteToast'));
            deleteToast.show();
        }

        //---------------------------Confirm Order---------------------------//
        let orderIdToConfirm = null;

        // Function to open the confirmation modal
        function openConfirmModal(orderid) {
            console.log("Opening modal for order ID:", orderid); // Debugging log
            orderIdToConfirm = orderid;
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            confirmModal.show();
        }

        // Event listener for the confirm button in the modal
        document.getElementById('confirmButton').addEventListener('click', function () {
            if (orderIdToConfirm) {
                $.ajax({
                    url: "orderconfirm.php",
                    method: "POST",
                    data: { orderId: orderIdToConfirm },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Exit the modal
                            const confirmModalElement = document.getElementById('confirmModal');
                            const confirmModal = bootstrap.Modal.getInstance(confirmModalElement);
                            confirmModal.hide();

                            // Show toast and then refresh the page
                            showToast(response.success, "bg-success", () => {
                                location.reload();
                            });
                        } else {
                            // Handle error scenario
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error confirming the order', 'bg-danger');
                    }
                });
            }
        });

        //---------------------------Cancel Order---------------------------//
        let orderIdToCancel = null;

        // Function to open the cancel modal
        function cancelOrder(orderid) {
            console.log("Opening cancel modal for order ID:", orderid); // Debugging log
            orderIdToCancel = orderid;
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            cancelModal.show();
        }

        // Event listener for the cancel button in the modal
        document.getElementById('cancelButton').addEventListener('click', function () {
            if (orderIdToCancel) {
                console.log("Cancelling order ID:", orderIdToCancel); // Debugging log

                // AJAX request to cancel the order
                $.ajax({
                    url: "ordercancel.php",
                    method: "POST",
                    data: { orderid: orderIdToCancel },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Exit the modal
                            const cancelModalElement = document.getElementById('cancelModal');
                            const cancelModal = bootstrap.Modal.getInstance(cancelModalElement);
                            cancelModal.hide();

                            // Show toast and then refresh the page
                            showToast(response.success, "bg-success", () => {
                                location.reload();
                            });
                        } else {
                            // Handle error scenario
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error cancelling the order', 'bg-danger');
                    }
                });
            }
        });

        //---------------------------Add Order---------------------------//
        function addOrder(orderid) {
            window.location = "orderadd.php?orderid=" + orderid;
        }

        //---------------------------Process Order---------------------------//
        let processIdToConfirm = null;

        // Function to open the confirmation modal
        function processNow(orderid) {
            console.log("Opening modal for order ID:", orderid); // Debugging log
            processIdToConfirm = orderid;
            const processModal = new bootstrap.Modal(document.getElementById('processModal'));
            processModal.show();
        }

        // Event listener for the confirm button in the modal
        document.getElementById('processButton').addEventListener('click', function () {
            if (processIdToConfirm) {
                $.ajax({
                    url: "orderprocess.php",
                    method: "POST",
                    data: { orderid: processIdToConfirm },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Exit the modal
                            const processModalElement = document.getElementById('processModal');
                            const processModal = bootstrap.Modal.getInstance(processModalElement);
                            processModal.hide();

                            // Show toast and then refresh the page
                            showToast(response.success, "bg-success", () => {
                                location.reload();
                            });
                        } else {
                            // Handle error scenario
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error prcoessing the order', 'bg-danger');
                    }
                });
            }
        });

        //---------------------------Serve Order---------------------------//
        let serveIdToConfirm = null;

        // Function to open the confirmation modal
        function openServeModal(orderid) {
            console.log("Opening modal for order ID:", orderid); // Debugging log
            serveIdToConfirm = orderid;
            const serveModal = new bootstrap.Modal(document.getElementById('serveModal'));
            serveModal.show();
        }

        // Event listener for the confirm button in the modal
        document.getElementById('serveButton').addEventListener('click', function () {
            if (serveIdToConfirm) {
                $.ajax({
                    url: "orderserve.php",
                    method: "POST",
                    data: { orderid: serveIdToConfirm },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Exit the modal
                            const serveModalElement = document.getElementById('serveModal');
                            const serveModal = bootstrap.Modal.getInstance(serveModalElement);
                            serveModal.hide();

                            // Show toast and then refresh the page
                            showToast(response.success, "bg-success", () => {
                                location.reload();
                            });
                        } else {
                            // Handle error scenario
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error serving the order', 'bg-danger');
                    }
                });
            }
        });

         //---------------------------Done Order---------------------------//
         let doneIdToConfirm = null;

        // Function to open the confirmation modal
        function openDoneModal(orderid) {
            console.log("Opening modal for order ID:", orderid); // Debugging log
            doneIdToConfirm = orderid;
            const doneModal = new bootstrap.Modal(document.getElementById('doneModal'));
            doneModal.show();
        }

        // Event listener for the confirm button in the modal
        document.getElementById('doneButton').addEventListener('click', function () {
            if (doneIdToConfirm) {
                $.ajax({
                    url: "orderdone.php",
                    method: "POST",
                    data: { orderid: doneIdToConfirm },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Exit the modal
                            const doneModalElement = document.getElementById('doneModal');
                            const doneModal = bootstrap.Modal.getInstance(doneModalElement);
                            doneModal.hide();

                            // Show toast and then refresh the page
                            showToast(response.success, "bg-success", () => {
                                location.reload();
                            });
                        } else {
                            // Handle error scenario
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error finalizing the order', 'bg-danger');
                    }
                });
            }
        });

        // Function to display a toast and optionally call a callback after it disappears
        function showToast(message, bgClass, callback = null) {
            const toastElement = document.getElementById('liveToast');
            if (toastElement) {
                console.log('Showing toast with message:', message); // Debugging log
                const toastBody = toastElement.querySelector('.toast-body');
                toastBody.textContent = message;

                // Set the background class dynamically
                toastElement.className = `toast align-items-center ${bgClass} border-0`;

                // Initialize and show the toast
                const toast = new bootstrap.Toast(toastElement);
                toast.show();

                // Set a timeout to trigger the callback after the toast is shown for 2 seconds
                setTimeout(() => {
                    if (callback) callback();
                }, 2000);
            } else {
                console.error('Toast element not found');
            }
        }

    </script>

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

</body>
</html>