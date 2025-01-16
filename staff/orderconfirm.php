<?php

require("../server/connection.php");

if (isset($_POST['orderId'])) {
    $orderId = $_POST['orderId'];

    $orderId = $connection->real_escape_string($orderId);

    $updateQuery = "UPDATE orders SET orderstatus = 'Confirmed' WHERE orderid = '$orderId'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Order Status confirmed successfully']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error confirming the order']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>