<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'admin_panel');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$products = $conn->query('SELECT * FROM products');
$customers = $conn->query('SELECT * FROM customers');
?>

<h1>Welcome, <?php echo $_SESSION['admin']; ?></h1>
<a href="logout.php">Logout</a>

<h2>Products</h2>
<ul>
    <?php while ($product = $products->fetch_assoc()): ?>
        <li><?php echo $product['name']; ?> - $<?php echo $product['price']; ?></li>
    <?php endwhile; ?>
</ul>

<h2>Customers</h2>
<ul>
    <?php while ($customer = $customers->fetch_assoc()): ?>
        <li><?php echo $customer['name']; ?> - <?php echo $customer['email']; ?></li>
    <?php endwhile; ?>
</ul>
