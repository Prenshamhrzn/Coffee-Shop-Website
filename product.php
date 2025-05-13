<?php
echo "<pre>";
print_r($_FILES);
echo "</pre>";

session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'admin_panel');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Create or Update product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $imageName = '';

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    if ($id) {
        // UPDATE
        if ($imageName) {
            $stmt = $conn->prepare('UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?');
            $stmt->bind_param('sdssi', $name, $price, $description, $imageName, $id);
        } else {
            $stmt = $conn->prepare('UPDATE products SET name=?, price=?, description=? WHERE id=?');
            $stmt->bind_param('sdsi', $name, $price, $description, $id);
        }
    } else {
        // INSERT
        $stmt = $conn->prepare('INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sdss', $name, $price, $description, $imageName);
    }

    $stmt->execute();
    header('Location: product.php');
    exit;
}

// DELETE product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: product.php');
    exit;
}

// EDIT product (load into form)
$editProduct = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM products WHERE id = $id");
    $editProduct = $res->fetch_assoc();
}

// Fetch all
$products = $conn->query('SELECT * FROM products');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>
    <style>
        img { max-width: 100px; height: auto; }
        form, ul { margin-bottom: 20px; }
        li { margin-bottom: 15px; }
        .actions a { margin-right: 10px; }
    </style>
</head>
<body>

<h1>Product Management</h1>
<a href="dashboard.php">← Back to Dashboard</a>

<h2><?php echo $editProduct ? 'Edit Product' : 'Add Product'; ?></h2>
<form method="post" enctype="multipart/form-data">
    <?php if ($editProduct): ?>
        <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
    <?php endif; ?>
    <input type="text" name="name" placeholder="Product Name" required value="<?php echo $editProduct['name'] ?? ''; ?>"><br><br>
    <input type="number" step="0.01" name="price" placeholder="Price" required value="<?php echo $editProduct['price'] ?? ''; ?>"><br><br>
    <textarea name="description" placeholder="Description"><?php echo $editProduct['description'] ?? ''; ?></textarea><br><br>
    <input type="file" name="image"><br><br>
    <button type="submit"><?php echo $editProduct ? 'Update' : 'Add'; ?> Product</button>
</form>

<h2>Existing Products</h2>
<ul>
    <?php while ($product = $products->fetch_assoc()): ?>
        <li>
            <strong><?php echo htmlspecialchars($product['name']); ?></strong> - $<?php echo $product['price']; ?><br>
            <?php if (!empty($product['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image"><br>
            <?php endif; ?>
            <?php echo nl2br(htmlspecialchars($product['description'])); ?><br>
            <div class="actions">
                <a href="product.php?edit=<?php echo $product['id']; ?>">✏️ Edit</a>
                <a href="product.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Delete this product?')">🗑️ Delete</a>
            </div>
        </li>
    <?php endwhile; ?>
</ul>

</body>
</html>


