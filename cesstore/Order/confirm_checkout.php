<?php
require '../config.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Retrieve cart items for the current user
$sql = "SELECT products.name, products.price, cart.quantity FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get user_id
$user_id = $_SESSION['user_id'];

// Insert the total into the transaksi table
$sql_insert_transaksi = "INSERT INTO transaksi (user_id, total) VALUES (?, ?)";
$stmt_insert_transaksi = $pdo->prepare($sql_insert_transaksi);
$stmt_insert_transaksi->execute([$user_id, $total]);

// Get the last inserted transaksi ID
$transaction_id = $pdo->lastInsertId();

// Clear the cart after checkout
$sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
$stmt_clear_cart = $pdo->prepare($sql_clear_cart);
$stmt_clear_cart->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Checkout</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1e3c72, #2a5298); /* Blue gradient background */
            font-family: Arial, sans-serif;
            color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 800px;
            background: linear-gradient(to bottom, #002f6c, #00508f); /* Dark to lighter blue gradient */
            color: #FFFFFF;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1, h3, p {
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2rem;
            color: #E0F7FA; /* Light cyan */
        }

        p {
            font-size: 1.2rem;
        }

        h3 {
            font-size: 1.5rem;
        }

        .btn-primary {
            background-color: #0288D1; /* Blue */
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1.1rem;
            text-transform: uppercase;
            transition: background-color 0.3s, transform 0.2s;
            color: white;
        }

        .btn-primary:hover {
            background-color: #01579B; /* Darker blue on hover */
            transform: scale(1.05);
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #ddd;
            border-top: 1px solid #bbb;
        }

        .footer p {
            margin: 0;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Checkout Confirmed</h1>
        <p>Your order has been successfully placed.</p>
        <h3>Total Payment: <?php echo number_format($total, 2, ',', '.'); ?> RP</h3>
        <p>Transaction ID: <?php echo $transaction_id; ?></p>
        <a href="../index.php" class="btn btn-primary btn-home">Back to Home</a>
    </div>
</body>

</html>
