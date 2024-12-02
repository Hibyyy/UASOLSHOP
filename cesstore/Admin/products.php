<?php
// products.php

require '../config.php';
session_start();

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Auth/login.php');
    exit();
}

// Inisialisasi query dan parameter pencarian
$searchQuery = '';
$query = "SELECT * FROM products";

// Cek jika ada query pencarian
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $query .= " WHERE name LIKE :searchQuery OR description LIKE :searchQuery";
}

try {
    $stmt = $pdo->prepare($query);
    if ($searchQuery) {
        $stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
    } else {
        $stmt->execute();
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Menyimpan semua produk
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to bottom, #87CEFA, #4682B4);
            margin: 0;
        }

        .navbar {
            background: linear-gradient(to right, #1E90FF, #4169E1);
            color: #ffffff;
        }

        .navbar a {
            color: #ffffff;
            margin-right: 15px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border: none;
            border-radius: 10px;
            background: linear-gradient(to bottom, #87CEFA, #4682B4);
            color: #ffffff;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(to right, #1E90FF, #4169E1);
            color: #ffffff;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(to right, #1E90FF, #4169E1);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #4682B4, #5F9EA0);
        }

        .btn-danger {
            background: linear-gradient(to right, #DC143C, #FF6347);
            border: none;
        }

        .btn-danger:hover {
            background: linear-gradient(to right, #B22222, #FF4500);
        }

        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid #ffffff;
        }

        .card-body {
            text-align: center;
        }

        .form-inline {
            margin-bottom: 20px;
        }

        input.form-control {
            border: 2px solid #ffffff;
            border-radius: 5px;
        }

        input.form-control:focus {
            box-shadow: 0px 0px 10px rgba(30, 144, 255, 0.7);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Manage Products</a>
        <div class="ml-auto">
            <a href="dashboard.php" class="btn btn-primary btn-sm">Dashboard</a>
            <a href="add_product.php" class="btn btn-success btn-sm">Add Product</a>
            <a href="../Auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container">
        <!-- Form pencarian produk -->
        <form method="GET" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search Products" value="<?= htmlspecialchars($searchQuery); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if (count($products) > 0): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="<?= $product['image']; ?>" class="card-img-top product-img" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?= $product['name']; ?></h5>
                                <p class="card-text"><?= $product['description']; ?></p>
                                <p class="card-text"><strong>Price: </strong>Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-primary btn-block">Edit</a>
                                <a href="delete_product.php?id=<?= $product['id']; ?>" class="btn btn-danger btn-block">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No products found for your search query.</p>
        <?php endif; ?>
    </div>
</body>

</html>
