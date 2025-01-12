<?php

require("../server/connection.php");

if (isset($_POST['orderid'])) {
    $orderid = $_POST['orderid'];

    $orderid = $connection->real_escape_string($orderid);

    $updateQuery = "UPDATE orders SET orderstatus = 'Cancelled' WHERE orderid = '$orderid'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(array('success' => 'Order cancelled successfully'));
    } else {
        // Log the error for debugging
        error_log("Error: " . $connection->error);
        echo json_encode(array('error' => 'Error cancelling the order'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request'));
}

$connection->close();

?>
