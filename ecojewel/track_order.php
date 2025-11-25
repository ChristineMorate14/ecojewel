<?php
include 'db_connect.php';

$order_details = null;
$error = "";

if (isset($_POST['track'])) {
  $input = mysqli_real_escape_string($conn, $_POST['order_info']);

  // Check by order ID or customer name
  $query = "
    SELECT * FROM orders 
    WHERE id = '$input' OR customer_name LIKE '%$input%'
    ORDER BY order_date DESC
    LIMIT 1
  ";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    $order_details = mysqli_fetch_assoc($result);

    // Fetch products for that order
    $order_id = $order_details['id'];
    $items = mysqli_query($conn, "
      SELECT p.name, p.price, p.image
      FROM order_items oi
      JOIN products p ON oi.product_id = p.id
      WHERE oi.order_id = '$order_id'
    ");
  } else {
    $error = "No order found. Please check your name or order ID.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Order - Elegant Gems</title>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #fffdfb;
      color: #333;
    }

    header {
      background-color: #d4af37;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    nav a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .container {
      max-width: 900px;
      margin: 30px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    form {
      text-align: center;
      margin-bottom: 20px;
    }

    input[type="text"] {
      padding: 10px;
      width: 70%;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    button {
      padding: 10px 15px;
      background-color: #d4af37;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      margin-left: 10px;
      cursor: pointer;
    }

    button:hover {
      background-color: #b8962f;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 10px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    img {
      width: 70px;
      border-radius: 8px;
    }

    .total {
      text-align: right;
      font-weight: bold;
      margin-top: 15px;
    }

    .error {
      color: red;
      text-align: center;
    }

    footer {
      background-color: #222;
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: 30px;
    }
  </style>
</head>

<body>
  <header>
    <h1>Elegant Gems</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart</a>
      <a href="checkout.php">Checkout</a>
      <a href="track_order.php">Track Order</a>
    </nav>
  </header>

  <div class="container">
    <h2>Track My Order</h2>

    <form method="POST">
      <input type="text" name="order_info" placeholder="Enter Order ID or Your Name" required>
      <button type="submit" name="track">Track</button>
    </form>

    <?php if ($error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($order_details): ?>
      <h3>Order Details</h3>
      <p><strong>Order ID:</strong> <?= $order_details['id'] ?></p>
      <p><strong>Customer:</strong> <?= htmlspecialchars($order_details['customer_name']) ?></p>
      <p><strong>Contact:</strong> <?= htmlspecialchars($order_details['contact']) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($order_details['address']) ?></p>
      <p><strong>Date:</strong> <?= $order_details['order_date'] ?></p>

      <table>
        <tr>
          <th>Image</th>
          <th>Product</th>
          <th>Price</th>
        </tr>
        <?php while ($item = mysqli_fetch_assoc($items)): ?>
          <tr>
            <td><img src="<?= $item['image'] ?>"></td>
            <td><?= $item['name'] ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>

      <p class="total">Total: ₱<?= number_format($order_details['total_price'], 2) ?></p>
    <?php endif; ?>
  </div>

  <footer>
    <p>&copy; 2025 Elegant Gems Jewelry Shop | All Rights Reserved</p>
  </footer>
</body>
</html>
