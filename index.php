<?php
session_start();
// Include the database connection file here
include 'db_connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karen Country Club - Asset Management System</title>
    <style>
        :root {
            --primary: #2c6e49;
            --secondary: #4c956c;
            --light: #fefee3;
            --accent: #ffc9b9;
            --dark: #1a535c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
        }
        
        .header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--secondary);
            color: white;
            height: calc(100vh - 70px);
            position: fixed;
            padding-top: 1rem;
        }
        
        .sidebar a {
            display: block;
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .sidebar a:hover {
            background-color: var(--dark);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        table, th, td {
            border: 1px solid #ddd;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: var(--primary);
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: black;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Karen Country Club - Asset Management</h1>
        <div>
            <span>Welcome, <?php echo $_SESSION['username'] ?? 'Guest'; ?></span>
            <a href="logout.php" style="color: white; margin-left: 1rem;">Logout</a>
        </div>
    </div>
    
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="assets.php">Asset Inventory</a>
        <a href="maintenance.php">Maintenance</a>
        <a href="reports.php">Reports</a>
        <a href="users.php">User Management</a>
    </div>
    
    <div class="main-content">
        <h2>Asset Dashboard</h2>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Assets</h3>
                <div class="value"><?php
                    $sql = "SELECT COUNT(*) as total FROM assets";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Total Asset Value</h3>
                <div class="value">Ksh <?php
                    $sql = "SELECT SUM(purchase_value) as total_value FROM assets";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total_value'] ?? 0);
                ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Pending Maintenance</h3>
                <div class="value"><?php
                    $sql = "SELECT COUNT(*) as pending FROM maintenance WHERE status = 'Pending'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['pending'];
                ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Assets Due for Replacement</h3>
                <div class="value"><?php
                    $sql = "SELECT COUNT(*) as due FROM assets WHERE DATEDIFF(CURDATE(), purchase_date) / 365 > estimated_lifespan * 0.8";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['due'];
                ?></div>
            </div>
        </div>
        
        <div class="card">
            <h3>Recently Added Assets</h3>
            <table>
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchase Date</th>
                        <th>Value (Ksh)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM assets ORDER BY purchase_date DESC LIMIT 5";
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>{$row['asset_id']}</td>
                                <td>{$row['asset_name']}</td>
                                <td>{$row['category']}</td>
                                <td>{$row['purchase_date']}</td>
                                <td>" . number_format($row['purchase_value']) . "</td>
                                <td>{$row['status']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No assets found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h3>Upcoming Maintenance</h3>
            <table>
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Maintenance Type</th>
                        <th>Scheduled Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT m.*, a.asset_name 
                            FROM maintenance m 
                            JOIN assets a ON m.asset_id = a.id 
                            WHERE m.scheduled_date >= CURDATE() 
                            ORDER BY m.scheduled_date ASC 
                            LIMIT 5";
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>{$row['asset_name']}</td>
                                <td>{$row['maintenance_type']}</td>
                                <td>{$row['scheduled_date']}</td>
                                <td>{$row['status']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No upcoming maintenance</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>