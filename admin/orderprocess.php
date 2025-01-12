<?php

require("../server/connection.php");

if (isset($_POST['orderid'])) {
    $orderid = $_POST['orderid'];

    $orderid = $connection->real_escape_string($orderid);

    $updateQuery = "UPDATE orders SET orderstatus = 'In The Kitchen' WHERE orderid = '$orderid'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Order processed successfully']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error processing the order']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>