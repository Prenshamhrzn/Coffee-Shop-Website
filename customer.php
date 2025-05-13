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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare('INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $phone);
    $stmt->execute();
    header('Location: customer.php');
    exit;
}

$customers = $conn->query('SELECT * FROM customers');
?>

<h1>Customer Management</h1>
<a href="dashboard.php">Back to Dashboard</a>

<h2>Add Customer</h2>
<form method="post">
    <input type="text" name="name" placeholder="Customer Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone">
    <button type="submit">Add Customer</button>
</form>

<h2>Existing Customers</h2>
<ul>
    <?php while ($customer = $customers->fetch_assoc()): ?>
        <li><?php echo $customer['name']; ?> - <?php echo $customer['email']; ?></li>
    <?php endwhile; ?>
</ul>
