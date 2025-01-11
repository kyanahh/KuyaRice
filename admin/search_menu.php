<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT * FROM menu 
                WHERE (menuitem LIKE '%$query%' 
                OR descrip LIKE '%$query%' 
                OR price LIKE '%$query%' 
                OR available LIKE '%$query%') 
                ORDER BY CASE WHEN 
                available = 'Available' THEN 1 WHEN available = 'Out of Stock' THEN 2 ELSE 3 END";
    } else {
        $sql = "SELECT * FROM menu ORDER BY CASE WHEN 
                available = 'Available' THEN 1 WHEN available = 'Out of Stock' THEN 2 ELSE 3 END;";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1; 
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['menuitem'] . '</td>';
            echo '<td>' . $row['descrip'] . '</td>';
            echo '<td>' . $row['price'] . '</td>';
            echo '<td>' . $row['available'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center gap-2">';
            echo '<button class="btn btn-sm btn-primary" onclick="editUser(' . $row['menuid'] . ')">Edit</button>';
            echo '<button class="btn btn-sm btn-danger" onclick="deleteUser(' . $row['menuid'] . ')">Delete</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++; 
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">No menu item found.</td></tr>';
    }
}

?>