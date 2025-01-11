<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT * FROM users 
                WHERE (userid LIKE '%$query%' 
                OR firstname LIKE '%$query%' 
                OR lastname LIKE '%$query%' 
                OR gender LIKE '%$query%' 
                OR phone LIKE '%$query%' 
                OR homeaddress LIKE '%$query%' 
                OR email LIKE '%$query%' 
                OR usertype LIKE '%$query%') ORDER BY userid DESC";
    } else {
        $sql = "SELECT * FROM users ORDER BY userid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1; 
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['userid'] . '</td>';
            echo '<td>' . $row['firstname'] . '</td>';
            echo '<td>' . $row['lastname'] . '</td>';
            echo '<td>' . $row['gender'] . '</td>';
            echo '<td>' . $row['phone'] . '</td>';
            echo '<td>' . $row['homeaddress'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>' . $row['usertype'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center gap-2">';
            echo '<button class="btn btn-sm btn-primary" onclick="editUser(' . $row['userid'] . ')">Edit</button>';
            echo '<button class="btn btn-sm btn-danger" onclick="deleteUser(' . $row['userid'] . ')">Delete</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++; 
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">No users found.</td></tr>';
    }
}

?>