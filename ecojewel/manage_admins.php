<?php
session_start();
include 'db_connect.php';

// Protect page
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin_login.php");
  exit();
}

$message = "";

// Add new admin
if (isset($_POST['add_admin'])) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $query = "INSERT INTO admins (username, password) VALUES ('$username', '$password')";
  if (mysqli_query($conn, $query)) {
    $message = "✅ Admin added successfully!";
  } else {
    $message = "❌ Error: " . mysqli_error($conn);
  }
}

// Delete admin
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  mysqli_query($conn, "DELETE FROM admins WHERE id = $id");
  $message = "🗑️ Admin deleted successfully.";
}

// Fetch all admins
$admins = mysqli_query($conn, "SELECT * FROM admins ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Admins - Elegant Gems</title>
<style>
  body {
    font-family: Poppins, sans-serif;
    background: #fff8f0;
    margin: 0;
    padding: 0;
  }

  header {
    background-color: #d4af37;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  h1 {
    margin: 0;
  }

  a {
    text-decoration: none;
    color: #d4af37;
  }

  .container {
    padding: 30px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
  }

  th {
    background-color: #f5deb3;
    color: #333;
  }

  tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  .add-form {
    margin-bottom: 30px;
    background: #fff3cd;
    padding: 20px;
    border-radius: 8px;
  }

  input[type="text"], input[type="password"] {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin: 5px;
  }

  button {
    background-color: #d4af37;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
  }

  button:hover {
    background-color: #b8962f;
  }

  .message {
    color: green;
    font-weight: bold;
  }
</style>
</head>
<body>

<header>
  <h1>Manage Admins</h1>
  <div>
    <a href="admin_dashboard.php" style="color:white;">🏠 Dashboard</a> |
    <a href="logout.php" style="color:white;">🚪 Logout</a>
  </div>
</header>

<div class="container">
  <div class="add-form">
    <h3>Add New Admin</h3>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="add_admin">Add Admin</button>
    </form>
  </div>

  <?php if ($message): ?>
    <p class="message"><?= $message ?></p>
  <?php endif; ?>

  <table>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Created At</th>
      <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($admins)): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['username'] ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <?php if ($row['username'] != $_SESSION['admin_username']): ?>
            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this admin?')">🗑️ Delete</a>
          <?php else: ?>
            <em>(You)</em>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>
