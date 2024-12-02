<?php
require 'config.php'; // Jika config.php ada di folder yang sama
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data transaksi dari database
$sql = "SELECT t.id, t.total, t.status, t.created_at
        FROM transaksi t
        WHERE t.user_id = ?
        ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #A3C4FC, #5D9DFD); /* Gradasi biru terang */
            font-family: Arial, sans-serif;
            color: #000000;
        }

        .container {
            padding-top: 50px;
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9); /* Transparansi untuk efek gradasi */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #1E90FF; /* Warna biru cerah */
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .table {
            background-color: #F0F8FF; /* Biru pucat */
            color: #000000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            margin-bottom: 20px;
        }

        .table thead {
            background-color: #1E90FF; /* Biru cerah */
            color: #FFFFFF;
        }

        .table tbody td {
            color: #000000;
        }

        .btn-primary {
            background-color: #1E90FF; /* Biru cerah */
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1.1rem;
            text-transform: uppercase;
            transition: background-color 0.3s, transform 0.2s;
            color: white;
        }

        .btn-primary:hover {
            background-color: #4682B4; /* Biru lebih gelap saat hover */
            transform: scale(1.05);
        }

        .badge-warning {
            background-color: #FFA500; /* Warna kuning keemasan untuk status 'Menunggu Konfirmasi' */
        }

        .badge-success {
            background-color: #32CD32; /* Warna hijau cerah untuk status 'Diterima' */
        }

        .badge-danger {
            background-color: #FF4500; /* Warna oranye terang untuk status 'Gagal' */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Riwayat Transaksi</h1>
        
        <!-- Tampilkan tabel transaksi -->
        <table class="table">
            <thead>
                <tr>
                    <th>No. Pemesanan</th>
                    <th>Status</th>
                    <th>Total Pembayaran</th>
                    <th>Waktu Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada transaksi</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                            <td>
                                <?php
                                    if ($transaction['status'] === 'Menunggu Konfirmasi') {
                                        echo '<span class="badge badge-warning">Menunggu Konfirmasi</span>';
                                    } elseif ($transaction['status'] === 'Diterima') {
                                        echo '<span class="badge badge-success">Diterima</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">Gagal</span>';
                                    }
                                ?>
                            </td>
                            <td>RP <?php echo number_format($transaction['total'], 2, ',', '.'); ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($transaction['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</body>
</html>
