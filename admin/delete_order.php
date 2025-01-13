<?php

require("../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['orderid'])) {
    $orderid = $connection->real_escape_string($_POST['orderid']);

    // Begin a transaction for safety
    $connection->begin_transaction();

    try {
        // Delete from order_details table
        $deleteOrderDetailsQuery = "DELETE FROM order_details WHERE orderid = '$orderid'";
        $connection->query($deleteOrderDetailsQuery);

        // Delete from orders table
        $deleteOrdersQuery = "DELETE FROM orders WHERE orderid = '$orderid'";
        $connection->query($deleteOrdersQuery);

        // Commit the transaction
        $connection->commit();

        echo json_encode(array('success' => 'Order deleted successfully'));
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $connection->rollback();

        echo json_encode(array('error' => 'Error deleting order: ' . $e->getMessage()));
    }

} else {
    echo json_encode(array('error' => 'Invalid request'));
}

$connection->close();

?>