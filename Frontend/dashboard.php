<?php
// Assuming you have already started the session where the Uid is stored
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "FiscalPoint";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Uid from session (replace with your actual session variable)
$uid = $_SESSION['Uid'];

// Query to fetch today's expense
$sql_today = "SELECT SUM(amount) AS total_today FROM Expense WHERE Uid = ? AND DATE(Date) = CURDATE()";
$stmt = $conn->prepare($sql_today);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result_today = $stmt->get_result();
$row_today = $result_today->fetch_assoc();
$today_expense = isset($row_today['total_today']) ? $row_today['total_today'] : 0;


// Query to fetch yesterday's expense
$sql_yesterday = "SELECT * FROM Expense WHERE Uid = $uid AND DATE(Date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$result_yesterday = $conn->query($sql_yesterday);
$row_yesterday = $result_yesterday->fetch_assoc();
$yesterday_expense = isset($row_yesterday['amount']) ? $row_yesterday['amount'] : 0;


// Query to fetch monthly expense
$sql_monthly = "SELECT SUM(amount) AS total_monthly FROM Expense WHERE Uid = $uid AND MONTH(Date) = MONTH(CURDATE())";
$result_monthly = $conn->query($sql_monthly);
$row_monthly = $result_monthly->fetch_assoc();
$monthly_expense = $row_monthly['total_monthly'];

// Query to fetch yearly expense
$sql_yearly = "SELECT SUM(amount) AS total_yearly FROM Expense WHERE Uid = $uid AND YEAR(Date) = YEAR(CURDATE())";
$result_yearly = $conn->query($sql_yearly);
$row_yearly = $result_yearly->fetch_assoc();
$yearly_expense = $row_yearly['total_yearly'];

$currentMonth = date("F");
$sql_budget = "SELECT Amount FROM Budget WHERE Uid = ? AND Month = ?";
$stmt = $conn->prepare($sql_budget);
$stmt->bind_param("is", $uid, $currentMonth);
$stmt->execute();
$result_budget = $stmt->get_result();
$row_budget = $result_budget->fetch_assoc();
$monthly_budget = isset($row_budget['Amount']) ? $row_budget['Amount'] : "No budget set";




// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<header> <img src="css/logo.png" alt="Logo" class="logo" onclick="location.href='landing.html'"></header>
<body>
    <aside class="sidebar">
        <div class="profile">
            <img src="css/profile.png" alt="Profile Image" class="avatar">
        </div>
        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li><br>
            <li><a href="setbudget.php">Budget</a></li><br>
            <li><a href="addexpense.php">Add Expense</a></li><br>
            <li>
            <li class="dropdown">
            <a href="#"><span style="font-style: italic; font-weight: bold;">Graph Reports:</span></a>
            <ul>
            <li><a href="linegraph.php">Line Graph Report</a></li>
            <li><a href="piegraph.php">Pie Graph Report</a></li>
        </ul>
            </li>
            <br>
    <li>
        <a href="#"> <span style="font-style: italic; font-weight: bold;">Tabular Reports:</span></a><br>
        <ul>
            <li><a href="tabularreport.php">All Expenses</a></li>
            <li><a href="categorywisereport.php">Category wise Expense</a></li>
        </ul>
    </li><br>
            <li><a href="profile.php">Profile</a></li><br>
            <li><a href="logout.php">Logout</a></li><br>
        </ul>
    </aside>
    </aside>
        <main class="dashboard">
            <div>
                <h3 class="budget-text">Your Budget :  <?php echo $currentMonth; ?></h3>
                <div class="Budget">
                <p><?php echo $monthly_budget; ?></p>
            </div>
        </div><br>
            <div class="expense-box">
                <h3>Today's Expense:</h3>
                <div class="expense-card">
                <p style="text-align: center; font-weight: bold; ">
                    <?php echo $today_expense; ?></div>
            </div>
            <div class="expense-box">
                <h3>Yesterday's Expense:</h3>
                <div class="expense-card"><?php echo $yesterday_expense; ?></div>
            </div>
            <div class="expense-box">
                <h3>Monthly Expense:</h3>
                <div class="expense-card"><?php echo $monthly_expense; ?></div>
            </div>
            <div class="expense-box">
                <h3>This Year Expense:</h3>
                <div class="expense-card"><?php echo $yearly_expense; ?></div>
            </div>
        </main>
    </div>
</body>
</html>
