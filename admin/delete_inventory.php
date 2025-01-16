<?php

require("../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['inventoryid'])) {
    $inventoryid = $_POST['inventoryid'];

    $deleteQuery = "DELETE FROM inventory WHERE inventoryid = '$inventoryid'";
    $deleteResult = $connection->query($deleteQuery);

    if ($deleteResult) {
        echo json_encode(array('success' => 'Inventory deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Error deleting inventory: ' . $connection->error));
    }

} else {
    echo json_encode(array('error' => 'Invalid request'));
}

$connection->close();

?>
