<?php
session_start();
include 'db_connect.php';

// Initialize cart session if not yet created
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Remove selected item
if (isset($_GET['remove'])) {
  $remove_id = $_GET['remove'];
  if (($key = array_search($remove_id, $_SESSION['cart'])) !== false) {
    unset($_SESSION['cart'][$key]);
  }
  header("Location: cart.php");
  exit();
}

// Handle checkout selection
if (isset($_POST['checkout_selected'])) {
  $_SESSION['selected_items'] = $_POST['selected'] ?? [];
  header("Location: checkout.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - Elegant Gems</title>
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

    .cart-container {
      max-width: 900px;
      margin: 30px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f5f5f5;
    }

    img {
      width: 70px;
      border-radius: 8px;
    }

    .btn {
      padding: 8px 15px;
      border-radius: 8px;
      text-decoration: none;
      color: white;
      background-color: #d4af37;
      font-weight: 500;
    }

    .btn:hover {
      background-color: #b8962f;
    }

    .remove {
      color: red;
      font-weight: bold;
      text-decoration: none;
    }

    .remove:hover {
      text-decoration: underline;
    }

    .total {
      text-align: right;
      font-size: 1.1rem;
      font-weight: bold;
      margin-bottom: 15px;
    }

    footer {
      background-color: #222;
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: 30px;
    }

    .empty {
      text-align: center;
      padding: 50px;
      color: #777;
    }
  </style>
</head>

<body>
  <header>
    <h1>Elegant Gems</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a>
      <a href="checkout.php">Checkout</a>
    </nav>
  </header>

  <div class="cart-container">
    <h2>Your Shopping Cart</h2>

    <?php
    if (empty($_SESSION['cart'])) {
      echo "<p class='empty'>Your cart is empty.</p>";
    } else {
      $ids = implode(',', $_SESSION['cart']);
      $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");

      $total = 0;
      $count = 0;

      echo "<form method='POST'>";
      echo "<table>";
      echo "<tr><th>Select</th><th>Image</th><th>Product</th><th>Price</th><th>Action</th></tr>";

      while ($row = mysqli_fetch_assoc($result)) {
        echo "
          <tr>
            <td><input type='checkbox' name='selected[]' value='{$row['id']}'></td>
            <td><img src='{$row['image']}'></td>
            <td>{$row['name']}</td>
            <td>₱" . number_format($row['price'], 2) . "</td>
            <td><a href='cart.php?remove={$row['id']}' class='remove'>Remove</a></td>
          </tr>
        ";
        $total += $row['price'];
        $count++;
      }

      echo "</table>";
      echo "<p class='total'>Total Items: $count | Total: ₱" . number_format($total, 2) . "</p>";
      echo "<button type='submit' name='checkout_selected' class='btn'>Proceed to Checkout</button>";
      echo "</form>";
    }
    ?>
  </div>

  <footer>
    <p>&copy; 2025 Elegant Gems Jewelry Shop | All Rights Reserved</p>
  </footer>
</body>
</html>
