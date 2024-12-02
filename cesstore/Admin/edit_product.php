<?php
// edit_product.php

require '../config.php';  // Menyertakan koneksi database PDO

// Cek apakah user sudah login dan memiliki role admin
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../dashboard.php');
    exit();
}

// Ambil ID produk dari URL
$product_id = $_GET['id'];

// Ambil data produk yang akan diedit
$query = "SELECT * FROM products WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fungsi untuk format harga ke rupiah
function format_rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Proses pembaruan produk jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    // Hapus simbol Rp dan titik pemisah ribuan dari input harga
    $price = str_replace(['Rp', '.', ' '], '', $_POST['price']); // Menghapus "Rp", titik dan spasi

    // Pastikan harga berupa angka
    if (!is_numeric($price)) {
        echo "Harga tidak valid!";
        exit;
    }

    $image = $_FILES['image']['name'];
    $target = "../Image/" . basename($image);

    // Jika gambar baru diupload
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Update produk ke database
        $update_query = "UPDATE products SET name = :name, price = :price, image = :image WHERE id = :id";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':image' => $image,
            ':id' => $product_id
        ]);
        header('Location: products.php');
    } else {
        // Jika tidak ada gambar baru, hanya update nama dan harga
        $update_query = "UPDATE products SET name = :name, price = :price WHERE id = :id";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':id' => $product_id
        ]);
        header('Location: products.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #1e90ff);
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .navbar {
            background-color: #003366;
        }

        .navbar a {
            color: white;
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
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            background: #f0f8ff;
        }

        .card-header {
            background: linear-gradient(135deg, #003366, #0066cc);
            color: white;
            text-align: center;
            font-size: 1.5rem;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: bold;
            color: #003366;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #003366, #0066cc);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #004080, #0073e6);
        }

        .image-preview {
            max-width: 150px;
            border-radius: 10px;
        }

        .mb-3 small {
            display: block;
            margin-top: 10px;
            font-size: 0.875rem;
            color: #555;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Admin Panel</a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-edit"></i> Edit Product
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $product['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Product Price</label>
                        <!-- Menampilkan harga dalam format rupiah -->
                        <input type="text" class="form-control" id="price" name="price" value="<?= format_rupiah($product['price']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <small>Current image: <img src="../Image/<?= $product['image']; ?>" class="image-preview" alt="Current Product Image"></small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
