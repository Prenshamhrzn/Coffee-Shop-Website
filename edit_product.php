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

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=? WHERE id=?");
    $stmt->bind_param('sdsi', $name, $price, $description, $id);
    $stmt->execute();

    header("Location: product.php");
    exit;
}

$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();
?>

<h1>Edit Product</h1>
<form method="post">
    <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
    <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
    <textarea name="description"><?php echo $product['description']; ?></textarea>
    <button type="submit">Update Product</button>
</form>
