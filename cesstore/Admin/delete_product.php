<?php
// delete_product.php

require '../config.php';
session_start();

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Auth/login.php');
    exit();
}

// Cek apakah ada parameter ID yang dikirim
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus produk berdasarkan ID
    $query = "DELETE FROM products WHERE id = :id";
    $stmt = $pdo->prepare($query);

    // Eksekusi query
    if ($stmt->execute([':id' => $id])) {
        // Redirect kembali ke halaman produk setelah berhasil menghapus
        header('Location: products.php');
        exit();
    } else {
        echo 'Gagal menghapus produk.';
    }
} else {
    echo 'ID produk tidak ditemukan.';
}
?>
