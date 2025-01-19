<?php
require("../server/connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'];
    $staffid = $_POST['staffid'];
    $guestFirstName = isset($_POST['guestFirstName']) ? $_POST['guestFirstName'] : '';
    $orderstatus = "Confirmed";
    $currentDateTime = date("Y-m-d H:i:s");

    if (!empty($guestFirstName)) {
        // Handle guest order and insert into the correct 'guest_name' column
        $stmt = $connection->prepare("INSERT INTO orders (guest_name, orderstatus, ordercreated, staffid) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $guestFirstName, $orderstatus, $currentDateTime, $staffid);
    } else {
        // Handle regular user order
        $stmt = $connection->prepare("INSERT INTO orders (userid, orderstatus, ordercreated, staffid) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $userid, $orderstatus, $currentDateTime, $staffid);
    }

    if ($stmt->execute()) {
        $latest_order_query = $connection->prepare("SELECT orderid FROM orders WHERE userid = ? ORDER BY ordercreated DESC LIMIT 1");
        $latest_order_query->bind_param("i", $userid);
        $latest_order_query->execute();
        $latest_order_result = $latest_order_query->get_result();
        $latest_order = $latest_order_result->fetch_assoc();
        $orderid = $latest_order['orderid'];

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
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?> 