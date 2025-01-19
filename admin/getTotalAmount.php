<?php
require("../server/connection.php");

if (isset($_POST['orderid'])) {
    $orderid = $_POST['orderid'];

    // Sanitize input
    $orderid = $connection->real_escape_string($orderid);

    // Fetch the total_amount
    $query = "SELECT total_amount FROM orders WHERE orderid = '$orderid'";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'total_amount' => $row['total_amount']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

$connection->close();
?>
