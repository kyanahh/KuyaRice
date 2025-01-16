<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT orders.*, users.firstname, users.lastname 
                FROM orders INNER JOIN users 
                ON orders.userid = users.userid 
                WHERE (orders.orderid LIKE '%$query%' 
                OR orders.userid LIKE '%$query%' 
                OR orders.orderstatus LIKE '%$query%' 
                OR users.firstname LIKE '%$query%' 
                OR users.lastname LIKE '%$query%') 
                ORDER BY orderid DESC";
    } else {
        $sql = "SELECT orders.*, users.firstname, users.lastname 
                FROM orders INNER JOIN users 
                ON orders.userid = users.userid ORDER BY orderid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['orderid'] . '</td>';
            echo '<td>' . $row['userid'] . '</td>';
            echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
            echo '<td>' . $row['orderstatus'] . '</td>';
            echo '<td>' . $row['total_amount'] . '</td>';
            echo '<td>' . $row['ordercreated'] . '</td>';
            echo '<td>' . $row['staffid'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center gap-2">';

            // Pending
            if ($row['orderstatus'] == 'Pending') {
                echo '<button class="btn btn-sm btn-success" onclick="openConfirmModal(' . $row['orderid'] . ')">Confirm</button>';
                echo '<button class="btn btn-sm btn-danger" onclick="cancelOrder(' . $row['orderid'] . ')">Cancel</button>';

            }
            
            // Confirmed
            if ($row['orderstatus'] == 'Confirmed') {
                echo '<button class="btn btn-sm btn-info" onclick="addOrder(' . $row['orderid'] . ')">Add Order</button>';
            }

            // In The Kitchen
            if ($row['orderstatus'] == 'In The Kitchen') {
                echo '<button class="btn btn-sm btn-success" onclick="openServeModal(' . $row['orderid'] . ')">Serve Now</button>';
            }

            // Currently Serving
            if ($row['orderstatus'] == 'Currently Serving') {
                echo '<button class="btn btn-sm btn-success" onclick="openDoneModal(' . $row['orderid'] . ')">Done</button>';
            }

            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">No order record found.</td></tr>';
    }
}

?>