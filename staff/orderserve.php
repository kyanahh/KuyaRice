<?php

require("../server/connection.php");

if (isset($_POST['orderid'])) {
    $orderid = $_POST['orderid'];

    $orderid = $connection->real_escape_string($orderid);

    $updateQuery = "UPDATE orders SET orderstatus = 'Currently Serving' WHERE orderid = '$orderid'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Order currently serving.']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error serving the order']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>