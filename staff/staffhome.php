<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["userid"])){
        $textaccount = $_SESSION["userid"];
        $fname = $_SESSION["firstname"];
        $lname = $_SESSION["lastname"];
        $useremail = $_SESSION["email"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

// Example: Inventory levels of each menu
$query_inventory = "SELECT 
    i.menuitem, 
    SUM(inv.quantity) AS available 
FROM 
    inventory inv
JOIN 
    menu i ON inv.menuid = i.menuid
GROUP BY 
    i.menuid, i.menuitem
HAVING 
    available > 0"; // Only include items with inventory levels > 0

$result_inventory = mysqli_query($connection, $query_inventory);

$inventory_data = [];
while ($row = mysqli_fetch_assoc($result_inventory)) {
    $inventory_data[] = $row;
}

// Example: Sales daily
$query_sales_daily = "SELECT SUM(total_amount) as daily_sales, DATE(ordercreated) as order_date FROM orders WHERE DATE(ordercreated) = CURDATE() GROUP BY order_date";
$result_sales_daily = mysqli_query($connection, $query_sales_daily);

// Fetch sales daily data into an array
$sales_daily_data = [];
while ($row = mysqli_fetch_assoc($result_sales_daily)) {
    $sales_daily_data[] = $row;
}

// Example: Sales weekly
$query_sales_weekly = "SELECT SUM(total_amount) as weekly_sales, YEARWEEK(ordercreated) as week FROM orders WHERE YEARWEEK(ordercreated) = YEARWEEK(CURDATE()) GROUP BY week";
$result_sales_weekly = mysqli_query($connection, $query_sales_weekly);

// Fetch sales weekly data into an array
$sales_weekly_data = [];
while ($row = mysqli_fetch_assoc($result_sales_weekly)) {
    $sales_weekly_data[] = $row;
}

// Monthly sales
$query_sales_monthly = "SELECT SUM(total_amount) as monthly_sales, MONTH(ordercreated) as month, YEAR(ordercreated) as year
                         FROM orders 
                         WHERE YEAR(ordercreated) = YEAR(CURDATE())
                         GROUP BY month ORDER BY month ASC";
$result_sales_monthly = mysqli_query($connection, $query_sales_monthly);

// Process the sales monthly data
$sales_monthly_data = [];
while ($row = mysqli_fetch_assoc($result_sales_monthly)) {
    $sales_monthly_data[] = $row;
}

// Sales yearly
$query_sales_yearly = "SELECT YEAR(ordercreated) as year, SUM(total_amount) as yearly_sales 
                       FROM orders 
                       GROUP BY year 
                       ORDER BY year ASC";

$result_sales_yearly = mysqli_query($connection, $query_sales_yearly);

$sales_yearly_data = [];
while ($row = mysqli_fetch_assoc($result_sales_yearly)) {
    $sales_yearly_data[] = $row;
}

// Users 
// Admin count
$query_admin_count = "SELECT COUNT(*) AS admin_count FROM users WHERE usertype = 'Admin'";
$result_admin_count = mysqli_query($connection, $query_admin_count);
if ($result_admin_count) {
    $admin_count = mysqli_fetch_assoc($result_admin_count);
} else {
    echo "Error fetching admin count: " . mysqli_error($connection);
}

// Staff count
$query_staff_count = "SELECT COUNT(*) AS staff_count FROM users WHERE usertype = 'Staff'";
$result_staff_count = mysqli_query($connection, $query_staff_count);
if ($result_staff_count) {
    $staff_count = mysqli_fetch_assoc($result_staff_count);
} else {
    echo "Error fetching staff count: " . mysqli_error($connection);
}

// Customer count
$query_customer_count = "SELECT COUNT(*) AS customer_count FROM users WHERE usertype = 'Customer'";
$result_customer_count = mysqli_query($connection, $query_customer_count);
if ($result_customer_count) {
    $customer_count = mysqli_fetch_assoc($result_customer_count);
} else {
    echo "Error fetching customer count: " . mysqli_error($connection);
}

// Gender distribution query
$query_gender_count = "SELECT gender, COUNT(*) AS count FROM users GROUP BY gender";
$result_gender_count = mysqli_query($connection, $query_gender_count);

if ($result_gender_count) {
    // Initialize the gender count array with default values
    $gender_count = [
        'male' => 0, // Default value for Male
        'female' => 0 // Default value for Female
    ];

    // Loop through the result set to get the gender counts
    while ($row = mysqli_fetch_assoc($result_gender_count)) {
        // Normalize gender value to lowercase for consistent matching
        $gender = strtolower($row['gender']);
        if (isset($gender_count[$gender])) {
            $gender_count[$gender] = $row['count'];
        }
    }

} else {
    echo "Error fetching gender count: " . mysqli_error($connection);
}

// Order status 
$query_order_status = "SELECT orderstatus, COUNT(*) as count 
                       FROM orders 
                       GROUP BY orderstatus";
$result_order_status = mysqli_query($connection, $query_order_status); // Add this line

// Process order status data
$order_status_data = [];
while ($row = mysqli_fetch_assoc($result_order_status)) {
    $order_status_data[] = $row;
}

// Total sales
$query_total_sales = "SELECT SUM(total_amount) as total_sales FROM orders";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tapsihan ni Kuya Rice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .equal-height {
            height: 100%; /* Ensures consistent height */
        }

        .row {
            display: flex; /* Enables consistent alignment */
        }

        .col-md-5 {
            flex: 1; /* Ensures equal width for all columns */
        }

        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-black py-3">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBlackNavbar" aria-controls="offcanvasBlackNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <img src="../img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
            </div>
            <div class="offcanvas offcanvas-start bg-black text-white" tabindex="-1" id="offcanvasBlackNavbar" aria-labelledby="offcanvasBlackNavbarLabel">
                <div class="offcanvas-header">
                    <img src="../img/logobg.png" alt="KUYA RICE" style="height: 7vh;" class="ms-3">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="staffhome.php"><i class="bi bi-bar-chart me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php"><i class="bi bi-people me-2"></i>Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="menu.php"><i class="bi bi-book me-2"></i>Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php"><i class="bi bi-cart-check me-2"></i>Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                        </li>
                    </ul>
                    <div class="dropup py-sm-4 py-1 mt-sm-auto ms-auto ms-sm-0 flex-shrink-1">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../img/nopf.jpg" alt="hugenerd" width="28" height="28" class="rounded-circle">
                            <span class="d-none d-sm-inline mx-2"><?php echo $fname . " " . $lname ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark px-0 px-sm-2 text-center text-sm-start" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item px-1" href="profile.php"><i class="fs-6 bi-person"></i><span class="d-none d-sm-inline ps-1">Profile</span></a></li>
                            <li><a class="dropdown-item px-1" href="settings.php"><i class="fs-6 bi-gear"></i><span class="d-none d-sm-inline ps-1">Settings</span></a></li>
                            <li><a class="dropdown-item px-1" href="../logout.php"><i class="fs-6 bi-power"></i><span class="d-none d-sm-inline ps-1">Logout</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN -->
    <div class="container my-5">
        <h3 class="text-center mb-3 fw-bold">Dashboard</h3>
        <div class="justify-content-center d-flex row mb-5">
            <!-- users -->
            <div class="col-md-5">
                <div class="card equal-height shadow">
                    <div class="card-body">
                        <h5 class="card-title">User Counts</h5>
                        <p class="card-text">Admin Count: <?php echo isset($admin_count['admin_count']) ? $admin_count['admin_count'] : 0; ?></p>
                        <p class="card-text">Staff Count: <?php echo isset($staff_count['staff_count']) ? $staff_count['staff_count'] : 0; ?></p>
                        <p class="card-text">Customer Count: <?php echo isset($customer_count['customer_count']) ? $customer_count['customer_count'] : 0; ?></p>
                    </div>
                </div>
            </div>
            <!-- gender -->
            <div class="col-md-5">
                <div class="card equal-height">
                    <div class="card-body shadow">
                        <h5 class="card-title">Customer Gender Distribution</h5>
                        <p class="card-text">Male Customers: <?php echo isset($gender_count['male']) ? $gender_count['male'] : 0; ?></p>
                        <p class="card-text">Female Customers: <?php echo isset($gender_count['female']) ? $gender_count['female'] : 0; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-center d-flex row mb-5">
            <!-- Sales daily -->
            <div class="col-md-4">
                <canvas id="salesDailyChart"></canvas>
            </div>
            <!-- Sales weekly -->
            <div class="col-md-4">
                <canvas id="salesWeeklyChart"></canvas>
            </div>
            <!-- Sales monthly -->
            <div class="col-md-4">
                <canvas id="salesMonthlyChart"></canvas>
            </div>
        </div>
        <div class="justify-content-center d-flex row mb-5">
            <!-- Inventory levels of each menu -->
            <div class="col-md-5">
                <canvas id="inventoryChart"></canvas>
            </div>
            <!-- Sales yearly -->
            <div class="col-md-5">
                <canvas id="salesYearlyChart"></canvas>
            </div>
        </div>
        <div class="justify-content-center d-flex row">
            <!-- order status -->
            <div class="col-md-4">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>

    </div>


    <!-- FOOTER -->
    <footer class="bg-black d-flex align-items-center mt-5" style="height: 200px;">
        <div class="container-fluid row m-2 text-white">
            <div class="row">
                <div class="col-sm-5">
                    <img src="../img/logobg.png" alt="KUYA RICE" class="w-50">
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">About</a>
                    </div>
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">Contact Us</a><br>
                    </div>
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">Careers</a><br>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                      <a href="#" class="text-decoration-none text-white mb-3">FAQs</a>
                    </div>
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white mb-3">Terms & Conditions</a>
                    </div>
                    <div class="row">
                        <a href="#" class="text-decoration-none text-white">Privacy Policy</a>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="row">
                        <div class="d-flex align-items-center gap-3">
                            <a href="https://www.facebook.com/profile.php?id=100064204447018" class="text-decoration-none text-white">
                                <i class="bi bi-facebook fs-5"></i>
                            </a>
                            <a href="m.me/100355241697170" class="text-decoration-none text-white">
                                <i class="bi bi-messenger fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </footer>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <script>
        // Inventory levels chart
        var inventoryData = <?php echo json_encode($inventory_data); ?>;
        var inventoryLabels = [];
        var inventoryValues = [];
        inventoryData.forEach(function(row) {
            inventoryLabels.push(row.menuitem);
            inventoryValues.push(row.available);
        });

        var inventoryChart = new Chart(document.getElementById('inventoryChart'), {
            type: 'bar',
            data: {
                labels: inventoryLabels,
                datasets: [{
                    label: 'Inventory Levels',
                    data: inventoryValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Sales daily chart
        var salesDailyData = <?php echo json_encode($sales_daily_data); ?>;
        var dailyLabels = [];
        var dailySales = [];
        salesDailyData.forEach(function(row) {
            dailyLabels.push(row.order_date);
            dailySales.push(row.daily_sales);
        });

        var salesDailyChart = new Chart(document.getElementById('salesDailyChart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Daily Sales',
                    data: dailySales,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                }]
            }
        });

        // Sales weekly chart
        var salesWeeklyData = <?php echo json_encode($sales_weekly_data); ?>;
        var weeklyLabels = [];
        var weeklySales = [];
        salesWeeklyData.forEach(function(row) {
            weeklyLabels.push('Week ' + row.week);
            weeklySales.push(row.weekly_sales);
        });

        var salesWeeklyChart = new Chart(document.getElementById('salesWeeklyChart'), {
            type: 'bar',
            data: {
                labels: weeklyLabels,
                datasets: [{
                    label: 'Weekly Sales',
                    data: weeklySales,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            }
        });

        // sales monthly
        var salesMonthlyData = <?php echo json_encode($sales_monthly_data); ?>;
        var monthlyLabels = [];
        var monthlySales = [];
        salesMonthlyData.forEach(function(row) {
            monthlyLabels.push('Month ' + row.month);
            monthlySales.push(row.monthly_sales);
        });

        var salesMonthlyChart = new Chart(document.getElementById('salesMonthlyChart'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Monthly Sales',
                    data: monthlySales,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            }
        });

        // sales yearly
        var salesYearlyData = <?php echo json_encode($sales_yearly_data); ?>;
        var yearlyLabels = [];
        var yearlySales = [];
        salesYearlyData.forEach(function(row) {
            yearlyLabels.push(row.year);
            yearlySales.push(row.yearly_sales);
        });

        var salesYearlyChart = new Chart(document.getElementById('salesYearlyChart'), {
            type: 'line',
            data: {
                labels: yearlyLabels,
                datasets: [{
                    label: 'Yearly Sales',
                    data: yearlySales,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    fill: false
                }]
            }
        });

        // order status 
        var orderStatusData = <?php echo json_encode($order_status_data); ?>;
        var orderStatusLabels = [];
        var orderStatusCounts = [];
        orderStatusData.forEach(function(row) {
            orderStatusLabels.push(row.orderstatus);
            orderStatusCounts.push(row.count);
        });

        var orderStatusChart = new Chart(document.getElementById('orderStatusChart'), {
            type: 'pie',
            data: {
                labels: orderStatusLabels,
                datasets: [{
                    label: 'Order Status',
                    data: orderStatusCounts,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40']
                }]
            }
        });

    </script>

</body>
</html>
