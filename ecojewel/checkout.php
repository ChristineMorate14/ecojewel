<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['selected_items']) || empty($_SESSION['selected_items'])) {
  echo "<script>alert('No items selected for checkout!'); window.location='cart.php';</script>";
  exit();
}

// Get selected product IDs
$selected_ids = implode(',', $_SESSION['selected_items']);
$result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($selected_ids)");

$total_price = 0;
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
  $products[] = $row;
  $total_price += $row['price'];
}

// Handle order submission
if (isset($_POST['place_order'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $contact = mysqli_real_escape_string($conn, $_POST['contact']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);

  // Insert order
  $query = "INSERT INTO orders (customer_name, contact, address, total_price) VALUES ('$name', '$contact', '$address', '$total_price')";
  mysqli_query($conn, $query);
  $order_id = mysqli_insert_id($conn);

  // Insert items
  foreach ($products as $item) {
    $pid = $item['id'];
    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id) VALUES ('$order_id', '$pid')");
  }

  // Clear cart
  unset($_SESSION['cart']);
  unset($_SESSION['selected_items']);

  echo "<script>alert('Order placed successfully! Thank you for shopping with us.'); window.location='index.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - Elegant Gems</title>
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

    .checkout-container {
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

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
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

    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    input, textarea {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }

    button {
      background-color: #d4af37;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      font-weight: 500;
    }

    button:hover {
      background-color: #b8962f;
    }

    footer {
      background-color: #222;
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: 30px;
    }

    .total {
      text-align: right;
      font-weight: bold;
      font-size: 1.1rem;
      margin-bottom: 15px;
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
    </nav>
  </header>

  <div class="checkout-container">
    <h2>Checkout Summary</h2>

    <table>
      <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Price</th>
      </tr>
      <?php foreach ($products as $item): ?>
        <tr>
          <td><img src="<?= $item['image'] ?>"></td>
          <td><?= $item['name'] ?></td>
          <td>₱<?= number_format($item['price'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <p class="total">Total: ₱<?= number_format($total_price, 2) ?></p>

    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="text" name="contact" placeholder="Contact Number" required>
      <textarea name="address" placeholder="Delivery Address" required></textarea>
      <button type="submit" name="place_order">Place Order</button>
    </form>
  </div>

  <footer>
    <p>&copy; 2025 Elegant Gems Jewelry Shop | All Rights Reserved</p>
  </footer>
</body>
</html>
