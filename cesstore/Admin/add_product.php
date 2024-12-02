<?php
// add_product.php

require '../config.php';
require 'uploads.php'; // Menambahkan pengecekan dan pembuatan folder uploads
session_start();

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Auth/login.php');
    exit();
}

// Menambahkan produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price']; // Menambahkan harga
    $image = $_FILES['image'];

    // Proses upload gambar
    $imagePath = 'uploads/' . basename($image['name']); // Tentukan path gambar
    if (move_uploaded_file($image['tmp_name'], $imagePath)) {
        // Jika upload gambar berhasil, simpan data produk ke database
        $query = "INSERT INTO products (name, description, price, image) VALUES (:name, :description, :price, :image)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,  // Menambahkan parameter harga
            ':image' => $imagePath
        ]);

        // Redirect ke halaman products setelah sukses
        header('Location: products.php');
        exit();
    } else {
        $error = 'Gagal meng-upload gambar!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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

        .btn-success {
            background: linear-gradient(to right, #56ab2f, #a8e063);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(to right, #a8e063, #56ab2f);
        }

        .btn-sm {
            color: #ffffff;
        }

        .form-control {
            background: #f8f9fa;
            border-radius: 5px;
            border: none;
        }

        label {
            color: #ffffff;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Add Product</a>
        <div class="ml-auto">
            <a href="products.php" class="btn btn-primary btn-sm">Manage Products</a>
            <a href="dashboard.php" class="btn btn-success btn-sm">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Add New Product</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= $error; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
