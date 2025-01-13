<?php
require("../server/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['userid'])) {
    $userid = $_POST['userid'];
    $staffid = $_POST['staffid']; 
    $orderstatus = "Pending";
    $currentDateTime = date("Y-m-d H:i:s");

    $stmt = $connection->prepare("INSERT INTO orders (userid, orderstatus, ordercreated, staffid) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $userid, $orderstatus, $currentDateTime, $staffid);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User ID in order added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add User ID. ' . $connection->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing User ID.']);
}
?>
