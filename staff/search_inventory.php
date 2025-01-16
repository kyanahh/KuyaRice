<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT inventory.*, menu.menuitem 
                FROM inventory INNER JOIN menu 
                ON inventory.menuid = menu.menuid 
                WHERE menuitem LIKE '%$query%' 
                OR quantity LIKE '%$query%' 
                OR transaction_date LIKE '%$query%' 
                OR DATE_FORMAT(transaction_date, '%b %d %Y') LIKE '%$query%' 
                OR DATE_FORMAT(transaction_date, '%M %d %Y') LIKE '%$query%' 
                OR DATE_FORMAT(transaction_date, '%m/%d/%Y') LIKE '%$query%' 
                OR DATE_FORMAT(transaction_date, '%m-%d-%Y') LIKE '%$query%'
                ORDER BY inventoryid DESC";
    } else {
        $sql = "SELECT inventory.*, menu.menuitem 
                FROM inventory INNER JOIN menu 
                ON inventory.menuid = menu.menuid ORDER BY inventoryid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1; 
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['menuitem'] . '</td>';
            echo '<td>' . $row['quantity'] . '</td>';
            echo '<td>' . $row['remarks'] . '</td>';
            echo '<td>' . $row['transaction_date'] . '</td>';
            echo '<td>' . $row['staffid'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center gap-2">';
            echo '<button class="btn btn-sm btn-danger" onclick="deleteInventory(' . $row['inventoryid'] . ')">Delete</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++; 
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">No inventory item found.</td></tr>';
    }
}

?>