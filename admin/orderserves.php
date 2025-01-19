<?php

require("../server/connection.php");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

function generateReceipt($orderid) {
    global $connection;

    // Query for order and customer details
    $orderQuery = "SELECT orders.orderid, orders.userid, orders.total_amount, orders.paid_amount, 
                    orders.change_amount, orders.ordercreated, orders.staffid, 
                    users.firstname, users.lastname
               FROM orders 
               JOIN users ON orders.userid = users.userid
               WHERE orders.orderid = $orderid";
    $orderResult = $connection->query($orderQuery);

    // Check if the order query is successful
    if ($orderResult && $orderResult->num_rows > 0) {
        $orderData = $orderResult->fetch_assoc();
    } else {
        error_log("No order data found for orderid: " . $orderid);
        return null; // Early return if no data is found
    }

    // Query for order details
    $detailsQuery = "SELECT order_details.quantity, order_details.price, menu.menuitem
                 FROM order_details 
                 JOIN menu ON order_details.menuid = menu.menuid
                 WHERE order_details.orderid = $orderid";
    $detailsResult = $connection->query($detailsQuery);

    // Check if the details query is successful
    if (!$detailsResult) {
        error_log("Details query failed: " . $connection->error);
        return null; // Early return if query fails
    }

    $orderDetails = [];
    if ($detailsResult && $detailsResult->num_rows > 0) {
        while ($row = $detailsResult->fetch_assoc()) {
            $orderDetails[] = $row;
        }
    } else {
        error_log("No order details found for orderid: " . $orderid);
    }

    // Construct receipt in a structured array format
    $receiptData = [
        'orderid' => $orderData['orderid'] ?? '',
        'userid' => $orderData['userid'] ?? '',
        'customerName' => ($orderData['firstname'] ?? '') . ' ' . ($orderData['lastname'] ?? ''),
        'orderCreated' => $orderData['ordercreated'] ?? '',
        'staffId' => $orderData['staffid'] ?? '',
        'orderDetails' => $orderDetails,
        'totalAmount' => $orderData['total_amount'] ?? 0,
        'amountPaid' => $orderData['paid_amount'],
        'changeAmount' => $orderData['change_amount']
    ];

    return $receiptData;
}

// Ensure we only return JSON
ob_clean(); // Clear the output buffer to prevent unwanted HTML output

if (isset($_POST['orderid'])) {
    $orderid = $connection->real_escape_string($_POST['orderid']);
    error_log("Received orderid: " . $orderid); // Debugging

    // Update order status
    $updateQuery = "UPDATE orders SET orderstatus = 'Currently Serving' WHERE orderid = $orderid";
    if (!$connection->query($updateQuery)) {
        error_log("Failed to update order status: " . $connection->error);
    }

    // Generate receipt data
    $receiptData = generateReceipt($orderid);

    // Return response with receipt data
    if ($receiptData) {
        error_log("Returning receipt data: " . print_r($receiptData, true)); // Log receipt data

        echo json_encode(['success' => true, 'receipt' => $receiptData]);
    } else {
        error_log("Receipt generation failed"); // Log failure
        echo json_encode(['success' => false, 'error' => 'Receipt generation failed.']);
    }
} else {
    error_log("Order ID not provided"); // Debugging
}