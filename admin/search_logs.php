<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT * FROM userlogs 
                WHERE userid LIKE '%$query%' 
                ORDER BY logid DESC";
    } else {
        $sql = "SELECT * FROM userlogs ORDER BY logid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1; 
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['userid'] . '</td>';
            echo '<td>' . $row['logtime'] . '</td>';
            echo '</tr>';
            $count++; 
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">No user log found.</td></tr>';
    }
}

?>