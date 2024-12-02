<?php
require 'config.php';

// Ambil data produk dengan pencarian
$searchQuery = '';

// Cek jika ada pencarian
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $query = "SELECT * FROM products WHERE name LIKE :searchQuery OR description LIKE :searchQuery";
} else {
    // Jika tidak ada pencarian, tampilkan semua produk
    $query = "SELECT * FROM products";
}

$stmt = $pdo->prepare($query);
if ($searchQuery) {
    $stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
} else {
    $stmt->execute();
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIESHOP</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: linear-gradient(to right, #A3C4FC, #5D9DFD); /* Gradasi biru terang */
            color: #ffffff;
            position: relative;
        }
        .navbar {
            background-color: #1E90FF; /* Biru cerah */
        }
        .navbar-brand, .nav-link {
            color: #FFFFFF !important;
        }
        .container {
            padding-top: 20px;
            z-index: 1;
            position: relative;
            background-color: rgba(0, 0, 0, 0.7); /* Transparansi untuk efek gradasi */
            border-radius: 10px;
            padding: 20px;
            color: #FFFFFF;
        }
        .card {
            background-color: #F0F8FF; /* Biru pucat */
            color: #000; /* Warna teks untuk kontras */
            margin-bottom: 20px;
            border: 2px solid #A3C4FC;
            border-radius: 10px;
        }
        .card-title {
            color: #1E90FF; /* Biru cerah */
        }
        .btn-primary {
            background-color: #5D9DFD; /* Biru terang */
            border: none;
        }
        .btn-primary:hover {
            background-color: #1E90FF; /* Biru cerah saat hover */
        }
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .form-inline input[type="text"] {
            border-radius: 5px;
            border: 1px solid #5D9DFD;
            background-color: #E6F7FF; /* Biru terang */
            color: #000;
        }
        .form-inline button {
            border-radius: 5px;
            background-color: #5D9DFD;
            color: #fff;
        }
        .form-inline button:hover {
            background-color: #1E90FF;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">BIESHOP</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="history.php"><i class="fas fa-history"></i> Riwayat Transaksi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Order/cart.php"><i class="fas fa-shopping-cart"></i></a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="Auth/logout.php"><i class="fas fa-sign-out-alt"></i></a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="Auth/login.php"><i class="fas fa-sign-in-alt"></i></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <!-- Form pencarian produk -->
    <form method="GET" class="form-inline mb-4">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search Products" value="<?= htmlspecialchars($searchQuery); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <h1 class="mb-4 text-center">Our Products</h1>
    <div class="row">
        <?php if (empty($products)): ?>
            <p class="text-center w-100">No products found.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= 'admin/' . $product['image']; ?>" class="card-img-top product-img" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['description']); ?></p>
                            <p class="card-text">RP <?= number_format($product['price'], 0, ',', '.'); ?></p>
                            <a href="Order/cart.php?action=add&id=<?= $product['id']; ?>" class="btn btn-primary">Add to Cart</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
