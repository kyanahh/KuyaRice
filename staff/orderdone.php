<?php

require("../server/connection.php");

if (isset($_POST['orderid'])) {
    $orderid = $_POST['orderid'];

    $orderid = $connection->real_escape_string($orderid);

    $updateQuery = "UPDATE orders SET orderstatus = 'Done' WHERE orderid = '$orderid'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Order finalized successfully']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error finalizing the order']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>