<?php
session_start(); // Start the session for toast notifications
// Include database connection
require("../server/connection.php");

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['guestName']) && !empty(trim($data['guestName']))) {
    $guestName = trim($data['guestName']);
    $usertype = 'Guest';

    // Prepare and execute the query to insert into the users table
    $stmt = $connection->prepare("INSERT INTO users (firstname, usertype) VALUES (?, ?)");
    $stmt->bind_param("ss", $guestName, $usertype);

    if ($stmt->execute()) {
        // Set session flag for toast notification
        $_SESSION['update_success'] = "Guest ID created successfully!";
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Guest name is required.']);
}

$connection->close();
?>
