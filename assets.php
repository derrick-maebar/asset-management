<?php
// assets.php
require_once 'config.php';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_asset'])) {
        $asset_name = $_POST['asset_name'];
        $category = $_POST['category'];
        $purchase_date = $_POST['purchase_date'];
        $purchase_value = $_POST['purchase_value'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        $location = $_POST['location'];
        $estimated_lifespan = $_POST['estimated_lifespan'];
        
        $sql = "INSERT INTO assets (asset_name, category, purchase_date, purchase_value, description, status, location, estimated_lifespan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssdsssi", $asset_name, $category, $purchase_date, $purchase_value, $description, $status, $location, $estimated_lifespan);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Asset added successfully!";
            } else {
                $error = "Error adding asset: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['update_asset'])) {
        $id = $_POST['id'];
        $asset_name = $_POST['asset_name'];
        $category = $_POST['category'];
        $purchase_value = $_POST['purchase_value'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        $location = $_POST['location'];
        
        $sql = "UPDATE assets SET asset_name=?, category=?, purchase_value=?, description=?, status=?, location=? WHERE id=?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssdsssi", $asset_name, $category, $purchase_value, $description, $status, $location, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Asset updated successfully!";
            } else {
                $error = "Error updating asset: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_GET['delete_id'])) {
        $id = $_GET['delete_id'];
        
        $sql = "DELETE FROM assets WHERE id=?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Asset deleted successfully!";
            } else {
                $error = "Error deleting asset: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch all assets
$sql = "SELECT * FROM assets ORDER BY asset_name";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Inventory - Karen Country Club</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="main-content">
        <h2>Asset Inventory</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Add New Asset</h3>
            <form method="post" action="">
                <div class="form-group">
                    <label for="asset_name">Asset Name</label>
                    <input type="text" id="asset_name" name="asset_name" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Sports Equipment">Sports Equipment</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Kitchen Equipment">Kitchen Equipment</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Maintenance Equipment">Maintenance Equipment</option>
                        <option value="Vehicles">Vehicles</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="purchase_date">Purchase Date</label>
                    <input type="date" id="purchase_date" name="purchase_date" required>
                </div>
                
                <div class="form-group">
                    <label for="purchase_value">Purchase Value (Ksh)</label>
                    <input type="number" id="purchase_value" name="purchase_value" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="estimated_lifespan">Estimated Lifespan (years)</label>
                    <input type="number" id="estimated_lifespan" name="estimated_lifespan" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Operational">Operational</option>
                        <option value="Under Maintenance">Under Maintenance</option>
                        <option value="Out of Service">Out of Service</option>
                        <option value="Retired">Retired</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                
                <button type="submit" name="add_asset" class="btn btn-primary">Add Asset</button>
            </form>
        </div>
        
        <div class="card">
            <h3>Asset List</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchase Date</th>
                        <th>Value (Ksh)</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['asset_name']}</td>
                                <td>{$row['category']}</td>
                                <td>{$row['purchase_date']}</td>
                                <td>" . number_format($row['purchase_value']) . "</td>
                                <td>{$row['location']}</td>
                                <td>{$row['status']}</td>
                                <td class='action-buttons'>
                                    <a href='edit_asset.php?id={$row['id']}' class='btn btn-warning'>Edit</a>
                                    <a href='assets.php?delete_id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this asset?\")'>Delete</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No assets found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>