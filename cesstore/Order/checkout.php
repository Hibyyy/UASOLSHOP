<?php
require '../config.php';
session_start();

// Inisialisasi variabel $cart_items dengan array kosong sebagai default
$cart_items = [];

// Pastikan user_id ada di session
if (isset($_SESSION['user_id'])) {
    // Retrieve cart items for the current user
    $sql = "SELECT products.name, products.price, cart.quantity FROM cart 
            JOIN products ON cart.product_id = products.id 
            WHERE cart.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
} else {
    echo "User not logged in.";
    exit(); // Keluar dari script jika pengguna tidak login
}

// Proses checkout ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['payment_method']) && !empty($_POST['payment_method']) && isset($_POST['payment_reference']) && !empty($_POST['payment_reference'])) {
        // Menyimpan transaksi checkout ke database untuk menunggu konfirmasi admin
        $payment_method = $_POST['payment_method'];
        $payment_reference = $_POST['payment_reference']; // ID transaksi atau bukti pembayaran

        // Simpan transaksi ke database (status 'Menunggu Konfirmasi')
        $sql = "INSERT INTO transaksi (user_id, payment_method, payment_reference, status) 
                VALUES (?, ?, ?, 'Menunggu Konfirmasi')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $payment_method, $payment_reference]);

        // Redirect ke halaman konfirmasi setelah pembayaran
        header('Location: confirm_checkout.php'); // Arahkan ke halaman konfirmasi
        exit();
    } else {
        $error_message = "Please select a payment method and enter payment reference.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1E90FF, #00BFFF); /* Gradasi biru */
            font-family: Arial, sans-serif;
            color: #FFFFFF;
        }

        .container {
            padding-top: 50px;
            max-width: 800px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.7); /* Transparan gelap untuk kontras */
            color: #FFFFFF; /* Warna teks putih */
            border-radius: 10px;
            padding: 20px;
        }

        .table {
            background-color: #FFFFFF; /* Putih untuk tabel */
            color: #000000; /* Teks hitam */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            margin-bottom: 20px;
        }

        .table thead {
            background-color: #1E90FF; /* Biru tua untuk header tabel */
            color: #FFFFFF; /* Teks putih */
        }

        .table tbody td {
            color: #000000; /* Teks hitam */
        }

        .btn-confirm {
            margin-top: 20px;
            background-color: #32CD32; /* Hijau untuk tombol konfirmasi */
            border-color: #228B22;
            color: #FFFFFF; /* Teks putih */
        }

        .btn-confirm:hover {
            background-color: #228B22; /* Hijau lebih gelap */
            border-color: #1C5D1F;
        }

        .btn-secondary {
            margin-top: 20px;
            background-color: #808080; /* Abu-abu untuk tombol sekunder */
            border-color: #696969;
            color: #FFFFFF; /* Teks putih */
        }

        .btn-secondary:hover {
            background-color: #696969;
            border-color: #4E4E4E;
        }

        .payment-info {
            margin-top: 20px;
            font-size: 1.2em;
            color: #000;
        }

        .payment-reference {
            display: none; /* Sembunyikan Payment Reference awalnya */
        }
    </style>

    <!-- JavaScript untuk otomatis mengisi nomor e-wallet berdasarkan pilihan metode pembayaran -->
    <script>
        function updatePaymentReference() {
            var paymentMethod = document.getElementById("payment_method").value;
            var paymentReference = document.getElementById("payment_reference");
            var paymentReferenceWrapper = document.getElementById("payment_reference_wrapper");

            if (paymentMethod === "dana" || paymentMethod === "ovo" || paymentMethod === "gopay") {
                paymentReference.value = "085774407831";
                paymentReferenceWrapper.style.display = "block"; // Tampilkan Payment Reference
            } else {
                paymentReference.value = "";
                paymentReferenceWrapper.style.display = "none"; // Sembunyikan Payment Reference
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Checkout</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>RP <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>RP <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No items in cart</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <h3>Total: RP <?php
        $total = 0;
        foreach ($cart_items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        echo number_format($total, 2, ',', '.'); ?></h3>

        <!-- Form untuk memilih metode pembayaran dan memasukkan ID transaksi -->
        <form method="POST">
            <div class="form-group">
                <label for="payment_method">Select Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required onchange="updatePaymentReference()">
                    <option value="">--Select Payment Method--</option>
                    <option value="dana">DANA</option>
                    <option value="ovo">OVO</option>
                    <option value="gopay">GoPay</option>
                </select>
            </div>

            <!-- Payment Reference hanya muncul jika metode pembayaran dipilih -->
            <div class="form-group payment-reference" id="payment_reference_wrapper">
                <label for="payment_reference"></label>
                <input type="text" class="form-control" id="payment_reference" name="payment_reference" required readonly>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-confirm">Confirm Purchase</button>
        </form>

        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
    </div>
</body>
</html>
