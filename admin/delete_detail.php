<?php

require("../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['orderdetailid'])) {
    $orderdetailid = $_POST['orderdetailid'];

    $deleteQuery = "DELETE FROM order_details WHERE orderdetailid = '$orderdetailid'";
    $deleteResult = $connection->query($deleteQuery);

    if ($deleteResult) {
        echo json_encode(array('success' => 'Order Item deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Error deleting order item: ' . $connection->error));
    }

} else {
    echo json_encode(array('error' => 'Invalid request'));
}

$connection->close();

?>
