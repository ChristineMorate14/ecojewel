<?php
session_start();

// Initialize cart if not yet set
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Handle add to cart
if (isset($_GET['add_to_cart'])) {
  $product_id = $_GET['add_to_cart'];

  // Add to session cart
  if (!in_array($product_id, $_SESSION['cart'])) {
    $_SESSION['cart'][] = $product_id;
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Elegant Gems - Jewelry Shop</title>
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

    .hero {
      background: url('assets/images/jewelry-banner.jpg') center/cover no-repeat;
      height: 300px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      text-shadow: 0 2px 4px rgba(0,0,0,0.6);
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      padding: 30px;
    }

    .product {
      background-color: #fff;
      border: 1px solid #eee;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      transition: transform 0.3s;
    }

    .product:hover {
      transform: scale(1.03);
    }

    .product img {
      width: 100%;
      border-radius: 15px 15px 0 0;
    }

    .price {
      font-weight: bold;
      color: #d4af37;
      margin-top: 5px;
    }

    .btn {
      display: inline-block;
      margin: 10px 0 15px;
      padding: 8px 15px;
      background-color: #d4af37;
      color: white;
      border-radius: 8px;
      text-decoration: none;
    }

    footer {
      background-color: #222;
      color: white;
      text-align: center;
      padding: 15px 10px;
      margin-top: 30px;
    }
  </style>
</head>

<body>
  <header>
    <h1>Five Queens Jewelry</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart (<?= count($_SESSION['cart']) ?>)</a>
      <a href="checkout.php">Checkout</a>
      <a href="track_order.php">Track Order</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <section class="hero">
    <h2>Discover Timeless Beauty</h2>
  </section>


  <section class="products">
    <div class='product'>
      <img src='assets/images/products/Diamond/Necklace/Luxury Emerald and Natural Diamond Necklace.png' alt='Diamond Necklace'>
      <h3>Diamond Necklace</h3>
      <p>Luxury Emerald and Natural Diamond Necklace.</p>
      <p class='price'>₱4,999.99</p>
      <a href='index.php?add_to_cart=1' class='btn'>Add to Cart</a>
    </div>

    <div class='product'>
      <img src='assets/images/products/Diamond/Ring/Diamond Initial Ring.png' alt='Diamond Ring'>
      <h3>Diamond Ring</h3>
      <p>Diamond Initial Ring.</p>
      <p class='price'>₱8,999.99</p>
      <a href='index.php?add_to_cart=2' class='btn'>Add to Cart</a>
    </div>

    <div class='product'>
      <img src='assets/images/products/Silver/Bracelet/Silver Crystal and Pearl Ring Bracelet.png' alt='Silver Bracelet'>
      <h3>Silver Bracelet</h3>
      <p>Sterling silver bracelet with elegant design.</p>
      <p class='price'>₱2,499.99</p>
      <a href='index.php?add_to_cart=3' class='btn'>Add to Cart</a>
    </div>

    <div class='product'>
      <img src='assets/images/products/Gold/Earrings/Gold Teardrop Dangle Earrings.png' alt='Gold Earrings'>
      <h3>Gold Earrings</h3>
      <p>Gold Teardrop Dangle Earrings.</p>
      <p class='price'>₱1,599.99</p>
      <a href='index.php?add_to_cart=4' class='btn'>Add to Cart</a>
    </div>

    <div class='product'>
      <img src='assets/images/products/Gold/Necklace/18k Gold with initial.png' alt='Gold Necklace'>
      <h3>Gold Necklace</h3>
      <p>18k Gold with initial.</p>
      <p class='price'>₱3,499.99</p>
      <a href='index.php?add_to_cart=5' class='btn'>Add to Cart</a>
    </div>

     <div class='product'>
      <img src='assets/images/products/Gold/Necklace/18k Gold with initial.png' alt='Gold Necklace'>
      <h3>Gold Necklace</h3>
      <p>18k Gold with initial.</p>
      <p class='price'>₱3,499.99</p>
      <a href='index.php?add_to_cart=5' class='btn'>Add to Cart</a>
    </div>

     <div class='product'>
      <img src='assets/images/products/Gold/Necklace/18k Gold with initial.png' alt='Gold Necklace'>
      <h3>Gold Necklace</h3>
      <p>18k Gold with initial.</p>
      <p class='price'>₱3,499.99</p>
      <a href='index.php?add_to_cart=6' class='btn'>Add to Cart</a>
    </div>

     <div class='product'>
      <img src='assets/images/products/Gold/Necklace/18k Gold with initial.png' alt='Gold Necklace'>
      <h3>Gold Necklace</h3>
      <p>18k Gold with initial.</p>
      <p class='price'>₱3,499.99</p>
      <a href='index.php?add_to_cart=7' class='btn'>Add to Cart</a>
    </div>

  </section>

  <footer>
    <p>&copy; 2025 Elegant Gems Jewelry Shop | All Rights Reserved</p>
  </footer>
</body>
</html>
