<?php
require '../config.php'; // Pastikan jalur ini benar
session_start();

$notification = ''; // Variabel untuk menyimpan notifikasi

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $cart_id = $_GET['id']; // Get cart.id instead of product_id
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?"; // Use cart.id for deletion
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cart_id, $user_id]);

    // Check if deletion was successful
    $deleted = $stmt->rowCount() > 0;

    if ($deleted) {
        $notification = "Product deleted from cart."; // Set notification
    } else {
        $notification = "Failed to delete product from cart.";
    }
}

// Handle add action
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $product_id, 1]);

    $notification = "Product added to cart."; // Set notification
}

// Handle update action
if (isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['id']) && isset($_POST['quantity'])) {
    $cart_id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$quantity, $cart_id, $user_id]);

    $notification = "Cart updated."; // Set notification
}

// Retrieve cart items for the current user
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT cart.id, products.name, products.price, cart.quantity FROM cart 
            JOIN products ON cart.product_id = products.id 
            WHERE cart.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
} else {
    $cart_items = []; // If user_id is not set in session, initialize an empty array
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to bottom, #1E90FF, #00BFFF); /* Gradient blue background */
            font-family: Arial, sans-serif;
            color: #FFFFFF;
        }

        .container {
            padding-top: 50px;
            max-width: 800px;
            margin: 0 auto;
            background: linear-gradient(to right, #87CEEB, #4682B4); /* Gradient blue for container */
            color: #FFFFFF;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table {
            background-color: #F0F8FF; /* Light blue for table */
            color: #000000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            margin-bottom: 20px;
        }

        .table thead {
            background-color: #4682B4; /* Dark blue header */
            color: #FFFFFF;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: center;
        }

        .btn-checkout {
            background-color: #1E90FF; /* Blue color for checkout button */
            border-color: #1E90FF;
        }

        .btn-checkout:hover {
            background-color: #00BFFF;
            border-color: #00BFFF;
        }

        .btn-decrease {
            background-color: #DC143C; /* Red for decrease button */
            border-color: #DC143C;
        }

        .btn-decrease:hover {
            background-color: #C71585;
            border-color: #C71585;
        }

        .btn-increase {
            background-color: #32CD32; /* Lime green for increase button */
            border-color: #32CD32;
        }

        .btn-increase:hover {
            background-color: #228B22;
            border-color: #228B22;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
        }

        .btn-icon {
            font-size: 1.25rem; /* Larger icon size */
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #32CD32; /* Green background for success messages */
            color: #FFFFFF;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none; /* Initially hidden */
        }

        .notification.error {
            background-color: #DC143C; /* Red background for error messages */
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Cart</h1>

        <!-- Notification -->
        <?php if ($notification): ?>
            <div id="notification" class="notification <?php echo strpos($notification, 'Failed') !== false ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($notification); ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>RP <?php echo number_format($item['price'], 0, ',', '.'); ?></td> <!-- RP in front -->
                        <td>
                            <form action="cart.php" method="POST" class="d-inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>"
                                    class="btn btn-decrease btn-sm">-</button>
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>"
                                    class="quantity-input" min="1">
                                <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>"
                                    class="btn btn-increase btn-sm">+</button>
                            </form>
                        </td>
                        <td>RP <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td> <!-- RP in front -->
                        <td>
                            <a href="cart.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    
            <!-- Checkout Button -->
            <div class="text-center">
                <a href="checkout.php" class="btn btn-checkout btn-lg">Proceed to Checkout</a>
            </div>
        </div>
    
        <script>
            // Show notification if there's a message
            document.addEventListener('DOMContentLoaded', function () {
                const notification = document.getElementById('notification');
                if (notification) {
                    notification.style.display = 'block';
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 3000);
                }
            });
        </script>
    </body>
    </html>
    