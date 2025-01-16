<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT * FROM orders WHERE userid = '$textaccount' 
                WHERE (orderid LIKE '%$query%' 
                OR orders.orderstatus LIKE '%$query%') 
                ORDER BY orderid DESC";
    } else {
        $sql = "SELECT * FROM orders WHERE userid = '$textaccount' ORDER BY orderid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['orderid'] . '</td>';
            echo '<td>' . $row['orderstatus'] . '</td>';
            echo '<td>' . $row['total_amount'] . '</td>';
            echo '<td>' . $row['ordercreated'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center gap-2">';

            // Confirmed
            if ($row['orderstatus'] == 'Confirmed') {
                echo '<button class="btn btn-sm btn-success" onclick="addOrder(' . $row['orderid'] . ')">Add Order</button>';
            }

            // In The Kitchen
            if ($row['orderstatus'] == 'In The Kitchen') {
                echo '<button class="btn btn-sm btn-info" onclick="View(' . $row['orderid'] . ')">View</button>';
            }

            // Currently Serving
            if ($row['orderstatus'] == 'Currently Serving') {
                echo '<button class="btn btn-sm btn-info" onclick="View(' . $row['orderid'] . ')">View</button>';
            }

            // Done
            if ($row['orderstatus'] == 'Done') {
                echo '<button class="btn btn-sm btn-info" onclick="View(' . $row['orderid'] . ')">View</button>';
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