<?php
// dashboard.php

require '../config.php';
session_start();

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Auth/login.php');
    exit();
}

// Ambil data produk untuk menampilkan jumlah total produk (opsional)
$query = "SELECT COUNT(*) AS total_products FROM products";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalProducts = $data['total_products'];
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Cek apakah ada parameter status di URL
$successMessage = '';
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $successMessage = 'Transaction confirmed successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #2193b0, #6dd5ed);
            margin: 0;
            color: #fff;
        }

        .navbar {
            background: linear-gradient(to right, #1f3b7d, #2a5298);
            color: #ffffff;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            background: linear-gradient(to bottom, #3a7bd5, #3a6073);
            border: none;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #ffffff;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(to right, #396afc, #2948ff);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #2948ff, #396afc);
        }

        .btn-danger {
            background: linear-gradient(to right, #e53935, #e35d5b);
            border: none;
        }

        .btn-danger:hover {
            background: linear-gradient(to right, #e35d5b, #e53935);
        }

        .alert-success {
            margin-top: 20px;
        }

        .d-flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Admin Dashboard</a>
    <div class="ml-auto">
        <a href="products.php" class="btn btn-primary btn-sm">Manage Products</a>
        <a href="admin_confirm_transaction.php?id=123&success=true" class="btn btn-primary btn-sm">Confirm</a>
        <a href="add_product.php" class="btn btn-success btn-sm">Add Product</a>
        <a href="../Auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container">
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>

    <div class="row d-flex-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Welcome, Admin!</h4>
                </div>
                <div class="card-body">
                    <p>You have total <strong><?= $totalProducts; ?></strong> products listed in the store.</p>
                    <a href="products.php" class="btn btn-primary btn-block">Manage Products</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>
