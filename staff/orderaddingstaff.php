<?php
require("../server/connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['userid'])) {
    $userid = $_POST['userid'];
    $staffid = $_POST['staffid'];
    $orderstatus = "Confirmed";
    $currentDateTime = date("Y-m-d H:i:s");

    // Insert order into the database
    $stmt = $connection->prepare("INSERT INTO orders (userid, orderstatus, ordercreated, staffid) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $userid, $orderstatus, $currentDateTime, $staffid);

    if ($stmt->execute()) {
        // Get the latest orderid for the current user
        $latest_order_query = $connection->prepare("SELECT orderid FROM orders WHERE userid = ? ORDER BY ordercreated DESC LIMIT 1");
        $latest_order_query->bind_param("i", $userid);
        $latest_order_query->execute();
        $latest_order_result = $latest_order_query->get_result();
        $latest_order = $latest_order_result->fetch_assoc();
        $orderid = $latest_order['orderid']; // Get the latest orderid

        // Return success message with the orderid for redirection
        echo json_encode([
            'success' => true, 
            'message' => 'Order created successfully.',
            'orderid' => $orderid
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add order: ' . $connection->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing User ID.']);
}
?>