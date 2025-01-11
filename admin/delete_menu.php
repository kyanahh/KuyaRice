<?php

require("../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['menuid'])) {
    $menuid = $_POST['menuid'];

    $deleteQuery = "DELETE FROM menu WHERE menuid = '$menuid'";
    $deleteResult = $connection->query($deleteQuery);

    if ($deleteResult) {
        echo json_encode(array('success' => 'Menu Item deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Error deleting menu item: ' . $connection->error));
    }

} else {
    echo json_encode(array('error' => 'Invalid request'));
}

$connection->close();

?>
