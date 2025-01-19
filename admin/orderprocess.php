<?php

require("../server/connection.php");

if (isset($_POST['orderid']) && isset($_POST['paid_amount']) && isset($_POST['change_amount'])) {
    $orderid = $_POST['orderid'];
    $paid_amount = $_POST['paid_amount'];
    $change_amount = $_POST['change_amount'];

    // Sanitize input
    $orderid = $connection->real_escape_string($orderid);
    $paid_amount = $connection->real_escape_string($paid_amount);
    $change_amount = $connection->real_escape_string($change_amount);

    // Update query
    $updateQuery = "
        UPDATE orders 
        SET orderstatus = 'In The Kitchen', 
            paid_amount = '$paid_amount', 
            change_amount = '$change_amount' 
        WHERE orderid = '$orderid'
    ";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Order and payment details updated successfully']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error updating the order and payment details']);
    }
} else {
    echo json_encode(['error' => 'Invalid request. Missing required parameters.']);
}

$connection->close();

?>